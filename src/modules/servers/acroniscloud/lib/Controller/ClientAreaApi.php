<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace WHMCS\Module\Server\AcronisCloud\Controller;

use Acronis\Cloud\Client\Model\Usages\UsageOutputOfferingItem;
use AcronisCloud\Model\TemplateOfferingItem;
use Acronis\Cloud\Client\Model\Users\User;
use Acronis\Cloud\Client\Model\Users\UserPut;
use AcronisCloud\Model\WHMCS\Service;
use AcronisCloud\Service\Dispatcher\ActionInterface;
use AcronisCloud\Service\Dispatcher\RequestException;
use AcronisCloud\Service\Dispatcher\RequestInterface;
use AcronisCloud\Service\Dispatcher\Response\DataResponse;
use AcronisCloud\Service\Dispatcher\Response\JsonErrorResponse;
use AcronisCloud\Service\Dispatcher\Response\JsonResponse;
use AcronisCloud\Service\Dispatcher\Response\StatusCodeInterface;
use AcronisCloud\Service\Logger\LoggerAwareTrait;
use AcronisCloud\Util\Str;
use AcronisCloud\Util\UomConverter;
use Exception;
use WHMCS\Module\Addon\AcronisCloud\Controller\TemplateHandler;
use WHMCS\Module\Server\AcronisCloud\Product\CustomFields;

class ClientAreaApi extends TemplateHandler
{
    use LoggerAwareTrait;

    const PARAM_SERVICE_ID = 'id';

    const PROPERTY_APPLICATION_TYPE = 'application_type';
    const PROPERTY_OFFERING_ITEMS = 'offering_items';
    const PROPERTY_USAGE_VALUE = 'usage_value';

    /** @var RequestInterface */
    private $request;

    /**
     * @return DataResponse
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
    public function getDetails(RequestInterface $request)
    {
        $this->setRequest($request);
        $service = $this->getClientService();

        $this->setServerId($service->getServerId());
        $cloudUser = $this->getCloudUser();

        return $this->getClientUserDetails($cloudUser);
    }

    /**
     * @param RequestInterface $request
     * @return array
     * @throws \AcronisCloud\CloudApi\CloudServerException
     * @throws RequestException
     */
    public function getUsages(RequestInterface $request)
    {
        $this->setRequest($request);
        $service = $this->getClientService();
        $this->setServerId($service->getServerId());
        $cloudApi = $this->getCloudApi();
        $tenantId = $this->getCustomFields()->getTenantId();
        if (!$tenantId) {
            throw new RequestException(
                'Tenant Id is missing from the product\'s custom fields. Please, activate the purchase.',
                [
                    'product_id' => $service->product->getId(),
                    'service_id' => $this->getServiceId(),
                ],
                StatusCodeInterface::HTTP_NOT_FOUND
            );
        }

        $meta = $this->getMetaInfo();
        $usages = $cloudApi->getTenantUsages($tenantId);
        uasort(
            $usages,
            function ($usage1, $usage2) {
                return $this->getUsageSortOrder($usage1, $usage2);
            }
        );

        $usageStats = [];
        foreach ($usages as $usage) {
            if (!$usage->hasOfferingItem()) {
                continue;
            }

            // check we have info on the offering item
            /** @var UsageOutputOfferingItem $offeringItem */
            $cloudOfferingItem = $usage->getOfferingItem();
            $oiMeta = $meta->getOfferingItemMeta($usage->getName());
            if (
                !$oiMeta
                || !$cloudOfferingItem->getStatus()
                || $oiMeta->getMeasurementUnit() === UomConverter::FEATURE
            ) {
                // offering item isn't known or is not active in cloud
                // or has no measurement
                continue;
            }

            $appType = $oiMeta->getApplicationType();

            $quota = $cloudOfferingItem->hasQuota() ? $cloudOfferingItem->getQuota() : null;
            $quotaValue = $quota && $quota->hasValue() ? $quota->getValue() : null;
            $usageValue = $usage->getValue();
            $measurementUnit = $usage->getMeasurementUnit();
            $usageStats[$appType][] = [
                TemplateOfferingItem::COLUMN_NAME => $oiMeta->getOfferingItemName(),
                TemplateOfferingItem::COLUMN_MEASUREMENT_UNIT => $measurementUnit,
                static::PROPERTY_USAGE_VALUE => $usageValue,
                TemplateOfferingItem::COLUMN_QUOTA_VALUE => $quotaValue,
            ];
        }

        $groupedUsage = [];
        foreach ($usageStats as $appType => $offeringItems) {
            $groupedUsage[] = [
                static::PROPERTY_APPLICATION_TYPE => $appType,
                static::PROPERTY_OFFERING_ITEMS => $offeringItems,
            ];
        }

        return $groupedUsage;
    }

    /**
     * @param RequestInterface $request
     * @return array
     * @throws \AcronisCloud\CloudApi\CloudServerException
     * @throws \Exception
     */
    public function updateDetails(RequestInterface $request)
    {
        $this->setRequest($request);
        $service = $this->getClientService();
        $version = $this->getRequestParameters()->getVersion();

        $this->setServerId($service->getServerId());
        $cloudApi = $this->getCloudApi();
        $cloudUser = $this->getCloudUser();

        $userNotifications = $this->getRequestParameters()
            ->getNotifications()
            ->toCloudFormat();

        $userBody = new UserPut();
        $userBody->setNotifications($userNotifications);
        $userBody->setVersion($version);
        $updatedUser = $cloudApi->updateUser($cloudUser->getId(), $userBody);

        return $this->getClientUserDetails($updatedUser);
    }

