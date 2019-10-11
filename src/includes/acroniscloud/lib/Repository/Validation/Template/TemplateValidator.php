<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Repository\Validation\Template;

use Acronis\Cloud\Client\Model\Applications\Application;
use Acronis\Cloud\Client\Model\Infra\Infra;
use Acronis\Cloud\Client\Model\OfferingItems\OfferingItemOutput;
use AcronisCloud\CloudApi\Api;
use AcronisCloud\Model\Template;
use AcronisCloud\Repository\Validation\AbstractValidator;
use Illuminate\Validation\DatabasePresenceVerifier;
use WHMCS\Database\Capsule;

class TemplateValidator extends AbstractValidator
{
    const RULES = [
        Template::COLUMN_NAME => 'required|string|max:255',
        Template::COLUMN_DESCRIPTION => 'sometimes|max:1024',
        Template::COLUMN_SERVER_ID => 'required|integer|min:1',
        Template::COLUMN_TENANT_KIND => 'required|in:partner,customer',
        Template::COLUMN_USER_ROLE => 'sometimes|in:admin,user',
    ];

    private $customErrorMessages = [];

    /** @var Application[] */
    private $applications;

    /** @var OfferingItemOutput[] */
    private $offeringItems;

    /** @var Infra[] */
    private $infras;

    public function __construct(array $data, $applications, $offeringItems, $infras)
    {
        $this->applications = $applications;
        $this->offeringItems = $offeringItems;
        $this->infras = $infras;
        $rules = $this->getValidationRules($data);
        parent::__construct($data, $rules, $this->customErrorMessages);
    }

    /**
     * @param $data
     * @return array
     */
    private function getValidationRules($data)
    {
        $rules = static::RULES;
        if (isset($data[Template::COLUMN_ID])) {
            $rules[Template::COLUMN_ID] = 'required|integer|exists:' . Template::TABLE . ',' . Template::COLUMN_ID;
            $dbManager = Capsule::getInstance()->getDatabaseManager();
            $this->setPresenceVerifier(new DatabasePresenceVerifier($dbManager));
        }

        $rules[Template::RELATION_APPLICATIONS] = $this->loadApplicationRules($data);

        return $rules;
    }

    /**
     * Adds custom rules
     *
     * @param $data
     * @return string
     */
    private function loadApplicationRules($data)
    {
        $validations = ['required', 'array'];
        $applications = array_map(function ($app) {
            return $app->getType();
        }, $this->applications);
        $editions = $this->getActiveEditions();
        $offeringItems = $this->getActiveOfferingItems();
        $infras = array_map(function ($i) {
            return $i->getId();
        }, $this->infras);
        // https://laravel.com/docs/5.1/validation#custom-validation-rules
        $rules = (new TemplateApplicationRules($data, $applications, $editions, $offeringItems, $infras))
            ->allRules();
        foreach ($rules as $rule) {
            $this->addExtension($rule->getName(), $rule->getResolution());
            $this->customErrorMessages[$rule->getMessageKey()] = $rule->getErrorMessage();
            $validations[] = $rule->getName();
        }

        return implode('|', $validations);
    }

    private function getActiveEditions()
    {
        $editions = [];
        foreach ($this->offeringItems as $offeringItem) {
            if ($offeringItem->getStatus() === Api::OFFERING_ITEM_STATUS_ACTIVE && $offeringItem->hasEdition()) {
                $editions[$offeringItem->getEdition()] = true;
            }
        }

        return array_keys($editions);
    }

    private function getActiveOfferingItems()
    {
        $offeringItems = [];
        foreach ($this->offeringItems as $offeringItem) {
            if ($offeringItem->getStatus() === Api::OFFERING_ITEM_STATUS_ACTIVE) {
                $offeringItems[] = $offeringItem->getName();
            }
        }

        return $offeringItems;
    }
}