<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Session;

interface ContainerInterface
{
    /**
     * @param $key
     * @return bool
     */
    public function has($key);

    /**
     * @param $key
     * @param $value
     */
    public function set($key, $value);

    /**
     * @param $key
     * @param $defaultValue
     */
    public function get($key, $defaultValue = null);

    /**
     * @param $key
     * @return SessionAccessor
     */
    public function delete($key);

    public function close();
}