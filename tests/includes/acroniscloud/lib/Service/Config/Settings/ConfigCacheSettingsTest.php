<?php

/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
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