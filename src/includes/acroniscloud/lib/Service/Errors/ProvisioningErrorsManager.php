<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Errors;

use AcronisCloud\Service\Session\ContainerInterface;
use AcronisCloud\Service\Session\SessionAwareTrait;

class ProvisioningErrorsManager
{
    use SessionAwareTrait;

    const PROVISIONING_ERRORS = 'provisioning_errors';

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container ?: $this->getSession();
    }

    /**
     * @param ErrorNotification[] $errors
     * @return ProvisioningErrorsManager
     */
    public function setErrors($errors)
    {
        $key = static::PROVISIONING_ERRORS;
        $errors = $this->serializeErrors($errors);
        $this->container->set($key, $errors);

        return $this;
    }

    /**
     * @return bool
     */
    public function hasErrors()
    {
        $key = static::PROVISIONING_ERRORS;

        return $this->container->has($key);
    }

    /**
     * @return ErrorNotification[]
     */
    public function getErrors()
    {
        $key = static::PROVISIONING_ERRORS;
        $errors =  $this->container->get($key);

        return $this->unserializeErrors($errors);
    }

    /**
     * @return ProvisioningErrorsManager
     */
    public function resetErrors()
    {
        $key = static::PROVISIONING_ERRORS;
        $this->container->delete($key);

        return $this;
    }

    /**
     * @return void
     */
    public function flush()
    {
        $this->container->close();
    }

    /**
     * @param $errors
     * @return array
     */
    protected function serializeErrors($errors)
    {
        return array_map(function (ErrorNotification $err) {
            return $err->serialize();
        }, $errors);
    }

    /**
     * @param $errors
     * @return array
     */
    protected function unserializeErrors($errors)
    {
        return array_map(function ($errData) {
            $error = new ErrorNotification();
            $error->unserialize($errData);
            return $error;
        }, $errors);
    }
}