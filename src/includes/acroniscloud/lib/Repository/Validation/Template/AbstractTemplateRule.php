<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
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