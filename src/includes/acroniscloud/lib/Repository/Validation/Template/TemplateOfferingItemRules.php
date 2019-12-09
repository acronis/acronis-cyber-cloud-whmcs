<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Repository\Validation\Template;

use AcronisCloud\CloudApi\ApiInterface;
use AcronisCloud\Localization\GetTextTrait;
use AcronisCloud\Model\TemplateApplication;
use AcronisCloud\Model\TemplateEdition;
use AcronisCloud\Model\TemplateOfferingItem;
use AcronisCloud\Repository\Validation\ValidationException;
use AcronisCloud\Service\MetaInfo\MetaInfoAwareTrait;
use AcronisCloud\Service\MetaInfo\OfferingItemMeta;
use AcronisCloud\Util\Arr;
use AcronisCloud\Util\MemoizeTrait;
use AcronisCloud\Util\Str;
use AcronisCloud\Util\UomConverter;
use WHMCS\Module\Server\AcronisCloud\Controller\ClientAreaApi;

class TemplateOfferingItemRules extends AbstractTemplateRule
{
    use MetaInfoAwareTrait,
        MemoizeTrait,
        GetTextTrait;

    const VALIDATION_FORMAT = 'OfferingItemFormat';
    const VALIDATION_ALLOWED = 'OfferingItemAllowed';
    const VALIDATION_META_PRESENT = 'OfferingItemMetaPresent';
    const VALIDATION_META_APP_TYPE = 'OfferingItemMetaAppType';
    const VALIDATION_META_MEASUREMENT = 'OfferingItemMetaMeasurement';
    const VALIDATION_APPLICATION_ACTIVE = 'OfferingItemAppActive';
    const VALIDATION_EDITION_ACTIVE = 'OfferingItemEditionActive';
    const VALIDATION_FEATURE_NO_QUOTA = 'OfferingItemFeatureNoQuota';
    const VALIDATION_QUOTA_VALUE = 'OfferingItemQuotaValue';
    const VALIDATION_PARENT_INACTIVE = 'OfferingItemParentInactive';
    const VALIDATION_STATUS = 'OfferingItemStatus';
    const VALIDATION_INFRA_ID = 'OfferingItemInfraId';
    const VALIDATION_CUSTOMER_UNIQUE_ITEMS = 'OfferingItemCustomerUnique';

    /**
     * @var array
     */
    private $applicationsData;

    /**
     * @var string
     */
    private $tenantKind;

    /**
     * @var string[]
     */
    private $allowedOfferingItems;

    /**
     * @var string[]
     */
    private $infras;

    public function __construct($applicationsData, $tenantKind, $allowedOfferingItems, $infras)
    {
        $this->applicationsData = $applicationsData;
        $this->tenantKind = $tenantKind;
        $this->allowedOfferingItems = $allowedOfferingItems;
        $this->infras = $infras;
    }

    public function getValidations()
    {
        return $this->memoize(function () {
            $this->runValidations();

            return [
                $this->buildValidation(static::VALIDATION_FORMAT, 'Invalid offering item, required fields: name, measurement_unit, status.'),
                $this->buildValidation(static::VALIDATION_ALLOWED, Str::format('Offering item does not exist in {0}. Check available offering items in the parent tenant.', ACRONIS_CLOUD_FRIENDLY_NAME)),
                $this->buildValidation(static::VALIDATION_META_PRESENT, 'Offering item has no meta information.'),
                $this->buildValidation(static::VALIDATION_META_APP_TYPE, 'Offering item application type is different than application type in meta info.'),
                $this->buildValidation(static::VALIDATION_META_MEASUREMENT, 'Offering item measurement unit is not the same as in meta info.'),
                $this->buildValidation(static::VALIDATION_APPLICATION_ACTIVE, 'Active offering item cannot belong to inactive application.'),
                $this->buildValidation(static::VALIDATION_EDITION_ACTIVE, 'Active offering item cannot belong to missing or inactive edition.'),
                $this->buildValidation(static::VALIDATION_FEATURE_NO_QUOTA, 'Offering item that is a feature cannot have quota value.'),
                $this->buildValidation(static::VALIDATION_QUOTA_VALUE, 'Offering item quota value (if set) must be a positive integer.'),
                $this->buildValidation(static::VALIDATION_STATUS, 'Offering item status must be either "active" or "inactive".'),
                $this->buildValidation(static::VALIDATION_INFRA_ID, 'Offering item has infra id that doesn\'t exist in Cloud.'),
                $this->buildValidation(static::VALIDATION_PARENT_INACTIVE, 'Offering item is a child of an offering item that is inactive or missing.'),
                $this->buildValidation(static::VALIDATION_CUSTOMER_UNIQUE_ITEMS, 'Only one infrastructure component can be enabled for a tenant of the "Customer" type.'),
            ];
        });
    }

    protected function runValidations()
    {
        foreach ($this->applicationsData as $application) {
            $appType = $application[TemplateApplication::COLUMN_TYPE];
            $offeringItems = $application[ClientAreaApi::PROPERTY_OFFERING_ITEMS];
            $this->validateOfferingItems($offeringItems, $appType);
        }
    }

