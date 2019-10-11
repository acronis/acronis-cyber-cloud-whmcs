<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
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