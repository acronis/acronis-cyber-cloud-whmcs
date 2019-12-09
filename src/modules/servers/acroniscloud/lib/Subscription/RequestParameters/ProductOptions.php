<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace WHMCS\Module\Server\AcronisCloud\Subscription\RequestParameters;

use AcronisCloud\Util\Arr;

class ProductOptions
{
    const PARAMETER_CONFIG_OPTION = 'configoption';

    const INDEX_TEMPLATE_ID = 1;
    const INDEX_ACTIVATION_METHOD = 2;

    const ACTIVATION_METHOD_EMAIL = 'email';
    const ACTIVATION_METHOD_PASSWORD = 'password';

    /** @var array */
    private $parameters;

    /**
     * ProductOptions constructor.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @param $index
     * @return string
     */
    public static function getConfigOptionName($index)
    {
        return static::PARAMETER_CONFIG_OPTION . $index;
    }

    /**
     * @param int $index
     * @return string
     */
    public function getConfigOption($index)
    {
        return Arr::get($this->parameters, static::getConfigOptionName($index));
    }

    /**
     * @return string
     */
    public function getTemplateId()
    {
        return $this->getConfigOption(static::INDEX_TEMPLATE_ID);
    }

    /**
     * @return string
     */
    public function getActivationMethod()
    {
        return $this->getConfigOption(static::INDEX_ACTIVATION_METHOD);
    }
}