    protected function validateOfferingItems($offeringItems, $application)
    {
        try {
            if ($this->tenantKind === ApiInterface::TENANT_KIND_CUSTOMER) {
                $this->validateUniqueOINames($offeringItems, $application);
            }
            foreach ($offeringItems as $offeringItem) {
                $this->validateFormat($offeringItem, $application);
                $this->validateOIAllowed($offeringItem, $application);
                $this->validateOIMeta($offeringItem, $application);
                $this->validateQuotaValue($offeringItem, $application);
                $this->validateStatus($offeringItem, $application);
                $this->validateInfraId($offeringItem, $application);
                $this->validateNoQuotaForFeature($offeringItem, $application);
                $this->validateParentOIisActive($offeringItem, $application);
            }
        } catch (ValidationException $e) {
            $this->setFailure($e->getErrorName(), ...$e->getData());
        }
    }

    /**
     * @param $offeringItems
     * @param $application
     * @throws ValidationException
     */
    protected function validateUniqueOINames($offeringItems, $application)
    {
        $nameFlags = [];
        foreach ($offeringItems as $offeringItem) {
            if ($offeringItem[TemplateOfferingItem::COLUMN_STATUS] === TemplateOfferingItem::STATUS_INACTIVE) {
                continue;
            }

            $offeringItemName = $offeringItem[TemplateOfferingItem::COLUMN_NAME];
            if (isset($nameFlags[$offeringItemName])) {
                throw new ValidationException(static::VALIDATION_CUSTOMER_UNIQUE_ITEMS, [$application, $offeringItem]);
            }
            $nameFlags[$offeringItemName] = true;
        }
    }

    /**
     * @param $offeringItem
     * @param $application
     * @throws ValidationException
     */
    protected function validateFormat($offeringItem, $application)
    {
        if (!isset($offeringItem[TemplateOfferingItem::COLUMN_NAME])
            || !isset($offeringItem[TemplateOfferingItem::COLUMN_MEASUREMENT_UNIT])
            || !isset($offeringItem[TemplateOfferingItem::COLUMN_STATUS])
        ) {
            throw new ValidationException(static::VALIDATION_FORMAT, [$application, $offeringItem]);
        }
    }

    /**
     * @param $offeringItem
     * @param $application
     * @throws ValidationException
     */
    protected function validateOIAllowed($offeringItem, $application)
    {
        if (!in_array($offeringItem[TemplateOfferingItem::COLUMN_NAME], $this->allowedOfferingItems)) {
            throw new ValidationException(static::VALIDATION_ALLOWED, [$application, $offeringItem]);
        }
    }

    /**
     * @param $offeringItem
     * @param $application
     * @throws ValidationException
     */
    protected function validateOIMeta($offeringItem, $application)
    {
        $oiName = $offeringItem[TemplateOfferingItem::COLUMN_NAME];
        $oiMeta = $this->getMetaInfo()->getOfferingItemMeta($oiName);
        if (!$oiMeta) {
            throw new ValidationException(static::VALIDATION_META_PRESENT, [$application, $offeringItem]);
        }
        if ($oiMeta->getApplicationType() !== $application) {
            throw new ValidationException(static::VALIDATION_META_APP_TYPE, [$application, $offeringItem, $oiMeta]);
        }

        $oiMeasurement = $offeringItem[TemplateOfferingItem::COLUMN_MEASUREMENT_UNIT];
        $oiMetaMeasurement = $oiMeta->getMeasurementUnit();
        if ($oiMeasurement !== $oiMetaMeasurement) {
            throw new ValidationException(static::VALIDATION_META_MEASUREMENT, [$application, $offeringItem, $oiMeta]);
        }

        if ($offeringItem[TemplateOfferingItem::PROPERTY_STATUS] === TemplateOfferingItem::STATUS_ACTIVE) {
            $this->validateAppActive($offeringItem, $application);
            $this->validateEditionActive($offeringItem, $oiMeta, $application);
        }
    }

    /**
     * @param $offeringItem
     * @param $application
     * @throws ValidationException
     */
    protected function validateAppActive($offeringItem, $application)
    {
        $appData = $this->getAppData($application);

        if ($appData[TemplateApplication::COLUMN_STATUS] === TemplateApplication::STATUS_INACTIVE) {
            throw new ValidationException(static::VALIDATION_APPLICATION_ACTIVE, [$application, $offeringItem]);
        }
    }

    /**
     * @param $offeringItem
     * @param OfferingItemMeta $oiMeta
     * @param $application
     * @throws ValidationException
     */
    protected function validateEditionActive($offeringItem, OfferingItemMeta $oiMeta, $application)
    {
        $editions = $this->getEditions($application);
        $metaEditionName = $oiMeta->getEditionName();
        if (!$editions || !$metaEditionName) {
            return;
        }
        $oiEdition = Arr::get($editions, $metaEditionName);
        if (!$oiEdition || $oiEdition[TemplateEdition::PROPERTY_STATUS] === TemplateEdition::STATUS_INACTIVE) {
            throw new ValidationException(static::VALIDATION_EDITION_ACTIVE, [$application, $offeringItem, $oiMeta]);
        }
    }

