<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace WHMCS\Module\Addon\AcronisCloud\Controller;

use Acronis\Cloud\Client\Model\Infra\Infra;
use Acronis\Cloud\Client\Model\OfferingItems\OfferingItemOutput;
use AcronisCloud\CloudApi\Api;
use AcronisCloud\CloudApi\ApiInterface;
use AcronisCloud\Model\Template;
use AcronisCloud\Service\Dispatcher\ActionInterface;
use AcronisCloud\Service\Dispatcher\RequestException;
use AcronisCloud\Service\Dispatcher\RequestInterface;
use AcronisCloud\Service\Dispatcher\Response\ErrorCodeInterface;
use AcronisCloud\Service\Dispatcher\Response\HttpResponse;
use AcronisCloud\Service\Dispatcher\Response\JsonErrorResponse;
use AcronisCloud\Service\Dispatcher\Response\JsonResponse;
use AcronisCloud\Service\Logger\LoggerAwareTrait;
use AcronisCloud\Service\MetaInfo\ApplicationMeta;
use AcronisCloud\Service\MetaInfo\OfferingItemMeta;
use AcronisCloud\Util\Arr;
use AcronisCloud\Util\WHMCS\ConfigurableOptionHelper as Option;
use Exception;
use Illuminate\Database\Eloquent\Model;

class ServiceTemplate extends TemplateHandler
{
    use LoggerAwareTrait;

    /**
     * Properties used by frontend to correctly group offering items when editing templates.
     */
    const PROPERTY_TENANT_KINDS = 'tenant_kinds';
    const PROPERTY_EDITION = 'edition';
    const PROPERTY_LOCATION_ID = 'location_id';

    /**
     * @return JsonResponse
     */
    public function getResponseStrategy()
    {
        return new JsonResponse();
    }

    /**
     * @inheritdoc
     */
    public function handleException(
        Exception $e,
        ActionInterface $action,
        RequestInterface $request
    )
    {
        return new JsonErrorResponse($e);
    }

    /**
     * @param RequestInterface $request
     * @return array
     * @throws RequestException
     * @throws \AcronisCloud\CloudApi\CloudServerException
     */
    public function create(RequestInterface $request)
    {
        $data = $this->validateData($request->getBodyParameters());
        $id = $this->repository->create($data);

        return [
            'id' => $id,
        ];
    }

    /**
     * @param RequestInterface $request
     * @return array
     * @throws RequestException
     * @throws \AcronisCloud\CloudApi\CloudServerException
     */
    public function update(RequestInterface $request)
    {
        $id = $this->getTemplateId($request);
        $data = $this->validateData($request->getBodyParameters());

        return $this->repository->update($data, $id);
    }

    /**
     * @param RequestInterface $request
     * @return array
     * @throws Exception
     * @throws RequestException
     */
    public function delete(RequestInterface $request)
    {
        $id = $this->getTemplateId($request);
        $this->checkDeletionAllowed($id);
        $result = $this->repository->delete($id);

        return ['success' => $result];
    }

    /**
     * @param RequestInterface $request
     * @return array
     * @throws Exception
     * @throws RequestException
     */
    public function getTemplate(RequestInterface $request)
    {
        $id = $this->getTemplateId($request);
        $template = $this->repository->find($id);

        return $template ? $template->toArray() : null;
    }

    /**
     * @return Model[]
     */
    public function listTemplates()
    {
        $templates = $this->repository->all();

        return $templates->toArray();
    }

    /**
     * @return array
     */
    public function listServers()
    {
        $servers = $this->getRepository()
            ->getAcronisServerRepository()
            ->all();

        $serversData = [];
        foreach ($servers as $server) {
            $serversData[] = [
                'id' => $server->getId(),
                'name' => $server->getName(),
                'hostname' => $server->getHostname(),
                'status' => $server->getStatus(),
            ];
        }

        return $serversData;
    }

