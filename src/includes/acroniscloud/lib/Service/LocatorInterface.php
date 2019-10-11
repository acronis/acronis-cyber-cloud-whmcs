<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service;

interface LocatorInterface
{
    public function addFactory($name, FactoryInterface $factory);

    public function hasFactory($name);

    public function getFactory($name);

    public function set($name, $instance);

    public function reset($name);

    public function get($name);

    public function has($name);
}