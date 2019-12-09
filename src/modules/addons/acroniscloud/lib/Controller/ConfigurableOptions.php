<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace WHMCS\Module\Addon\AcronisCloud\Controller;

use Acronis\Cloud\Client\Model\Applications\Application;
use Acronis\Cloud\Client\Model\Infra\Infra;
use Acronis\Cloud\Client\Model\Locations\Location;
use Acronis\Cloud\Client\Model\OfferingItems\OfferingItemOutput;
use Acronis\Cloud\Client\Model\Tenants\OfferingItem;
use AcronisCloud\CloudApi\ApiInterface;
use AcronisCloud\Localization\GetTextTrait;
use AcronisCloud\Model\WHMCS\ProductConfigOption;
use AcronisCloud\Model\WHMCS\ProductConfigSubOption;
use AcronisCloud\Repository\Validation\ConfigurableOptionsValidator;
use AcronisCloud\Service\Dispatcher\ActionInterface;
use AcronisCloud\Service\Dispatcher\RequestException;
use AcronisCloud\Service\Dispatcher\RequestInterface;
use AcronisCloud\Service\Dispatcher\Response\JsonErrorResponse;
use AcronisCloud\Service\Dispatcher\Response\JsonResponse;
use AcronisCloud\Service\Dispatcher\Response\StatusCodeInterface;
use AcronisCloud\Service\Logger\LoggerAwareTrait;
use AcronisCloud\Util\Arr;
use AcronisCloud\Util\UomConverter;
use AcronisCloud\Util\WHMCS\ConfigurableOptionHelper as Option;
use Exception;

class ConfigurableOptions extends TemplateHandler
{
    use GetTextTrait,
        LoggerAwareTrait;

