<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Cache;

/**
 * Is designed very close to PSR-16 cache interface
 * https://www.php-fig.org/psr/psr-16/
 * Interface CacheInterface
 *
 * @package AcronisCloud\Service\Cache
 */
interface CacheInterface
{
    /**
     * @param string $key
     * @param mixed $value
     * @param int|null $ttl
     */
    public function set($key, $value, $ttl = null);

    /**
     * @param string $key
     * @param string|null $default
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * @param string $key
     * @return bool
     */
    public function has($key);

    /**
     * @param string $key
     * @return bool
     */
    public function delete($key);

    /**
     * @return bool
     */
    public function clear();
}