    /**
     * ?action=get_applications
     * @param RequestInterface $request
     * @return array
     * @throws Exception
     * @throws RequestException
     */
    public function getServerApplications(RequestInterface $request)
    {
        $this->initServerId($request);
        $cloudApi = $this->getCloudApi();
        $applications = $cloudApi->getRootTenantApplications();

        $applicationSorted = [];
        foreach ($applications as $application) {
            $type = $application->getType();
            $applicationMeta = $this->getMetaInfo()->getApplicationMeta($type);
            if (!$applicationMeta || !($tenantKinds = $applicationMeta->getTenantKinds())) {
                // skip applications that are unknown
                continue;
            }
            $editions = $this->getTenantApplicationEditions($cloudApi->getRootTenantId(), $application->getId());
            $applicationSorted[$applicationMeta->getSortPriority()] = [
                ApplicationMeta::PROPERTY_TYPE => $type,
                ApplicationMeta::PROPERTY_EDITIONS => $editions,
                ApplicationMeta::PROPERTY_TENANT_KINDS => $tenantKinds,
            ];
        }

        ksort($applicationSorted);

        return array_values($applicationSorted);
    }

    /**
     * @param RequestInterface $request
     * @return array
     * @throws Exception
     * @throws RequestException
     */
    public function getServerOfferingItems(RequestInterface $request)
    {
        $response = [];
        $this->initServerId($request);
        $metaInfo = $this->getMetaInfo();
        $offeringItems = $this->getSortedRootOfferingItems();
        foreach ($offeringItems as $offeringItem) {
            $tenantKinds = $this->getRootOfferingItemTenantKinds($offeringItem);
            if (empty($tenantKinds)) {
                continue;
            }

            $offeringItemName = $offeringItem->getName();
            $offeringItemMeta = $metaInfo->getOfferingItemMeta($offeringItemName);
            $infraId = $offeringItem->hasInfraId() ? $offeringItem->getInfraId() : null;

            $response[] = [
                Option::NAME => $offeringItemName,
                OfferingItemMeta::PROPERTY_APPLICATION_TYPE => $offeringItemMeta->getApplicationType(),
                static::PROPERTY_EDITION => $offeringItem->hasEdition() ? $offeringItem->getEdition() : null,
                OfferingItemMeta::PROPERTY_RESOURCE_TYPE => $offeringItemMeta->getResourceType(),
                static::PROPERTY_TENANT_KINDS => $tenantKinds,
                Option::INFRA_ID => $infraId,
                static::PROPERTY_LOCATION_ID => $this->getLocationId($infraId),
                OfferingItemMeta::PROPERTY_CAPABILITY => $offeringItemMeta->getCapability(),
                OfferingItemMeta::PROPERTY_MEASUREMENT_UNIT => $offeringItemMeta->getMeasurementUnit(),
                OfferingItemMeta::PROPERTY_CHILD_OFFERING_ITEMS => $offeringItemMeta->getChildOfferingItems(),
            ];
        }

        return $response;
    }

    /**
     * @param RequestInterface $request
     * @return array
     * @throws Exception
     * @throws RequestException
     */
    public function getLocations(RequestInterface $request)
    {
        $this->initServerId($request);
        $cloudApi = $this->getCloudApi();
        $infras = $cloudApi->getRootTenantInfras();

        $groupedInfras = [];
        foreach ($infras as $infra) {
            $groupedInfras[$infra->getLocationId()][] = [
                'infra_id' => $infra->getId(),
                'name' => $infra->getName(),
                'capabilities' => $infra->getCapabilities(),
            ];
        }

        $locations = $cloudApi->getRootTenantLocations();

        return array_map(
            function ($location) use ($groupedInfras) {
                return [
                    'location_id' => $location->getId(),
                    'name' => $location->getName(),
                    'infras' => Arr::get($groupedInfras, $location->getId(), []),
                ];
            },
            $locations
        );
    }

    private function getSortedRootOfferingItems()
    {
        $rootOfferingItems = $this->getCloudApi()->getRootTenantOfferingItems();
        $metaInfo = $this->getMetaInfo();

        $offeringItems = array_filter($rootOfferingItems, function ($oi) use ($metaInfo) {
            // skip unknown and inactive offering items
            return $oi->getStatus() !== ApiInterface::OFFERING_ITEM_STATUS_INACTIVE
                && $metaInfo->hasOfferingItemMeta($oi->getName());
        });

        uasort($offeringItems, function ($oi1, $oi2) use ($metaInfo) {
            $meta1 = $metaInfo->getOfferingItemMeta($oi1->getName());
            $meta2 = $metaInfo->getOfferingItemMeta($oi2->getName());

            return $meta1->getSortPriority() - $meta2->getSortPriority();
        });

        return $offeringItems;
    }

