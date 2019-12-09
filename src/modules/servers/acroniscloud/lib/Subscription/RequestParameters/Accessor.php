<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace WHMCS\Module\Server\AcronisCloud\Subscription\RequestParameters;

use AcronisCloud\Util\Arr;
use AcronisCloud\Util\MemoizeTrait;

class Accessor
{
    use MemoizeTrait;

    const PARAMETER_USER_ID = 'userid';
    const PARAMETER_USERNAME = 'username';
    const PARAMETER_PASSWORD = 'password';
    const PARAMETER_PID = 'pid';
    const PARAMETER_SERVICE_ID = 'serviceid';
    const PARAMETER_SERVER = 'server';
    const PARAMETER_CLIENTS_DETAILS = 'clientsdetails';
    const PARAMETER_WHMCS_VERSION = 'whmcsVersion';
    const PARAMETER_CONFIG_OPTIONS = 'configoptions';

    /** @var array */
    private $parameters;

    /**
     * Accessor constructor.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return int|null
     */
    public function getProductId()
    {
        return Arr::get($this->parameters, static::PARAMETER_PID);
    }

    /**
     * @return int|null
     */
    public function getServiceId()
    {
        return Arr::get($this->parameters, static::PARAMETER_SERVICE_ID);
    }

    /**
     * @return int|null
     */
    public function getUserId()
    {
        return Arr::get($this->parameters, static::PARAMETER_USER_ID);
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return Arr::get($this->parameters, static::PARAMETER_USERNAME, '');
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return Arr::get($this->parameters, static::PARAMETER_PASSWORD, '');
    }

    /**
     * @return ProductOptions
     */
    public function getProductOptions()
    {
        return $this->memoize(function () {
            return new ProductOptions($this->parameters);
        });
    }

    /**
     * @return ConfigurableOption[]
     */
    public function getConfigurableOptions()
    {
        return $this->memoize(function () {
            $configurableOptions = [];

            $parameters = Arr::get($this->parameters, static::PARAMETER_CONFIG_OPTIONS, []);
            foreach ($parameters as $name => $value) {
                $configurableOptions[] = new ConfigurableOption($name, $value);
            }

            return $configurableOptions;
        });
    }

    /**
     * @return bool
     */
    public function hasServer()
    {
        return Arr::get($this->parameters, static::PARAMETER_SERVER, false);
    }

    /**
     * @return Server|null
     */
    public function getServer()
    {
        return $this->memoize(function () {
            if ($this->hasServer()) {
                $server = new Server($this->parameters);

                return $server;
            }

            return null;
        });
    }

    /**
     * @return bool
     */
    public function hasClientsDetails()
    {
        return Arr::has($this->parameters, static::PARAMETER_CLIENTS_DETAILS);
    }

    /**
     * @return ClientsDetails|null
     */
    public function getClientsDetails()
    {
        return $this->memoize(function () {
            if (!$this->hasClientsDetails()) {
                return null;
            }

            return new ClientsDetails(Arr::get($this->parameters, static::PARAMETER_CLIENTS_DETAILS, []));
        });
    }

    /**
     * @return string
     */
    public function getWhmcsVersion()
    {
        return Arr::get($this->parameters, static::PARAMETER_WHMCS_VERSION, '');
    }
}