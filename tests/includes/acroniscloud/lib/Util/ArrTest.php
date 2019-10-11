<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Util;

use ArrayAccess;
use stdClass;

class ClassWithMagicMethodIsset
{
    public function __isset($property)
    {
    }
}

class ArrTest extends \PHPUnit_Framework_TestCase
{
    public function dataProviderDecode()
    {
        return [
            //'test name' => [expected, data],
            'empty' => [[], ''],
            'key only' => [['key' => null], 'key'],
            'key=value' => [['key' => 'value'], 'key=value'],
            'key=value;key1' => [['key' => 'value', 'key1' => null], 'key=value;key1'],
            'key1=value1;key2=value2' => [['key1' => 'value1', 'key2' => 'value2'], 'key1=value1;key2=value2'],
            'ke\;y=va\;lue' => [['ke;y' => 'va;lue'], 'ke\;y=va\;lue'],
            'ke\=y=va\=lue' => [['ke=y' => 'va=lue'], 'ke\=y=va\=lue'],
            'ke\ny=va\nlue' => [["ke\ny" => "va\nlue"], "ke\ny=va\nlue"],
        ];
    }

    public function dataProviderEncode()
    {
        return [
            //'test name' => [expected, data],
            'empty' => ['', []],
            'key only' => ['key', ['key' => null]],
            'key=value' => ['key=value', ['key' => 'value']],
            'key=value;key1' => ['key=value;key1', ['key' => 'value', 'key1' => null]],
            'key1=value1;key2=value2' => ['key1=value1;key2=value2', ['key1' => 'value1', 'key2' => 'value2']],
            'ke\;y=va\;lue' => ['ke\;y=va\;lue', ['ke;y' => 'va;lue']],
            'ke\=y=va\=lue' => ['ke\=y=va\=lue', ['ke=y' => 'va=lue']],
            'ke\ny=va\nlue' => ["ke\ny=va\nlue", ["ke\ny" => "va\nlue"]],
        ];
    }

    public function dataProviderHas()
    {
        $arrayAccess = $this->getMockBuilder(ArrayAccess::class)
            ->getMock();
        $arrayAccess->expects($this->once())
            ->method('offsetExists')
            ->with('key')
            ->willReturn(true);

        $arrayIsset = $this->getMockBuilder(ClassWithMagicMethodIsset::class)
            ->setMethods(['__isset'])
            ->getMock();
        $arrayIsset->expects($this->once())
            ->method('__isset')
            ->with('key')
            ->willReturn(true);

        $buildStdClass = function (array $properties = []) {
            $object = new stdClass();
            foreach ($properties as $property => $value) {
                $object->{$property} = $value;
            }

            return $object;
        };

        return [
            // 'test name' => [key, data, expected]

            'ArrayAccess' => ['key', $arrayAccess, true],

            '__isset' => ['key', $arrayIsset, true],

            'array false value' => ['key', ['key' => false], true],
            'array zero value' => ['key', ['key' => 0], true],
            'array null value' => ['key', ['key' => null], true],
            'array empty value' => ['key', ['key' => ''], true],
            'array key not exists' => ['key', ['another_key' => ''], false],
            'array empty' => ['key', [], false],

            'object false value' => ['key', (object)['key' => false], true],
            'object zero value' => ['key', (object)['key' => 0], true],
            'object null value' => ['key', (object)['key' => null], true],
            'object empty value' => ['key', (object)['key' => ''], true],
            'object key not exists' => ['key', (object)['another_key' => ''], false],
            'object empty' => ['key', (object)[], false],

            'stdClass false value' => ['key', $buildStdClass(['key' => false]), true],
            'stdClass zero value' => ['key', $buildStdClass(['key' => 0]), true],
            'stdClass null value' => ['key', $buildStdClass(['key' => null]), true],
            'stdClass empty value' => ['key', $buildStdClass(['key' => '']), true],
            'stdClass key not exists' => ['key', $buildStdClass(['another_key' => '']), false],
            'stdClass empty' => ['key', $buildStdClass([]), false],
        ];
    }

    public function dataProviderGetNested()
    {
        $arrayIsset = $this->getMockBuilder(ClassWithMagicMethodIsset::class)
            ->setMethods(['__isset'])
            ->setConstructorArgs([])
            ->getMock();
        $arrayIsset->expects($this->once())
            ->method('__isset')
            ->with('d_key')
            ->willReturn(true);
        $arrayIsset->d_key = 'bar';

        $arrayAccess = $this->getMockBuilder(ArrayAccess::class)
            ->getMock();
        $arrayAccess->expects($this->once())
            ->method('offsetExists')
            ->with('c_key')
            ->willReturn(true);
        $arrayAccess->expects($this->once())
            ->method('offsetGet')
            ->with('c_key')
            ->willReturn($arrayIsset);

        return [
            'default value contains specified path' => [
                ['a_key', 'b_key'],
                ['a_key' => 'foo'],
                ['a_key' => 'b_key'],
                ['a_key' => 'b_key'],
            ],
            'depth 2, has nested property' => [['a_key', 'b_key'], ['a_key' => ['b_key' => 'bar']], 'baz', 'bar'],
            'depth 2, default value' => [['a_key', 'b_key'], ['a_key' => ['bbbb_key' => 'bar']], 'baz', 'baz'],
            'mixed data (object, array, array access, array isset)' => [
                ['a_key', 'b_key', 'c_key', 'd_key'],
                (object)['a_key' => ['b_key' => $arrayAccess]],
                'baz',
                'bar',
            ],
        ];
    }

