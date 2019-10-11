<?php

/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Config;

class ConfigLoggerSettingsTest extends ConfigSectionTest
{
    protected $configData = [
        'logger' => [
            'enabled' => true,
            'filename' => '/var/log/acroniscloud/acronis-cloud.log',
            'level' => 'DEBUG',
        ],
    ];

    public function testGetEnabled()
    {
        $this->assertEquals(true, $this->getConfig()->getLoggerSettings()->getEnabled());
    }

    /**
     * @dataProvider logLevelDataProvider
     * @param $configData
     * @param $expected
     */
    public function testGetLevel($configData, $expected)
    {
        $this->setupConfigData($configData);
        $this->assertEquals($expected, $this->getConfig()->getLoggerSettings()->getLevel());
    }

    public function testGetFilename()
    {
        $this->assertEquals('/var/log/acroniscloud/acronis-cloud.log',
            $this->getConfig()->getLoggerSettings()->getFilename());
    }

    public function testDefaultValues()
    {
        $this->setupConfigData([]);
        $this->assertEquals(false, $this->getConfig()->getLoggerSettings()->getEnabled());
        $this->assertEquals(250, $this->getConfig()->getLoggerSettings()->getLevel());
        $this->assertEquals('', $this->getConfig()->getLoggerSettings()->getFilename());
    }

    public function logLevelDataProvider()
    {
        return [
            'LOGGER DEBUG LEVEL TEST' => [
                [
                    'logger' => ['level' => 'DEBUG'],
                ],
                100,
            ],
            'LOGGER INFO LEVEL TEST' => [
                [
                    'logger' => ['level' => 'INFO'],
                ],
                200,
            ],
            'LOGGER NOTICE LEVEL TEST' => [
                [
                    'logger' => ['level' => 'NOTICE'],
                ],
                250,
            ],
            'LOGGER WARNING LEVEL TEST' => [
                [
                    'logger' => ['level' => 'WARNING'],
                ],
                300,
            ],
            'LOGGER ERROR LEVEL TEST' => [
                [
                    'logger' => ['level' => 'ERROR'],
                ],
                400,
            ],
            'LOGGER CRITICAL LEVEL TEST' => [
                [
                    'logger' => ['level' => 'CRITICAl'],
                ],
                500,
            ],
            'LOGGER ALERT LEVEL TEST' => [
                [
                    'logger' => ['level' => 'ALERT'],
                ],
                550,
            ],
            'LOGGER EMERGENCY LEVEL TEST' => [
                [
                    'logger' => ['level' => 'EMERGENCY'],
                ],
                600,
            ],
            'LOGGER DEFAULT LEVEL TEST' => [
                [
                    'logger' => ['level' => 'WRONG LEVEL'],
                ],
                250,
            ],
        ];
    }
}