    private function getRootOfferingItemTenantKinds($offeringItem)
    {
        $tenantKindsMap = $this->memoize(function () {
            $mapping = [];
            $cloudApi = $this->getCloudApi();
            $rootTenantId = $cloudApi->getRootTenantId();
            $tenantKinds = [
                ApiInterface::TENANT_KIND_CUSTOMER,
                ApiInterface::TENANT_KIND_PARTNER,
            ];
            foreach ($tenantKinds as $tenantKind) {
                $offeringItems = $cloudApi->getOfferingItemsAvailableForChild($rootTenantId, $tenantKind);
                foreach ($offeringItems as $offeringItem) {
                    $hash = Api::getOfferingItemHash($offeringItem);
                    $mapping[$hash][] = $tenantKind;
                }
            }

            return $mapping;
        });
        $oiHash = Api::getOfferingItemHash($offeringItem);

        return Arr::get($tenantKindsMap, $oiHash, []);
    }

    private function getLocationId($infraId)
    {
        if (!$infraId) {
            return null;
        }

        $infra = $this->getInfraById($infraId);

        return $infra && $infra->hasLocationId()
            ? $infra->getLocationId()
            : null;
    }

    /**
     * @param $infraId
     * @return Infra[]
     */
    private function getInfraById($infraId)
    {
        $infraMap = $this->memoize(function () {
            return Arr::map(
                $this->getCloudApi()->getRootTenantInfras(),
                function ($infra) { return $infra->getId(); },
                function ($infra) { return $infra; }
            );
        });

        return Arr::get($infraMap, $infraId);
    }

    private function getTenantApplicationEditions($tenantId, $applicationId)
    {
        return $this->memoize(function () use ($tenantId, $applicationId) {
            $offeringItems = $this->getCloudApi()->getTenantApplicationOfferingItems($tenantId, $applicationId);
            $editions = [];
            foreach($offeringItems as $oi) {
                $oiMeta = $this->getMetaInfo()->getOfferingItemMeta($oi->getName());
                if (!$oiMeta) {
                    continue;
                }
                $editionName = $oiMeta->getEditionName();
                if (!$editionName) {
                    continue;
                }
                $editionMeta = $this->getMetaInfo()->getEditionMeta($editionName);
                if (!$editionMeta) {
                    continue;
                }
                $editions[$editionMeta->getSortPriority()] = $editionName;
            }
            ksort($editions);
            return array_values($editions);
        }, implode(':', [$tenantId, $applicationId]));
    }

    /**
     * @param RequestInterface $request
     * @return mixed
     * @throws RequestException
     */
    private function getTemplateId(RequestInterface $request)
    {
        $templateId = $request->getQueryParameter('template_id');
        $id = filter_var($templateId, FILTER_VALIDATE_INT);
        if (!$id || $id <= 0) {
            $errorCode = $templateId ? HttpResponse::HTTP_UNPROCESSABLE_ENTITY : HttpResponse::HTTP_BAD_REQUEST;
            throw new RequestException(
                'The id field is required and must be a positive number.',
                ['request_params' => ['template_id' => $templateId]],
                $errorCode
            );
        }

        return $id;
    }

    private function initServerId(RequestInterface $request)
    {
        $serverId = $request->getQueryParameter(static::PARAM_SERVER_ID);
        if (!$serverId) {
            throw new RequestException(
                'Server ID must be present.',
                [
                    'request_params' => [static::PARAM_SERVER_ID => $serverId],
                    'error_code' => ErrorCodeInterface::ERROR_SERVICE_TEMPLATE_NO_SERVER_ID
                ],
                HttpResponse::HTTP_BAD_REQUEST
            );
        }
        $this->setServerId($serverId);
    }

    private function checkDeletionAllowed($templateId)
    {
        /** @var Template $template */
        $template = $this->repository->find($templateId);
        if (!$template) {
            throw new RequestException(
                'No template was found with the provided id.',
                [
                    'request_params' => ['template_id' => $templateId],
                    'error_code' => ErrorCodeInterface::ERROR_SERVICE_TEMPLATE_NOT_FOUND
                ],
                HttpResponse::HTTP_BAD_REQUEST
            );
        }

        $products = $template->products()->get()->all();
        if ($products) {
            $productNames = array_map(function ($p) { return $p->getName(); }, $products);
            throw new RequestException(
                'Cannot delete template because it is still used by products.',
                ['products' => $productNames, 'error_code' => ErrorCodeInterface::ERROR_SERVICE_TEMPLATE_IS_USED],
                HttpResponse::HTTP_BAD_REQUEST
            );
        }
    }
}
