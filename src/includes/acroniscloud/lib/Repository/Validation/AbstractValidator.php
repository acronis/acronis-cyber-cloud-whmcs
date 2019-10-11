<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Repository\Validation;

use AcronisCloud\Service\Localization\TranslatorFactory;
use AcronisCloud\Service\Locator;
use Illuminate\Validation\Validator;
use Symfony\Component\Translation\Translator;

abstract class AbstractValidator extends Validator
{
    const MESSAGES = [
        'boolean' => 'The :attribute field must be true or false.',
        'json' => 'The :attribute must be a valid JSON string.',
        'in' => 'The selected :attribute is invalid. Valid values: :values',
        'integer' => 'The :attribute must be an integer.',
        'max' => [
            'string' => 'The :attribute may not be greater than :max characters.',
        ],
        'min' => [
            'numeric' => 'The :attribute must be at least :min.',
        ],
        'required' => 'The :attribute field is required.',
        'string' => 'The :attribute must be a string.',
        'exists' => 'Object with this :attribute does not exist.',
    ];

    public function __construct(array $data, array $rules, array $messages = [])
    {
        $messages = array_merge(static::MESSAGES, $messages);
        parent::__construct($this->getAcronisTranslator(), $data, $rules, $messages);
    }

    /**
     * Cannot use TranslatorAwareTrait because of method name conflict with parent class
     *
     * @return Translator
     */
    protected function getAcronisTranslator()
    {
        return Locator::getInstance()->get(TranslatorFactory::NAME);
    }
}