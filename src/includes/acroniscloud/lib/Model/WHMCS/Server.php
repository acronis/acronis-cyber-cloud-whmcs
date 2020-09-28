<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Model\WHMCS;

use Acronis\Cloud\Client\Model\Clients\ClientPostResult;
use Acronis\UsageReport\Model\DatacenterInterface;
use AcronisCloud\CloudApi\AuthorizedApi;
use AcronisCloud\CloudApi\CloudServerInterface;
use AcronisCloud\Model\AbstractModel;
use AcronisCloud\Util\WHMCS\LocalApi;

class Server extends AbstractModel implements CloudServerInterface, DatacenterInterface
{
    const TABLE = 'tblservers';

    const COLUMN_TYPE = 'type';
    const COLUMN_DISABLED = 'disabled';
    const COLUMN_NAME = 'name';
    const COLUMN_SECURE = 'secure';
    const COLUMN_HOSTNAME = 'hostname';
    const COLUMN_PORT = 'port';
    const COLUMN_USERNAME = 'username';
    const COLUMN_PASSWORD = 'password';
    const COLUMN_ACCESSHASH = 'accesshash';

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    public $timestamps = false;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getAttributeValue(static::COLUMN_TYPE);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getAttributeValue(static::COLUMN_NAME);
    }

    /**
     * @return bool
     */
    public function isSecure()
    {
        return $this->getAttributeValue(static::COLUMN_SECURE) === 'on';
    }

    /**
     * @return string
     */
    public function getHostname()
    {
        return $this->getAttributeValue(static::COLUMN_HOSTNAME);
    }

    /**
     * @param $hostname
     * @return Server
     */
    public function setHostname($hostname)
    {
        $this->setAttribute(static::COLUMN_HOSTNAME, $hostname);

        return $this;
    }

    /**
     * @return string
     */
    public function getPort()
    {
        return $this->getAttributeValue(static::COLUMN_PORT);
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->getAttributeValue(static::COLUMN_USERNAME);
    }

    /**
     * @param $username
     * @return Server
     */
    public function setUsername($username)
    {
        $this->setAttribute(static::COLUMN_USERNAME, $username);

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        $encryptedPassword = $this->getAttributeValue(static::COLUMN_PASSWORD);

        return $encryptedPassword
            ? LocalApi::decryptPassword($encryptedPassword)
            : $encryptedPassword;
    }

    /**
     * @param $password
     * @return Server
     */
    public function setPassword($password)
    {
        $encryptedPassword = $password ? LocalApi::encryptPassword($password) : $password;
        $this->setAttribute(static::COLUMN_PASSWORD, $encryptedPassword);

        return $this;
    }

    public function getStatus()
    {
        $isDisabled = $this->getAttributeValue(static::COLUMN_DISABLED);

        return $isDisabled ? static::STATUS_INACTIVE : static::STATUS_ACTIVE;
    }

    /**
     * @return string
     */
    public function getAccessHash()
    {
        return $this->getAttributeValue(static::COLUMN_ACCESSHASH);
    }

    /**
     * @param $accessHash
     * @return Server
     */
    public function setAccessHash($accessHash)
    {
        $this->setAttribute(static::COLUMN_ACCESSHASH, $accessHash);

        return $this;
    }
}