    public function dataProviderGet()
    {
        $buildArrayAccess = function (array $properties = [], array $notExistProperties = []) {
            $object = $this->getMockBuilder(ArrayAccess::class)
                ->getMock();
            foreach ($properties as $property => $value) {
                $object->expects($this->once())
                    ->method('offsetExists')
                    ->with($property)
                    ->willReturn(true);

                $object->expects($this->once())
                    ->method('offsetGet')
                    ->with($property)
                    ->willReturn($value);
            }

            foreach ($notExistProperties as $property) {
                $object->expects($this->once())
                    ->method('offsetExists')
                    ->with($property)
                    ->willReturn(false);
            }

            return $object;
        };

        $obj = new stdClass();

        return [

            'ArrayAccess zero value' => ['key', $buildArrayAccess(['key' => 0]), $this->anything(), 0],
            'ArrayAccess empty value' => ['key', $buildArrayAccess(['key' => '']), $this->anything(), ''],
            'ArrayAccess false value' => ['key', $buildArrayAccess(['key' => false]), $this->anything(), false],
            'ArrayAccess null value' => ['key', $buildArrayAccess(['key' => null]), $this->anything(), null],
            'ArrayAccess array value' => ['key', $buildArrayAccess(['key' => []]), $this->anything(), []],
            'ArrayAccess object value' => ['key', $buildArrayAccess(['key' => $obj]), $this->anything(), $obj],

            'ArrayAccess default zero value' => ['key', $buildArrayAccess([], ['key']), 0, 0],
            'ArrayAccess default empty value' => ['key', $buildArrayAccess([], ['key']), '', ''],
            'ArrayAccess default false value' => ['key', $buildArrayAccess([], ['key']), false, false],
            'ArrayAccess default null value' => ['key', $buildArrayAccess([], ['key']), null, null],
            'ArrayAccess default array value' => ['key', $buildArrayAccess([], ['key']), [], []],
            'ArrayAccess default object value' => ['key', $buildArrayAccess([], ['key']), $obj, $obj],

            'array zero value' => ['key', ['key' => 0], $this->anything(), 0],
            'array empty value' => ['key', ['key' => ''], $this->anything(), ''],
            'array false value' => ['key', ['key' => false], $this->anything(), false],
            'array null value' => ['key', ['key' => null], $this->anything(), null],
            'array array value' => ['key', ['key' => []], $this->anything(), []],
            'array object value' => ['key', ['key' => $obj], $this->anything(), $obj],

            'array default zero value' => ['key', [], 0, 0],
            'array default empty value' => ['key', [], '', ''],
            'array default false value' => ['key', [], false, false],
            'array default null value' => ['key', [], null, null],
            'array default array value' => ['key', [], [], []],
            'array default object value' => ['key', [], $obj, $obj],

            'object zero value' => ['key', (object)['key' => 0], $this->anything(), 0],
            'object empty value' => ['key', (object)['key' => ''], $this->anything(), ''],
            'object false value' => ['key', (object)['key' => false], $this->anything(), false],
            'object null value' => ['key', (object)['key' => null], $this->anything(), null],
            'object array value' => ['key', (object)['key' => []], $this->anything(), []],
            'object object value' => ['key', (object)['key' => $obj], $this->anything(), $obj],

            'object default zero value' => ['key', (object)[], 0, 0],
            'object default empty value' => ['key', (object)[], '', ''],
            'object default false value' => ['key', (object)[], false, false],
            'object default null value' => ['key', (object)[], null, null],
            'object default array value' => ['key', (object)[], [], []],
            'object default object value' => ['key', (object)[], $obj, $obj],
        ];
    }

