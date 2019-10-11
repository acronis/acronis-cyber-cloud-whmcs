<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Session;

use AcronisCloud\Service\Locator;

class SessionAccessor implements ContainerInterface
{
    /** @var string  */
    private $sessionNamespace = ACRONIS_CLOUD_SERVICE_NAME;

    private static $sessionEnabledInternally = false;

    /**
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        $this->initSession();

        return isset($_SESSION[$this->sessionNamespace][$key]);
    }

    /**
     * @param $key
     * @param $value
     * @return SessionAccessor
     */
    public function set($key, $value)
    {
        $this->initSession();
        $_SESSION[$this->sessionNamespace][$key] = $value;

        return $this;
    }

    /**
     * @param $key
     * @param $defaultValue
     * @return SessionAccessor
     */
    public function get($key, $defaultValue = null)
    {
        $this->initSession();
        if (!$this->has($key)) {
            return $defaultValue;
        }

        return $_SESSION[$this->sessionNamespace][$key];
    }

    /**
     * @param $key
     * @return SessionAccessor
     */
    public function delete($key)
    {
        $this->initSession();
        unset($_SESSION[$this->sessionNamespace][$key]);

        return $this;
    }

    /**
     * Re-initialize session array with original values
     * @return SessionAccessor
     */
    public function reset()
    {
        $this->initSession();
        session_reset();

        return $this;
    }

    /**
     * End the current session
     */
    public function end()
    {
        if (session_status() !== PHP_SESSION_ACTIVE || !static::$sessionEnabledInternally) {
            return;
        }
        session_write_close();

        static::$sessionEnabledInternally = false;
    }

    public function close()
    {
        $this->end();
    }

    protected function initSession()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            static::$sessionEnabledInternally = true;
            session_start();
        }
    }
}
