<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Cache;

use Exception;

/**
 * It is designed as singleton as we can't have many instances of APCU and manage their settings separetly
 * Class ApcuCache
 * @package AcronisCloud\Service\Cache
 */
class ApcuCacheStorage implements CacheInterface
{
    /** @var int|null */
    private $defaultTtl;

    /** @var string */
    private $keyPrefix;

    /** @var ApcuCache */
    private $apcuCache;

    /**
     * ApcuCacheStorage constructor.
     * @param $namespace
     * @param null $defaultTtl
     * @throws Exception
     */
    public function __construct($namespace, $defaultTtl = null)
    {
        $this->defaultTtl = $defaultTtl;
        $this->keyPrefix = $namespace . '_';
        $this->apcuCache = ApcuCache::getInstance();
        $this->apcuCache->registerUniquePrefix($this->keyPrefix);
    }

    /** {@inheritdoc} */
    public function set($key, $value, $ttl = null)
    {
        $this->apcuCache->set(
            $this->formatKey($key), $value, is_null($ttl) ? $this->defaultTtl : $ttl
        );
    }

    /** {@inheritdoc} */
    public function get($key, $default = null)
    {
        return $this->apcuCache->get($this->formatKey($key), $default);
    }

    /** {@inheritdoc} */
    public function refresh($key, $ttl = null)
    {
        return $this->apcuCache->refresh(
            $this->formatKey($key), is_null($ttl) ? $this->defaultTtl : $ttl
        );
    }

    /** {@inheritdoc} */
    public function has($key)
    {
        return $this->apcuCache->has($this->formatKey($key));
    }

    /** {@inheritdoc} */
    public function delete($key)
    {
        return $this->apcuCache->delete($this->formatKey($key));
    }

    /** {@inheritdoc} */
    public function clear()
    {
        return $this->apcuCache->deleteAll($this->keyPrefix);
    }

    private function formatKey($key)
    {
        return $this->keyPrefix . $key;
    }
}