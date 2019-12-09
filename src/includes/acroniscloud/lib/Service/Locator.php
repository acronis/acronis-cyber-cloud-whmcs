<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service;

use AcronisCloud\Util\Str;

final class Locator implements SingletonInterface, LocatorInterface
{
    use SingletonTrait;

    private $factories = [];
    private $instances = [];

    public function addFactory($name, FactoryInterface $factory)
    {
        $this->factories[$name] = $factory;
    }

    public function hasFactory($name)
    {
        return isset($this->factories[$name]);
    }

    /**
     * @param string $name
     * @return FactoryInterface
     */
    public function getFactory($name)
    {
        if (!$this->hasFactory($name)) {
            throw new \InvalidArgumentException(Str::format(
                'There is no factory for the service "%s".',
                $name
            ));
        }

        return $this->factories[$name];
    }

    public function set($name, $instance)
    {
        if ($this->has($name)) {
            throw new \InvalidArgumentException(Str::format(
                'Instance for the service "%s" is already exists.',
                $name
            ));
        }
        if (!is_object($instance)) {
            throw new \InvalidArgumentException(Str::format(
                'Instance for the service "%s" should be an object.',
                $name
            ));
        }
        $this->instances[$name] = $instance;
    }

    public function reset($name)
    {
        if (!$this->has($name)) {
            throw new \InvalidArgumentException(Str::format(
                'There is no instance for the service "%s".',
                $name
            ));
        }
        unset($this->instances[$name]);
    }

    public function get($name)
    {
        if (!$this->has($name)) {
            if (!$this->hasFactory($name)) {
                throw new \InvalidArgumentException(Str::format(
                    'There is no instance for the service "%s".',
                    $name
                ));
            }
            $this->instances[$name] = $this->getFactory($name)->createInstance();
        }

        return $this->instances[$name];
    }

    public function has($name)
    {
        return isset($this->instances[$name]);
    }
}