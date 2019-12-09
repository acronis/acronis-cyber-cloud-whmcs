<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Config;

use AcronisCloud\Service\Config\Settings\CacheSettings;
use AcronisCloud\Service\Config\Settings\CloudApiSettings;
use AcronisCloud\Service\Config\Settings\LoggerSettings;

class ConfigAccessorTest extends \PHPUnit_Framework_TestCase
{
    public function testGetLoggerSettings()
    {
        $accessor = new ConfigAccessor($some_data = []);
        $this->assertInstanceOf(LoggerSettings::class, $accessor->getLoggerSettings());
    }

    public function testGetCacheSettings()
    {
        $accessor = new ConfigAccessor($some_data = []);
        $this->assertInstanceOf(CacheSettings::class, $accessor->getCacheSettings());
    }

    public function testGetCloudApiSettings()
    {
        $accessor = new ConfigAccessor($some_data = []);
        $this->assertInstanceOf(CloudApiSettings::class, $accessor->getCloudApiSettings());
    }
}