    public function dataProviderGetByPath()
    {
        $arrayAccess = $this->getMockBuilder(ArrayAccess::class)
            ->getMock();

        $arrayAccess->expects($this->exactly(2))
            ->method('offsetExists')
            ->with('path')
            ->willReturn(true);

        $arrayAccess->expects($this->once())
            ->method('offsetGet')
            ->with('path')
            ->willReturn(1);

        return [
            'path is present' => [
                ['contact' => ['email' => 'test_user@acronis.com']],
                'contact.email',
                'test_user@acronis.com',
            ],
            'array is object' => [(object)['one' => 1], 'one', 1],
            'array is ArrayAccess' => [$arrayAccess, 'path', 1],
            'path is missing' => [[], 'path.missing', null],
            'path is missing with default' => [[], 'path.missing', 'expectedValue', 'expectedValue'],
            'array is object with missing path' => [new \stdClass(), 'path', null],
            'array is string' => ['test', '0', null],
            'sub-path is missing' => [['exists' => true], 'exists.not_exists', null],
            'sub-path is object' => [['exists' => new \stdClass()], 'exists.not_exists', null],
            'empty path' => [['one' => 1], '', null],
        ];
    }

    public function dataProviderFind()
    {
        return [
            'not found' => [
                null,
                [1, 2, 2, 3, 4, 5],
                function ($item) {
                    return $item === 0;
                },
            ],
            'value equals 1' => [
                1,
                [1, 2, 2, 3, 4, 5],
                function ($item) {
                    return $item === 1;
                },
            ],
            'value equals 2' => [
                2,
                [1, 2, 2, 4, 5],
                function ($item) {
                    return $item === 2;
                },
            ],
            'key equals 3' => [
                4,
                [1, 2, 2, 4, 5],
                function ($item, $key) {
                    return $key === 3;
                },
            ],
            'key equals e' => [
                5,
                ['a' => 1, 'b' => 2, 'c' => 2, 'd' => 4, 'e' => 5],
                function ($item, $key) {
                    return $key === 'e';
                },
            ],
            'previous values equals 4' => [
                5,
                [1, 2, 2, 4, 5],
                function ($item, $key, &$array) {
                    return $key && $array[$key - 1] === 4;
                },
            ],
        ];
    }

    /**
     * @dataProvider dataProviderDecode
     */
    public function testDecode($expected, $data)
    {
        $this->assertEquals($expected, Arr::decode($data));
    }

    /**
     * @dataProvider dataProviderEncode
     */
    public function testEncode($expected, $data)
    {
        $this->assertEquals($expected, Arr::encode($data));
    }

    /**
     * @dataProvider dataProviderHas
     */
    public function testHas($key, $data, $expected)
    {
        $this->assertEquals($expected, Arr::has($data, $key));
    }

    /**
     * @dataProvider dataProviderGet
     */
    public function testGet($key, $data, $defaultValue, $expected)
    {
        $this->assertEquals($expected, Arr::get($data, $key, $defaultValue));
    }

    /**
     * @dataProvider dataProviderGetByPath
     */
    public function testGetByPath($array, $path, $expected, $default = false)
    {
        $result = $default === false
            ? Arr::getByPath($array, $path)
            : Arr::getByPath($array, $path, $default);

        $this->assertEquals($expected, $result);
    }

    public function testMerge()
    {
        $limits = ['a' => 11, 'b' => 22, 'c' => 33, 'd' => 44,];
        $usages = ['a' => 1, 'b' => 2, 'c' => 3, 'e' => 4,];
        $expected = [
            'a' => ['limit' => 11, 'usage' => 1,],
            'b' => ['limit' => 22, 'usage' => 2,],
            'c' => ['limit' => 33, 'usage' => 3,],
            'd' => ['limit' => 44, 'usage' => 0,],
            'e' => ['usage' => 4,],
        ];

        $counters = Arr::merge([
            'limit' => $limits,
            'usage' => $usages,
        ], [
            'usage' => 0,
        ]);

        $this->assertEquals($expected, $counters);
    }

    public function testMap()
    {
        $countries = [
            ['country_code' => 'US', 'currency_code' => 'USD', 'name' => 'United States',],
            ['country_code' => 'RU', 'currency_code' => 'RUB', 'name' => 'Russia',],
            ['country_code' => 'FR', 'currency_code' => 'EUR', 'name' => 'France',],
        ];
        $expected = [
            'US' => 'USD',
            'RU' => 'RUB',
            'FR' => 'EUR',
        ];

        $currencies = Arr::map($countries, 'country_code', 'currency_code');

        $this->assertEquals($expected, $currencies);
    }

    public function testMapKeys()
    {
        $data = [
            'key1' => 'value1',
            'key2' => 'value2',
            'key3' => 'value3',
        ];
        $expected = [
            'value1' => 'key1',
            'value2' => 'key2',
            'value3' => 'key3',
        ];

        $result = Arr::map(
            $data,
            function ($value) {
                return $value;
            },
            function ($value, $key) {
                return $key;
            }
        );

        $this->assertEquals($expected, $result);
    }

    /**
     * @dataProvider dataProviderFind
     */
    public function testFind($expected, $array, $fn)
    {
        $this->assertEquals($expected, Arr::find($array, $fn));
    }
}
