<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Repository\Validation\Template;

use AcronisCloud\Util\MemoizeTrait;

abstract class AbstractTemplateRule
{
    use MemoizeTrait;

    protected $failedValidations = [];

    abstract public function getValidations();

    protected function buildValidation($ruleName, $message)
    {
        $hasErrors = isset($this->failedValidations[$ruleName]);
        $validation = new ValidationRule($ruleName, $message, !$hasErrors);
        if ($hasErrors && is_array($this->failedValidations[$ruleName])) {
            $details = $this->failedValidations[$ruleName];
            $validation->setDetails($details);
        }

        return $validation;
    }

    abstract protected function runValidations();
}