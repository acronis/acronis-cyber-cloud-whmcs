<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Util;

class UomConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider storageConverterProvider
     * @param $value
     * @param $from
     * @param $to
     * @param $expected
     */
    public function testStorageConverter($value, $from, $to, $expected)
    {
        $actual = UomConverter::convertStorage($value, $from, $to);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testStorageConverterInvalidFrom()
    {
        $from = 'INVALID';
        UomConverter::convertStorage(3, $from, UomConverter::BYTES);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testStorageConverterInvalidTo()
    {
        $to = 'INVALID';
        UomConverter::convertStorage(3, UomConverter::BYTES, $to);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTimeConverterInvalidFrom()
    {
        $from = 'INVALID';
        UomConverter::convertTime(3, $from, UomConverter::SECONDS);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTimeConverterInvalidTo()
    {
        $to = 'INVALID';
        UomConverter::convertTime(3, UomConverter::SECONDS, $to);
    }

    /**
     * @dataProvider timeConverterProvider
     * @param $value
     * @param $from
     * @param $to
     * @param $expected
     */
    public function testTimeConverter($value, $from, $to, $expected)
    {
        $actual = UomConverter::convertTime($value, $from, $to);

        $this->assertEquals($expected, $actual);
    }

    public function storageConverterProvider()
    {
        $value = 123.456;

        return [
            // 'Name' => [Value, From, To, Expected],
            'zero bytes' => [0, UomConverter::BYTES, UomConverter::BYTES, 0],
            'zero kilobytes' => [0, UomConverter::KILOBYTES, UomConverter::KILOBYTES, 0],
            'zero megabytes' => [0, UomConverter::MEGABYTES, UomConverter::MEGABYTES, 0],
            'zero gigabytes' => [0, UomConverter::GIGABYTES, UomConverter::GIGABYTES, 0],
            'zero terabytes' => [0, UomConverter::TERABYTES, UomConverter::TERABYTES, 0],

            'bytes to bytes' => [$value, UomConverter::BYTES, UomConverter::BYTES, $value],
            'bytes to kilobytes' => [$value * pow(1024, 1), UomConverter::BYTES, UomConverter::KILOBYTES, $value],
            'bytes to megabytes' => [$value * pow(1024, 2), UomConverter::BYTES, UomConverter::MEGABYTES, $value],
            'bytes to gigabytes' => [$value * pow(1024, 3), UomConverter::BYTES, UomConverter::GIGABYTES, $value],
            'bytes to terabytes' => [$value * pow(1024, 4), UomConverter::BYTES, UomConverter::TERABYTES, $value],

            'kilobytes to bytes' => [$value, UomConverter::KILOBYTES, UomConverter::BYTES, $value * pow(1024, 1)],
            'kilobytes to kilobytes' => [$value, UomConverter::KILOBYTES, UomConverter::KILOBYTES, $value],
            'kilobytes to megabytes' => [
                $value * pow(1024, 1),
                UomConverter::KILOBYTES,
                UomConverter::MEGABYTES,
                $value,
            ],
            'kilobytes to gigabytes' => [
                $value * pow(1024, 2),
                UomConverter::KILOBYTES,
                UomConverter::GIGABYTES,
                $value,
            ],
            'kilobytes to terabytes' => [
                $value * pow(1024, 3),
                UomConverter::KILOBYTES,
                UomConverter::TERABYTES,
                $value,
            ],

            'megabytes to bytes' => [$value, UomConverter::MEGABYTES, UomConverter::BYTES, $value * pow(1024, 2)],
            'megabytes to kilobytes' => [
                $value,
                UomConverter::MEGABYTES,
                UomConverter::KILOBYTES,
                $value * pow(1024, 1),
            ],
            'megabytes to megabytes' => [$value, UomConverter::MEGABYTES, UomConverter::MEGABYTES, $value],
            'megabytes to gigabytes' => [
                $value * pow(1024, 1),
                UomConverter::MEGABYTES,
                UomConverter::GIGABYTES,
                $value,
            ],
            'megabytes to terabytes' => [
                $value * pow(1024, 2),
                UomConverter::MEGABYTES,
                UomConverter::TERABYTES,
                $value,
            ],

            'gigabytes to bytes' => [$value, UomConverter::GIGABYTES, UomConverter::BYTES, $value * pow(1024, 3)],
            'gigabytes to kilobytes' => [
                $value,
                UomConverter::GIGABYTES,
                UomConverter::KILOBYTES,
                $value * pow(1024, 2),
            ],
            'gigabytes to megabytes' => [
                $value,
                UomConverter::GIGABYTES,
                UomConverter::MEGABYTES,
                $value * pow(1024, 1),
            ],
            'gigabytes to gigabytes' => [$value, UomConverter::GIGABYTES, UomConverter::GIGABYTES, $value],
            'gigabytes to terabytes' => [
                $value * pow(1024, 1),
                UomConverter::GIGABYTES,
                UomConverter::TERABYTES,
                $value,
            ],

            'terabytes to bytes' => [$value, UomConverter::TERABYTES, UomConverter::BYTES, $value * pow(1024, 4)],
            'terabytes to kilobytes' => [
                $value,
                UomConverter::TERABYTES,
                UomConverter::KILOBYTES,
                $value * pow(1024, 3),
            ],
            'terabytes to megabytes' => [
                $value,
                UomConverter::TERABYTES,
                UomConverter::MEGABYTES,
                $value * pow(1024, 2),
            ],
            'terabytes to gigabytes' => [
                $value,
                UomConverter::TERABYTES,
                UomConverter::GIGABYTES,
                $value * pow(1024, 1),
            ],
            'terabytes to terabytes' => [$value, UomConverter::TERABYTES, UomConverter::TERABYTES, $value],
        ];
    }

    public function timeConverterProvider()
    {
        $value = 123.456;

        return [
            // 'Name' => [Value, From, To, Expected],
            'zero seconds' => [0, UomConverter::SECONDS, UomConverter::SECONDS, 0],
            'zero minutes' => [0, UomConverter::MINUTES, UomConverter::MINUTES, 0],
            'zero hours' => [0, UomConverter::HOURS, UomConverter::HOURS, 0],
            'zero days' => [0, UomConverter::DAYS, UomConverter::DAYS, 0],

            'seconds to seconds' => [$value, UomConverter::SECONDS, UomConverter::SECONDS, $value],
            'seconds to minutes' => [$value * 60, UomConverter::SECONDS, UomConverter::MINUTES, $value],
            'seconds to hours' => [$value * 60 * 60, UomConverter::SECONDS, UomConverter::HOURS, $value],
            'seconds to days' => [$value * 60 * 60 * 24, UomConverter::SECONDS, UomConverter::DAYS, $value],

            'minutes to seconds' => [$value, UomConverter::MINUTES, UomConverter::SECONDS, $value * 60],
            'minutes to minutes' => [$value, UomConverter::MINUTES, UomConverter::MINUTES, $value],
            'minutes to hours' => [$value * 60, UomConverter::MINUTES, UomConverter::HOURS, $value],
            'minutes to days' => [$value * 60 * 24, UomConverter::MINUTES, UomConverter::DAYS, $value],

            'hours to seconds' => [$value, UomConverter::HOURS, UomConverter::SECONDS, $value * 60 * 60],
            'hours to minutes' => [$value, UomConverter::HOURS, UomConverter::MINUTES, $value * 60],
            'hours to hours' => [$value, UomConverter::HOURS, UomConverter::HOURS, $value],
            'hours to days' => [$value * 24, UomConverter::HOURS, UomConverter::DAYS, $value],

            'days to seconds' => [$value, UomConverter::DAYS, UomConverter::SECONDS, $value * 60 * 60 * 24],
            'days to minutes' => [$value, UomConverter::DAYS, UomConverter::MINUTES, $value * 60 * 24],
            'days to hours' => [$value, UomConverter::DAYS, UomConverter::HOURS, $value * 24],
            'days to days' => [$value, UomConverter::DAYS, UomConverter::DAYS, $value],
        ];
    }
}