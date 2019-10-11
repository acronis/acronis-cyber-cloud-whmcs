<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Cache;

use AcronisCloud\Util\Str;
use APCIterator;
use Exception;

/**
 * It is designed as singleton as we can't have many instances of APCU and manage their settings separetly
 * @package AcronisCloud\Service\Cache
 */
class ApcuCache
{
    /** @var bool[] */
    private $prefixes = [];

    /** @var null|static */
    protected static $instance = null;

    /**
     * @return static
     * @throws Exception
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * @param string $prefix
     * @throws Exception
     */
    public function registerUniquePrefix($prefix)
    {
        if (!isset($this->prefixes[$prefix])) {
            $this->prefixes[$prefix] = true;
        } else {
            throw new Exception(Str::format(
                'Trying to register prefix "%s" for APCu cache more than once.',
                $prefix
            ));
        }
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed If key don't exists returns null
     */
    public function get($key, $default = null)
    {
        $value = apcu_fetch($key, $success);

        return $success ? $value : $default;
    }

    /**
     * Note: apcu_store() overrides existing value while apcu_add() not
     * @param string $key
     * @param mixed $value
     * @param int|null $ttl
     * @return bool
     */
    public function set($key, $value, $ttl = null)
    {
        return apcu_store($key, $value, (int) $ttl);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return apcu_exists($key);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function delete($key)
    {
        return apcu_delete($key);
    }

    /**
     * @param string $keyPrefix
     * @return int
     */
    public function deleteAll($keyPrefix = '')
    {
        // APCu means user here
        $keysIterator = new APCIterator('user', '#^' . $keyPrefix . '#', APC_ITER_KEY);
        $deletedCount = 0;
        foreach ($keysIterator as $key) {
            $deletedCount += apcu_delete($key);
        }

        return $deletedCount;
    }

    /**
     * ApcuCache constructor.
     * @throws Exception
     */
    private function __construct()
    {
        if (!extension_loaded('apc') || !ini_get('apc.enabled')) {
            throw new \Exception('APCu extension is not loaded or enabled.');
        }
    }
}