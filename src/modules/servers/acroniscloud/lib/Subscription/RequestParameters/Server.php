<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace WHMCS\Module\Server\AcronisCloud\Subscription\RequestParameters;

use AcronisCloud\CloudApi\CloudServerInterface;
use AcronisCloud\Util\Arr;

class Server implements CloudServerInterface
{
    const PARAMETER_SERVER_ID = 'serverid';
    const PARAMETER_SERVER_SECURE = 'serversecure';
    const PARAMETER_SERVER_HOSTNAME = 'serverhostname';
    const PARAMETER_SERVER_PORT = 'serverport';
    const PARAMETER_SERVER_USERNAME = 'serverusername';
    const PARAMETER_SERVER_PASSWORD = 'serverpassword';
    const PARAMETER_SERVER_ACCESSHASH = 'serveraccesshash';

    /** @var array */
    private $parameters;

    /**
     * Server constructor.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return Arr::get($this->parameters, static::PARAMETER_SERVER_ID);
    }

    /**
     * @return bool
     */
    public function isSecure()
    {
        return Arr::get($this->parameters, static::PARAMETER_SERVER_SECURE, true);
    }

    /**
     * @return string
     */
    public function getHostname()
    {
        return Arr::get($this->parameters, static::PARAMETER_SERVER_HOSTNAME, '');
    }

    /**
     * @return string
     */
    public function getPort()
    {
        return Arr::get($this->parameters, static::PARAMETER_SERVER_PORT, '');
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return Arr::get($this->parameters, static::PARAMETER_SERVER_USERNAME, '');
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return Arr::get($this->parameters, static::PARAMETER_SERVER_PASSWORD, '');
    }

    /**
     * @return string
     */
    public function getAccessHash()
    {
        return Arr::get($this->parameters, static::PARAMETER_SERVER_ACCESSHASH, '');
    }
}