    /**
     * @param User $cloudUser
     * @return array
     * @throws \AcronisCloud\CloudApi\CloudServerException
     */
    private function getClientUserDetails(User $cloudUser)
    {
        $cloudApi = $this->getCloudApi();
        $tenant = $cloudApi->getTenant($cloudUser->getTenantId());
        $tenantKind = $tenant->getKind();

        $contact = $cloudUser->hasContact() ? $cloudUser->getContact() : null;
        $userNotifications = $cloudUser->hasNotifications()
            ? new NotificationsManager($cloudUser->getNotifications())
            : null;

        return [
            'login' => $cloudUser->getLogin(),
            'email' => $contact && $contact->hasEmail() ? $contact->getEmail() : '',
            'tenant_kind' => $tenantKind,
            'notifications' => $userNotifications ? $userNotifications->toClientAreaFormat() : [],
            'version' => $cloudUser->getVersion(),
        ];
    }

    /**
     * @param RequestInterface $request
     * @return array
     */
    public function getSubscription(RequestInterface $request)
    {
        $this->setRequest($request);
        // make sure the client didn't access another service
        $service = $this->getClientService();
        $product = $service->product;
        $currencyId = $this->getClient()->currency;
        $currency = $this->getRepository()
            ->getCurrencyRepository()
            ->getCurrency($currencyId);

        return [
            'name' => $product ? $product->getName() : '',
            'status' => strtolower($service->getStatus()),
            'registration_date' => $service->getRegistrationDate(),
            'due_date' => $service->getNextDueDate(),
            'billing_cycle' => $service->getBillingCycle(),
            'billing_amount' => $service->getAmount(),
            'billing_currency' => $currency->getCode(),
            'payment_method' => $service->getPaymentMethodName(),
        ];
    }

    /**
     * @param RequestInterface $request
     * @return DataResponse
     */
    public function singleSignOn(RequestInterface $request)
    {
        try {
            $this->setRequest($request);
            $service = $this->getClientService();
            $this->setServerId($service->getServerId());

            return new DataResponse([
                'success' => true,
                'redirectTo' => $this->getCloudApi()->getUrl(),
            ]);
        } catch (Exception $e) {
            $this->getLogger()->error(
                'Single sign on error: {0}, {1}',
                [$e->getMessage(), $e->getTraceAsString()]
            );

            return new DataResponse([
                'errorMsg' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param RequestInterface $request
     */
    protected function setRequest(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * @return RequestInterface
     */
    protected function getRequest()
    {
        return $this->request;
    }

    /**
     * @return int
     */
    protected function getServiceId()
    {
        return (int)$this->getRequest()
            ->getQueryParameter(static::PARAM_SERVICE_ID);
    }

    /**
     * @return ClientRequest
     */
    private function getRequestParameters()
    {
        return $this->memoize(function () {
            return new ClientRequest($this->getRequest());
        });
    }

    /**
     * @return mixed
     */
    protected function getClient()
    {
        return $this->getWhmcsClientArea()
            ->getClient();
    }

    /**
     * @return User
     * @throws RequestException
     * @throws \Exception
     * @throws \AcronisCloud\CloudApi\CloudServerException
     */
    protected function getCloudUser()
    {
        $userId = $this->getCustomFields()->getUserId();
        if (!$userId) {
            throw new RequestException(
                'User Id is missing from the product\'s custom fields. Please, contact administrator to activate the purchase.',
                [
                    'service_id' => $this->getRequestParameters()->getServiceId(),
                ],
                StatusCodeInterface::HTTP_NOT_FOUND
            );
        }
        $cloudUser = $this->getCloudApi()->getUser($userId);
        if ($cloudUser->getTenantId() !== $this->getCustomFields()->getTenantId()) {
            throw new RequestException(
                'Cloud user tenant does not match tenant id set in purchase.',
                [
                    'service_id' => $this->getRequestParameters()->getServiceId(),
                ],
                StatusCodeInterface::HTTP_NOT_FOUND
            );
        }

        return $cloudUser;
    }

    /**
     * @return Service
     */
    protected function getClientService()
    {
        return $this->memoize(function () {
            $clientId = $this->getClient()->getAttribute('id');
            $serviceId = $this->getRequestParameters()->getServiceId();
            $service = $this->getRepository()
                ->getServiceRepository()
                // make sure the client didn't access another client's service
                ->getClientServiceWithServersById($clientId, $serviceId);
            if (!$service) {
                $this->getLogger()->error(Str::format(
                    'No service with service Id %s found for client %s',
                    $clientId, $serviceId
                ));
                throw new \Exception('Client has no service with this id');
            }

            return $service;
        });
    }

    protected function getUsageSortOrder($usage1, $usage2)
    {
        $meta = $this->getMetaInfo();
        $oiMeta1 = $meta->getOfferingItemMeta($usage1->getName());
        $oiMeta2 = $meta->getOfferingItemMeta($usage2->getName());
        // put missing at the start
        if (!$oiMeta1) {
            return -1;
        } elseif (!$oiMeta2) {
            return 1;
        }
        $appMeta1 = $meta->getApplicationMeta($oiMeta1->getApplicationType());
        $appMeta2 = $meta->getApplicationMeta($oiMeta2->getApplicationType());

        if ($appMeta1->getSortPriority() !== $appMeta2->getSortPriority()) {
            return $appMeta1->getSortPriority() - $appMeta2->getSortPriority();
        }

        return $oiMeta1->getSortPriority() - $oiMeta2->getSortPriority();
    }

    /**
     * @return CustomFields
     */
    protected function getCustomFields()
    {
        return $this->memoize(function () {
            $service = $this->getClientService();

            return new CustomFields($service->getProductId(), $service->getId());
        });
    }

    /**
     * @return \WHMCS\ClientArea
     */
    protected function getWhmcsClientArea()
    {
        global $ca;

        return $ca;
    }
}