<?php

/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Config;

class ConfigCacheSettingsTest extends ConfigSectionTest
{
    protected $configData = [
        'cache' => [
            'enabled' => true,
        ],
    ];

    public function testGetEnabled()
    {
        $this->assertEquals(true, $this->getConfig()->getCacheSettings()->getEnabled());
    }

    public function testDefaultValue()
    {
        $this->setupConfigData([]);
        $this->assertEquals(false, $this->getConfig()->getCacheSettings()->getEnabled());
    }
}