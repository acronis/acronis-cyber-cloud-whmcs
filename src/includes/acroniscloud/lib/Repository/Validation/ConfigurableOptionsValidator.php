<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Repository\Validation;

use WHMCS\Module\Addon\AcronisCloud\Controller\ConfigurableOptions;

class ConfigurableOptionsValidator extends AbstractValidator
{
    const RULES = [
        ConfigurableOptions::PARAM_SERVER_ID => 'required|integer|min:1',
        ConfigurableOptions::PARAM_NAME => 'required|max:1024',
        ConfigurableOptions::PARAM_DESCRIPTION => 'sometimes|max:1024',
    ];

    const ERROR_MESSAGE = 'Invalid configurable option data.';

    public function __construct(array $data)
    {
        parent::__construct($data, static::RULES);
    }

    /**
     * @throws \Exception
     */
    public function checkWithException()
    {
        if (!$this->passes()) {
            throw new \Exception(self::ERROR_MESSAGE);
        }
    }

    /**
     * @param $validEditions
     * @throws \Exception
     */
    public function validateEditions($validEditions)
    {
        $editionsRules = $this->getEditionRules($validEditions);
        $this->setRules($editionsRules);
        $this->checkWithException();
    }

    /**
     * @param $validEditions
     * @return string[]
     */
    private function getEditionRules($validEditions)
    {
        return [
            ConfigurableOptions::PARAM_EDITION => 'required|in:' . implode(',', $validEditions),
        ];
    }
}