    /**
     * @param $offeringItem
     * @param $application
     * @throws ValidationException
     */
    protected function validateQuotaValue($offeringItem, $application)
    {
        $oiQuotaValue = Arr::get($offeringItem, TemplateOfferingItem::COLUMN_QUOTA_VALUE);
        if ($oiQuotaValue && (!is_int($oiQuotaValue) || (int)$oiQuotaValue < 0)) {
            throw new ValidationException(static::VALIDATION_QUOTA_VALUE, [$application, $offeringItem]);
        }
    }

    /**
     * @param $offeringItem
     * @param $application
     * @throws ValidationException
     */
    protected function validateStatus($offeringItem, $application)
    {
        $oiStatus = $offeringItem[TemplateOfferingItem::COLUMN_STATUS];
        if (!in_array($oiStatus, TemplateOfferingItem::ALLOWED_STATUSES)) {
            throw new ValidationException(static::VALIDATION_STATUS, [$application, $offeringItem]);
        }
    }

    /**
     * @param $offeringItem
     * @param $application
     * @throws ValidationException
     */
    protected function validateInfraId($offeringItem, $application)
    {
        $oiInfraId = Arr::get($offeringItem, TemplateOfferingItem::COLUMN_INFRA_ID);
        if ($oiInfraId && !in_array($oiInfraId, $this->infras)) {
            throw new ValidationException(static::VALIDATION_INFRA_ID, [$application, $offeringItem]);
        }
    }

    /**
     * @param $offeringItem
     * @param $application
     * @throws ValidationException
     */
    protected function validateNoQuotaForFeature($offeringItem, $application)
    {
        if ($offeringItem[TemplateOfferingItem::COLUMN_MEASUREMENT_UNIT] !== UomConverter::KIND_FEATURE) {
            return;
        }

        $quota = Arr::get($offeringItem, TemplateOfferingItem::COLUMN_QUOTA_VALUE);
        if (!is_null($quota)) {
            throw new ValidationException(static::VALIDATION_FEATURE_NO_QUOTA, [$application, $offeringItem]);
        }
    }

    /**
     * @param $offeringItem
     * @param $application
     * @throws ValidationException
     */
    protected function validateParentOIisActive($offeringItem, $application)
    {
        if ($offeringItem[TemplateOfferingItem::COLUMN_STATUS] === TemplateOfferingItem::STATUS_INACTIVE) {
            return;
        }
        $parent = $this->getParent($offeringItem, $application);
        if ($parent === false || $parent[TemplateOfferingItem::PROPERTY_STATUS] === TemplateOfferingItem::STATUS_INACTIVE) {
            throw new ValidationException(static::VALIDATION_PARENT_INACTIVE, [$application, $offeringItem]);
        }
    }

    private function setFailure($failureName, $appType, $offeringItem, $metaInfo = null)
    {
        $this->failedValidations[$failureName] = [
            'application' => $appType,
            'offering_item' => $offeringItem,
        ];
        if ($metaInfo) {
            $this->failedValidations[$failureName]['metaInfo'] = $metaInfo->getData();
        }
    }

    private function getAppData($appType)
    {
        return $this->memoize(function () use ($appType) {
            return current(array_filter(
                $this->applicationsData,
                function ($app) use ($appType) {
                    $colType = TemplateApplication::COLUMN_TYPE;

                    return isset($app[$colType]) && $app[$colType] === $appType;
                }
            ));
        }, $appType);
    }

    private function getEditions($appType)
    {
        return $this->memoize(function () use ($appType) {
            $appData = $this->getAppData($appType);

            return Arr::map(
                $appData[TemplateApplication::COLUMN_EDITIONS],
                TemplateEdition::PROPERTY_NAME,
                function ($edition) {
                    return $edition;
                }
            );
        }, $appType);
    }

    private function getParent($offeringItem, $appType)
    {
        $parentsMapping = $this->memoize(function () {
            $offeringItems = $this->getMetaInfo()->getOfferingItemsMeta();
            $oiParents = [];
            foreach ($offeringItems as $offeringItem) {
                foreach ($offeringItem->getChildOfferingItems() as $childName) {
                    $oiParents[$childName] = $offeringItem->getOfferingItemName();
                }
            }

            return $oiParents;
        });

        $parent = null;
        $oiName = $offeringItem[TemplateOfferingItem::COLUMN_NAME];
        $parentName = Arr::get($parentsMapping, $oiName);
        if ($parentName) {
            $application = $this->getAppData($appType);
            $offeringItems = $application[ClientAreaApi::PROPERTY_OFFERING_ITEMS];
            $parent = current(array_filter(
                $offeringItems,
                function ($oi) use ($parentName) {
                    return $oi[TemplateOfferingItem::COLUMN_NAME] === $parentName;
                }
            ));
        }

        return $parent;
    }
}