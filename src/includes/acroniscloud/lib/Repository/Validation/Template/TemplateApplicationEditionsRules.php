<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Repository\Validation\Template;

use AcronisCloud\CloudApi\ApiInterface;
use AcronisCloud\Model\TemplateApplication;
use AcronisCloud\Model\TemplateEdition;
use AcronisCloud\Repository\Validation\ValidationException;
use AcronisCloud\Util\Arr;
use AcronisCloud\Util\MemoizeTrait;

class TemplateApplicationEditionsRules extends AbstractTemplateRule
{
    const VALIDATION_EDITIONS_PRESENT = 'editionsPresent';
    const VALIDATION_MIN_ONE_EDITION_ACTIVE = 'minOneEditionActive';
    const VALIDATION_EDITION_FORMAT = 'editionFormat';
    const VALIDATION_EDITION_NAME = 'editionName';
    const VALIDATION_EDITION_STATUS = 'editionStatus';
    const VALIDATION_ONE_EDITION_PER_CUSTOMER = 'oneEditionPerCustomer';
    const VALIDATION_ACTIVE_EDITIONS = 'editionsActive';

    /** @var string */
    private $tenantKind;

    /** @var array */
    private $applicationData;

    /** @var array */
    private $validEditions;

    public function __construct($applicationData, $tenantKind, $validEditions)
    {
        $this->applicationData = $applicationData;
        $this->tenantKind = $tenantKind;
        $this->validEditions = $validEditions;
    }

    public function getValidations()
    {
        return $this->memoize(function () {
            $this->runValidations();

            return [
                $this->buildValidation(static::VALIDATION_EDITIONS_PRESENT, 'Missing property "editions".'),
                $this->buildValidation(static::VALIDATION_EDITION_FORMAT, 'Incorrect edition format. Edition must have "name" and "status".'),
                $this->buildValidation(static::VALIDATION_EDITION_NAME, 'Edition is not present in cloud.'),
                $this->buildValidation(static::VALIDATION_EDITION_STATUS, 'Edition has an invalid status. Status must be "active" or "inactive"'),
                $this->buildValidation(static::VALIDATION_MIN_ONE_EDITION_ACTIVE, 'Active application must have at least one active edition (if there are any).'),
                $this->buildValidation(static::VALIDATION_ONE_EDITION_PER_CUSTOMER, 'Applications can have only one edition when the tenant kind is customer.'),
                $this->buildValidation(static::VALIDATION_ACTIVE_EDITIONS, 'Inactive application cannot have editions which are enabled.'),
            ];
        });
    }

    protected function runValidations()
    {
        try {
            foreach ($this->getEditions() as $appType => $editions) {
                if (!is_array($editions)) {
                    // application is missing correct property edition
                    throw new ValidationException(static::VALIDATION_EDITIONS_PRESENT, [$appType, $editions]);
                }

                foreach ($editions as $edition) {
                    $this->validateEditionFormatValid($edition, $appType);
                    $this->validateEditionName($edition, $appType);
                    $this->validateEditionStatus($edition, $appType);
                }

                if ($this->isAppActive($appType)) {
                    $this->validateMinOneEditionActive($editions, $appType);
                    if ($this->tenantKind === ApiInterface::TENANT_KIND_CUSTOMER) {
                        $this->validateOneEditionPerCustomer($editions, $appType);
                    }
                } else {
                    $this->validateNoActiveEditionsInInactiveApp($editions, $appType);
                }
            }
        } catch (ValidationException $e) {
            $this->setFailure($e->getErrorName(), ...$e->getData());
        }
    }

    /**
     * @param $edition
     * @param $appType
     * @throws ValidationException
     */
    protected function validateEditionFormatValid($edition, $appType)
    {
        $hasValidKeys = count($edition) === 2
            && isset($edition[TemplateEdition::PROPERTY_NAME])
            && isset($edition[TemplateEdition::PROPERTY_STATUS]);
        if (!$hasValidKeys) {
            throw new ValidationException(static::VALIDATION_EDITION_FORMAT, [$appType, $edition]);
        }
    }

    /**
     * @param $edition
     * @throws ValidationException
     */
    protected function validateEditionName($edition, $appType)
    {
        $isValidName = isset($edition[TemplateEdition::PROPERTY_NAME])
            && in_array($edition[TemplateEdition::PROPERTY_NAME], $this->validEditions);
        if (!$isValidName) {
            throw new ValidationException(static::VALIDATION_EDITION_NAME, [$appType, $edition]);
        }
    }

    /**
     * @param $edition
     * @param $appType
     * @throws ValidationException
     */
    protected function validateEditionStatus($edition, $appType)
    {
        $isValidStatus = isset($edition[TemplateEdition::PROPERTY_STATUS])
            && in_array($edition[TemplateEdition::PROPERTY_STATUS], TemplateEdition::ALLOWED_STATUSES);
        if (!$isValidStatus) {
            throw new ValidationException(static::VALIDATION_EDITION_STATUS, [$appType, $edition]);
        }
    }

    /**
     * An active application must have at least one active edition (if it has editions)
     *
     * @param $editions
     * @param $appType
     * @throws ValidationException
     */
    protected function validateMinOneEditionActive($editions, $appType)
    {
        $activeEditionsCount = count(array_filter($editions, function ($e) {
            return $e[TemplateEdition::PROPERTY_STATUS] === TemplateEdition::STATUS_ACTIVE;
        }));

        if (count($editions) && !$activeEditionsCount) {
            throw new ValidationException(static::VALIDATION_MIN_ONE_EDITION_ACTIVE, [$appType, $editions]);
        }
    }

    /**
     * There needs to be exactly one edition (if editions are present) per application
     * for non-partner tenant kinds
     *
     * @param $editions
     * @throws ValidationException
     */
    protected function validateOneEditionPerCustomer($editions, $appType)
    {
        $activeEditionsCount = count(array_filter($editions, function ($e) {
            return $e[TemplateEdition::PROPERTY_STATUS] === TemplateEdition::STATUS_ACTIVE;
        }));

        if (count($editions) && $activeEditionsCount !== 1) {
            throw new ValidationException(static::VALIDATION_ONE_EDITION_PER_CUSTOMER, [$appType, $editions]);
        }
    }

    /**
     * @param array $editions
     * @param $appType
     * @throws ValidationException
     */
    protected function validateNoActiveEditionsInInactiveApp(array $editions, $appType)
    {
        $activeEditionsCount = count(array_filter($editions, function ($edition) {
            return $edition[TemplateEdition::PROPERTY_STATUS] === TemplateEdition::STATUS_ACTIVE;
        }));

        if ($activeEditionsCount > 0) {
            throw new ValidationException(static::VALIDATION_ACTIVE_EDITIONS, [$appType, $editions]);
        }
    }

    private function setFailure($failure, $application, $edition)
    {
        $this->failedValidations[$failure] = [
            'application' => $application,
            'edition(s)' => $edition,
        ];
    }

    /**
     * @return array
     */
    private function getEditions()
    {
        return Arr::map(
            $this->applicationData,
            TemplateApplication::COLUMN_TYPE,
            TemplateApplication::COLUMN_EDITIONS
        );
    }

    private function isAppActive($appType)
    {
        $appStatusMap = $this->memoize(function () {
            return Arr::map(
                $this->applicationData,
                TemplateApplication::COLUMN_TYPE,
                TemplateApplication::COLUMN_STATUS
            );
        });

        return $appStatusMap[$appType] === TemplateApplication::STATUS_ACTIVE;
    }
}