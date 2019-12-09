<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Config;

class ConfigFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateInstance()
    {
        $factory = new ConfigFactory();
        $this->assertInstanceOf(ConfigAccessor::class, $factory->createInstance());
    }
}