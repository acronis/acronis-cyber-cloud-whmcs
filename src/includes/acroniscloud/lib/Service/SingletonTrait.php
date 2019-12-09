<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service;

trait SingletonTrait
{
    protected static $instance;

    final public static function getInstance()
    {
        return isset(static::$instance)
            ? static::$instance
            : static::$instance = new static;
    }

    final private function __construct()
    {
        $this->init();
    }

    protected function init()
    {
    }

    final private function __wakeup()
    {
    }

    final private function __clone()
    {
    }
}