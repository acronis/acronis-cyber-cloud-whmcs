<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Repository\Validation\Template;

use AcronisCloud\Model\Template;
use AcronisCloud\Model\TemplateApplication;
use AcronisCloud\Repository\Validation\ValidationException;
use AcronisCloud\Service\MetaInfo\MetaInfoAwareTrait;
use AcronisCloud\Util\MemoizeTrait;
use WHMCS\Module\Server\AcronisCloud\Controller\ClientAreaApi;

class TemplateApplicationRules extends AbstractTemplateRule
{
    use MetaInfoAwareTrait;

    const VALIDATION_APP_FORMAT = 'appFormat';
    const VALIDATION_APP_TYPE_META = 'appTypeMeta';
    const VALIDATION_APP_TYPE_ALLOWED = 'appTypeAllowed';
    const VALIDATION_APP_STATUS_ALLOWED = 'appStatusAllowed';
    const VALIDATION_MIN_ONE_ACTIVE = 'appMinOneActive';

    protected $templateData;

    /** @var TemplateApplicationEditionsRules */
    private $editionRules;

    private $offeringItemRules;

    private $allowedApplications;

    public function __construct(array $templateData, $allowedApplications, $allowedEditions, $allowedOfferingItems, $allowedInfras)
    {
        $this->templateData = $templateData;
        $this->allowedApplications = $allowedApplications;
        $this->editionRules = new TemplateApplicationEditionsRules(
            $templateData[Template::RELATION_APPLICATIONS],
            $templateData[Template::COLUMN_TENANT_KIND],
            $allowedEditions
        );
        $this->offeringItemRules = new TemplateOfferingItemRules(
            $templateData[Template::RELATION_APPLICATIONS],
            $templateData[Template::COLUMN_TENANT_KIND],
            $allowedOfferingItems,
            $allowedInfras
        );
    }

    /**
     * @return ValidationRule[]
     */
    public function allRules()
    {
        $ruleContainers = [$this, $this->editionRules, $this->offeringItemRules];
        $rules = [];
        $failure = false;
        // stop validations on first failed rule
        while (!$failure && $ruleContainers) {
            /** @var AbstractTemplateRule $container */
            $container = array_shift($ruleContainers);
            $rules = array_merge($rules, $container->getValidations());
            $failure = count($container->failedValidations);
        }

        return $rules;
    }

    public function getValidations()
    {
        return $this->memoize(function () {
            $this->runValidations();

            return [
                $this->buildValidation(static::VALIDATION_APP_FORMAT, 'Application is missing properties. Required: type, status, editions and offering_items'),
                $this->buildValidation(static::VALIDATION_APP_TYPE_META, 'Invalid application type in applications list (missing meta info)'),
                $this->buildValidation(static::VALIDATION_APP_TYPE_ALLOWED, 'Application type in applications list is not enabled for root tenant'),
                $this->buildValidation(static::VALIDATION_APP_STATUS_ALLOWED, 'An application\'s status is not valid. Allowed values: active, inactive.'),
                $this->buildValidation(static::VALIDATION_MIN_ONE_ACTIVE, 'At least one application must be active.'),
            ];
        });
    }

    protected function runValidations()
    {
        try {
            $applications = $this->templateData[Template::RELATION_APPLICATIONS];
            foreach ($applications as $application) {
                $this->validateAppFormat($application);
                $this->validateAppType($application);
                $this->validateAppStatus($application);
            }
            $this->validateMinOneActive($applications);
        } catch (ValidationException $e) {
            $this->failedValidations[$e->getErrorName()] = $e->getData();
        }
    }

    /**
     * @param $application
     * @throws ValidationException
     */
    protected function validateAppFormat($application)
    {
        if (
            !isset($application[TemplateApplication::COLUMN_TYPE])
            || !isset($application[TemplateApplication::COLUMN_STATUS])
            || !isset($application[TemplateApplication::COLUMN_EDITIONS])
            || !isset($application[ClientAreaApi::PROPERTY_OFFERING_ITEMS])
        ) {
            throw new ValidationException(static::VALIDATION_APP_FORMAT, ['application' => $application]);
        }
    }

    /**
     * @param $application
     * @throws ValidationException
     */
    protected function validateAppType($application)
    {
        $appType = $application[TemplateApplication::COLUMN_TYPE];
        if (!$this->getMetaInfo()->hasApplicationMeta($appType)) {
            throw new ValidationException(static::VALIDATION_APP_TYPE_META, ['application' => $application]);
        };
        if (!in_array($appType, $this->allowedApplications)) {
            throw new ValidationException(static::VALIDATION_APP_TYPE_ALLOWED, ['application' => $application]);
        }
    }

    /**
     * @param $application
     * @throws ValidationException
     */
    protected function validateAppStatus($application)
    {
        $appStatus = $application[TemplateApplication::COLUMN_STATUS];
        if (!in_array($appStatus, TemplateApplication::ALLOWED_STATUSES)) {
            throw new ValidationException(static::VALIDATION_APP_STATUS_ALLOWED, ['application' => $application]);
        }
    }

    /**
     * @param $applications
     * @throws ValidationException
     */
    protected function validateMinOneActive($applications)
    {
        $activeApplications = count(array_filter($applications, function ($a) {
            return $a[TemplateApplication::COLUMN_STATUS] === TemplateApplication::STATUS_ACTIVE;
        }));

        if (!$activeApplications) {
            throw new ValidationException(static::VALIDATION_MIN_ONE_ACTIVE, ['applications' => $applications]);
        }
    }
}