<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Util;


class StrTest extends \PHPUnit_Framework_TestCase
{

    public function testFormat()
    {
        $this->assertEquals('hello', Str::format('hello'));
        $this->assertEquals(
            'hello John and James',
            Str::format('hello %s and %s', 'John', 'James')
        );
        $this->assertEquals(
            'hello John and James',
            Str::format(
                'hello :manager_name and :developer_name',
                array(':manager_name' => 'John', ':developer_name' => 'James')
            )
        );
    }

    public function testStartsWith()
    {
        $this->assertTrue(Str::startsWith('hello', 'h'));
        $this->assertTrue(Str::startsWith('hello', 'he'));
        $this->assertTrue(Str::startsWith('hello', 'hell'));
        $this->assertTrue(Str::startsWith('hel.lo', 'hel.'));
        $this->assertTrue(Str::startsWith('h.el.lo', 'h.el.'));
        $this->assertTrue(Str::startsWith('hello', 'hello'));
        $this->assertTrue(Str::startsWith('', ''));
        $this->assertFalse(Str::startsWith('hello', 'o'));
        $this->assertFalse(Str::startsWith('hello', 'lo'));
        $this->assertFalse(Str::startsWith('hel.l.o', '.l.o'));
        $this->assertFalse(Str::startsWith('hel.l.o', '.o'));
    }

    public function testEndsWith()
    {
        $this->assertFalse(Str::endsWith('hello', 'h'));
        $this->assertFalse(Str::endsWith('hello', 'he'));
        $this->assertFalse(Str::endsWith('hello', 'hell'));
        $this->assertFalse(Str::endsWith('hello', 'll'));
        $this->assertTrue(Str::endsWith('', ''));
        $this->assertTrue(Str::endsWith('hello', 'o'));
        $this->assertTrue(Str::endsWith('hello', 'lo'));
        $this->assertTrue(Str::endsWith('hello', 'hello'));
        $this->assertTrue(Str::endsWith('hel.l.o', '.l.o'));
        $this->assertTrue(Str::endsWith('hel.l.o', '.o'));
    }

    /**
     * @dataProvider findTagDataProvider
     */
    public function testFindTag($string, $tagRegexp, $intialOffset, $groupNumnber, $expected)
    {
        $tagData = Str::findTag($string, $tagRegexp, $intialOffset, $groupNumnber);

        $this->assertEquals($expected, $tagData);
    }

    public function findTagDataProvider()
    {
        return [
            // It just works
            'simple_tag'                => ['<tag>', '/\<tag\>/', 0, 0, ['tag' => '<tag>', 'start_position' => 0, 'end_position' => 5]],
            // Method returns character offset, not byte
            'tag_with_unicode'          => ['Ää <tag> Ää', '/\<tag\>/', 0, 0, ['tag' => '<tag>', 'start_position' => 3, 'end_position' => 8]],
            // Method is not catching tags before initial offset: AB<tag> <tag></tag> |<tag> cd (| means initial offset position)
            'tag_with_offset'           => ['AB<tag> <tag></tag> <tag> cd', '/\<tag\>/', 20, 0, ['tag' => '<tag>', 'start_position' => 20, 'end_position' => 25]],
            // Initial offset works for multi-byte string: ÄÄ<tag> <tag></tag> |<tag> cd, not ÄÄ<tag> <tag></tag> <t|ag> cd
            'tag_with_offset_multibyte' => ['ÄÄ<tag> <tag></tag> <tag> cd', '/\<tag\>/', 20, 0, ['tag' => '<tag>', 'start_position' => 20, 'end_position' => 25]],
            // Tag regexp is not using exact match, however we are getting exact tag:
            'tag_not_strict'            => ['12345<taAAAAAg>', '/\<taA*g\>/', 0, 0, ['tag' => '<taAAAAAg>', 'start_position' => 5, 'end_position' => 15]],
            // We are capturing group number 1
            'tag_first_group'           => ['special<taAAAAAg>data', '/special(\<taA*g\>)data/', 0, 1, ['tag' => '<taAAAAAg>', 'start_position' => 7, 'end_position' => 17]],
            // When tag isn't found, method returns null:
            'no_tag'                    => ['<foo>', '/\<bar\>/', 0, 1, null],
        ];
    }
}