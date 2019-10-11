<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
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