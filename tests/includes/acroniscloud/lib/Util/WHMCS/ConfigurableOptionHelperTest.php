<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Util\WHMCS;

class ConfigurableOptionHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider fullNameDataProvider
     */
    public function testGetFullName($expected, $name, $unit, $id, $friendlyName)
    {
        $helper = new ConfigurableOptionHelper();
        $this->assertEquals(
            $expected,
            $helper->getFullName($friendlyName, $name, $unit, $id),
            'Testing getting full name failed'
        );
    }

    /**
     * @dataProvider fullNameParseDataProvider
     */
    public function testParseFullName($expected, $fullName)
    {
        $helper = new ConfigurableOptionHelper();

        $this->assertEquals($expected, $helper->parseFullName($fullName), 'Testing parsing full name failed');
    }

    /**
     * @dataProvider nameDataProvider
     */
    public function testParseName($expected, $parseName)
    {
        $helper = new ConfigurableOptionHelper();

        $this->assertEquals($expected, $helper->parseName($parseName), 'Testing parsing name failed');
    }

    public function nameDataProvider()
    {
        return [
            [
                [
                    ConfigurableOptionHelper::OFFERING_ITEM_NAME => 'testItem',
                    ConfigurableOptionHelper::MEASUREMENT_UNIT => 'testUnit',
                    ConfigurableOptionHelper::INFRA_ID => '25',
                ],
                'testItem:testUnit:25',
            ],
            [
                [
                    ConfigurableOptionHelper::OFFERING_ITEM_NAME => 'testOtherItem',
                    ConfigurableOptionHelper::MEASUREMENT_UNIT => 'testOtherUnit',
                    ConfigurableOptionHelper::INFRA_ID => '35',
                ],
                'testOtherItem:testOtherUnit:35',
            ],
        ];
    }

    public function fullNameDataProvider()
    {
        return [
            ['testName:testUnit:25|testFriendlyName', 'testName', 'testUnit', '25', 'testFriendlyName'],
            ['otherName:someUnit:35|testFriendlyName', 'otherName', 'someUnit', '35', 'testFriendlyName'],
        ];
    }

    public function fullNameParseDataProvider()
    {
        return [
            [
                [
                    ConfigurableOptionHelper::NAME => 'testName',
                    ConfigurableOptionHelper::FRIENDLY_NAME => 'testFriendlyName',
                ],
                'testName|testFriendlyName',
            ],
            [
                [
                    ConfigurableOptionHelper::NAME => 'otherName',
                    ConfigurableOptionHelper::FRIENDLY_NAME => 'someFriendlyName',
                ],
                'otherName|someFriendlyName',
            ],
        ];
    }
}