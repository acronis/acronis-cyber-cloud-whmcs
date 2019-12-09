<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Repository;

use AcronisCloud\Repository\WHMCS\ConfigurationRepository;

class ConfigurationRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function SettingsProvider()
    {
        return [
            'Settings with language' => [true, 'English'],
            'Settings with bool value' => [true, true],
            'No such setting in DB' => [false, null],
        ];
    }

    public function testGetLanguage()
    {
        $setting = new \stdClass();
        $setting->value = 'English';

        $findSettingMock = $this->getMockObjectGenerator()->getMock(ConfigurationRepository::class, ['findBySetting']);
        $findSettingMock->expects($this->once())
            ->method('findBySetting')
            ->with(ConfigurationRepository::SETTING_LANGUAGE)
            ->will($this->returnValue($setting));

        $this->assertEquals('English', $findSettingMock->getLanguage());
    }

    /**
     * @dataProvider SettingsProvider
     */
    public function testGetSettingValue($settingExist, $expected)
    {
        $setting = null;
        if ($settingExist) {
            $setting = new \stdClass();
            $setting->value = $expected;
        }

        $findSettingMock = $this->getMockObjectGenerator()->getMock(ConfigurationRepository::class, ['findBySetting']);
        $findSettingMock->expects($this->once())
            ->method('findBySetting')
            ->will($this->returnValue($setting));

        $this->assertEquals($expected, $findSettingMock->getSettingValue('fakeData'));
    }
}