    const PARAM_NAME = 'name';
    const PARAM_DESCRIPTION = 'description';
    const PARAM_GROUP_ID = 'id';

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
    ) {
        return new JsonErrorResponse($e);
    }

    /**
     * @param RequestInterface $request
     * @return array
     * @throws Exception
     */
    public function create(RequestInterface $request)
    {
        $this->initServerId($request);
        $this->validateRequest($request);

        $groupName = (string)$request->getBodyParameter(static::PARAM_NAME, '');
        $groupDescription = (string)$request->getBodyParameter(static::PARAM_DESCRIPTION, '');
        $edition = $request->getBodyParameter(static::PARAM_EDITION);

        $cloudApi = $this->getCloudApi();
        $rootTenantId = $cloudApi->getRootTenantId();
        $offeringItems = array_filter(
            $cloudApi->getRootTenantOfferingItems(),
            function (OfferingItemOutput $offeringItem) use ($edition, $rootTenantId, $cloudApi) {
                /** @var OfferingItemOutput $offeringItem */
                return
                    // skip disabled offering items
                    $offeringItem->getStatus() === ApiInterface::OFFERING_ITEM_STATUS_ACTIVE
                    // skip unavailable offering items for customer
                    && $cloudApi->isOfferingItemAvailableForChild(
                        $rootTenantId, ApiInterface::TENANT_KIND_CUSTOMER, $offeringItem->getName()
                    )
                    && (
                        // allow null editions to be shown as well
                        !$offeringItem->hasEdition() || is_null($offeringItem->getEdition())
                        // filter by edition
                        || $offeringItem->getEdition() === $edition
                    );
            }
        );

        usort($offeringItems, function ($offeringItem1, $offeringItem2) {
            return $this->compareOfferingItemsPriorities($offeringItem1, $offeringItem2);
        });

        $offeringItemsNames = array_unique(array_map(function ($offeringItem) {
            /** @var OfferingItemOutput $offeringItem */
            return $offeringItem->getName();
        }, $offeringItems));

        $options = [];
        $optionOrder = 0;
        $metaInfo = $this->getMetaInfo();
        foreach ($offeringItemsNames as $offeringItemName) {
            // skip unknown offering items
            $offeringItemMeta = $metaInfo->getOfferingItemMeta($offeringItemName);
            if (!$offeringItemMeta) {
                continue;
            }

            $configurableOptionMeta = $offeringItemMeta->getConfigurableOption();

            $offeringItemFriendlyName = $configurableOptionMeta->getFriendlyName();
            $offeringItemUnit = $configurableOptionMeta->getMeasurementUnit();

            $optionFullName = Option::getFullName(
                $offeringItemFriendlyName,
                $offeringItemName,
                $offeringItemUnit
            );

            $optionType = $offeringItemUnit === UomConverter::FEATURE
                ? ProductConfigOption::OPTION_TYPE_CHECKBOX
                : ProductConfigOption::OPTION_TYPE_QUANTITY;

            $subOptionName = $configurableOptionMeta->getMeasurementUnitName();

            $option[ProductConfigOption::COLUMN_OPTION_NAME] = $optionFullName;
            $option[ProductConfigOption::COLUMN_OPTION_TYPE] = $optionType;
            $option[ProductConfigOption::COLUMN_ORDER] = $optionOrder;
            $option[ProductConfigOption::RELATION_SUB_OPTIONS] = [
                [ProductConfigSubOption::COLUMN_OPTION_NAME => $subOptionName],
            ];

            $optionOrder++;

            $options[] = $option;
        }

        $group = $this->getRepository()
            ->getProductConfigGroupRepository()
            ->createGroupWithOptions($groupName, $groupDescription, $options);

        return [
            static::PARAM_GROUP_ID => $group->id,
        ];
    }

    /**
     * @param OfferingItemOutput $offeringItem1
     * @param OfferingItemOutput $offeringItem2
     *
     * @return int
     */
    private function compareOfferingItemsPriorities($offeringItem1, $offeringItem2)
    {
        // order by application
        $priority1 = $this->getApplicationPriority($offeringItem1);
        $priority2 = $this->getApplicationPriority($offeringItem2);
        if ($priority1 !== $priority2) {
            return $priority1 - $priority2;
        }

        // order by addition
        $priority1 = $this->getEditionPriority($offeringItem1);
        $priority2 = $this->getEditionPriority($offeringItem2);
        if ($priority1 !== $priority2) {
            return $priority1 - $priority2;
        }

        // order by location
        $priority1 = $this->getLocationPriority($offeringItem1);
        $priority2 = $this->getLocationPriority($offeringItem2);
        if ($priority1 !== $priority2) {
            return $priority1 - $priority2;
        }

        // order by infra
        $priority1 = $this->getInfraPriority($offeringItem1);
        $priority2 = $this->getInfraPriority($offeringItem2);
        if ($priority1 !== $priority2) {
            return $priority1 - $priority2;
        }

        // order by offering item
        $priority1 = $this->getOfferingItemPriority($offeringItem1);
        $priority2 = $this->getOfferingItemPriority($offeringItem2);

        return $priority1 - $priority2;
    }

    /**
     * @param OfferingItemOutput $offeringItem
     * @return int
     */
    private function getApplicationPriority($offeringItem)
    {
        $priorities = $this->memoize(function () {
            $applications = $this->getCloudApi()->getApplications(); // todo use getRootTenantApplications
            $metaInfo = $this->getMetaInfo();
            $hashTable = Arr::map(
                $applications,
                function ($application) {
                    /** @var Application $application */
                    return $application->getId();
                },
                function ($application) use ($metaInfo) {
                    /** @var Application $application */
                    $meta = $metaInfo->getApplicationMeta($application->getType());

                    return $meta ? $meta->getSortPriority() : null;
                },
                false
            );

            return $this->populatePrioritiesHashTable($hashTable);
        });

        return Arr::get($priorities, $offeringItem->getApplicationId(), 0);
    }

    /**
     * @param OfferingItemOutput $offeringItem
     * @return mixed
     */
    private function getEditionPriority($offeringItem)
    {
        $priorities = $this->memoize(function () {
            $editions = $this->getCloudApi()->getEditions();
            $metaInfo = $this->getMetaInfo();
            $hashTable = Arr::map(
                $editions,
                function ($edition) {
                    return $edition;
                },
                function ($edition) use ($metaInfo) {
                    $meta = $metaInfo->getEditionMeta($edition);

                    return $meta ? $meta->getSortPriority() : null;
                }
            );

            return $this->populatePrioritiesHashTable($hashTable);
        });

        return $offeringItem->hasEdition()
            ? Arr::get($priorities, $offeringItem->getEdition(), 0)
            : 0;
    }

    /**
     * @param OfferingItemOutput $offeringItem
     * @return int
     */
    private function getLocationPriority($offeringItem)
    {
        $infra = $this->getOfferingItemInfra($offeringItem);
        if (!$infra) {
            return 0;
        }

        $priorities = $this->memoize(function () {
            $items = $this->getCloudApi()->getRootTenantLocations();

            return $this->calculatePriorities($items);
        });

        return Arr::get($priorities, $infra->getLocationId(), 0);
    }

    /**
     * @param OfferingItemOutput $offeringItem
     * @return int
     */
    private function getInfraPriority($offeringItem)
    {
        if (!$offeringItem->hasInfraId()) {
            return 0;
        }

        $priorities = $this->memoize(function () {
            $items = $this->getCloudApi()->getRootTenantInfras();

            return $this->calculatePriorities($items);
        });

        return Arr::get($priorities, $offeringItem->getInfraId(), 0);
    }

    /**
     * @param OfferingItemOutput $offeringItem
     * @return int
     */
    private function getOfferingItemPriority($offeringItem)
    {
        $priorities = $this->memoize(function () {
            $items = $this->getCloudApi()->getRootTenantOfferingItems();
            $metaInfo = $this->getMetaInfo();
            $hashTable = Arr::map(
                $items,
                function ($item) {
                    /** @var OfferingItem $item */
                    return $item->getName();
                },
                function ($item) use ($metaInfo) {
                    /** @var OfferingItem $item */
                    $meta = $metaInfo->getOfferingItemMeta($item->getName());

                    return $meta ? $meta->getSortPriority() : null;
                },
                false
            );

            return $this->populatePrioritiesHashTable($hashTable);
        });

        return Arr::get($priorities, $offeringItem->getName(), 0);
    }

    /**
     * @param OfferingItemOutput $offeringItem
     * @return Infra
     */
    private function getOfferingItemInfra($offeringItem)
    {
        if (!$offeringItem->hasInfraId()) {
            return null;
        }

        $infrasHashTable = $this->memoize(function () {
            $infras = $this->getCloudApi()->getRootTenantInfras();

            return Arr::map(
                $infras,
                function ($infra) {
                    /** @var Infra $infra */
                    return $infra->getId();
                },
                function ($infra) {
                    return $infra;
                }
            );
        });

        return Arr::get($infrasHashTable, $offeringItem->getInfraId());
    }

    /**
     * @param Infra[]|Location[]|Application[] $items
     * @return array
     */
    private function calculatePriorities(array $items)
    {
        $hashTable = Arr::map(
            $items,
            function ($item) {
                /** @var Infra|Location|Application $item */
                return $item->getId();
            },
            function ($item) {
                /** @var Infra|Location|Application $item */
                return $item->getName();
            }
        );

        asort($hashTable);

        return array_map(
            function ($priority) {
                return $priority + 1;
            },
            array_flip(array_keys($hashTable))
        );
    }

    /**
     * @param array $hashTable
     * @return array
     */
    private function populatePrioritiesHashTable(array $hashTable)
    {
        $max = max($hashTable) + 1;
        ksort($hashTable);
        foreach ($hashTable as $name => $priority) {
            if (!is_null($priority)) {
                continue;
            }

            $hashTable[$name] = $max;
            $max++;
        }

        return $hashTable;
    }

    /**
     * @param RequestInterface $request
     */
    private function initServerId(RequestInterface $request)
    {
        $serverId = $request->getBodyParameter(static::PARAM_SERVER_ID);
        $this->setServerId($serverId);
    }

    /**
     * @param RequestInterface $request
     * @throws RequestException
     */
    private function validateRequest(RequestInterface $request)
    {
        $validator = new ConfigurableOptionsValidator($request->getBodyParameters());
        try {
            $validator->checkWithException();
            $cloudApi = $this->getCloudApi();
            $availableEditions = $cloudApi->getEditions();
            $validator->validateEditions($availableEditions);
        } catch (\Exception $e) {
            throw new RequestException(
                $e->getMessage(),
                ['errors' => $validator->errors()->messages()],
                StatusCodeInterface::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }
}