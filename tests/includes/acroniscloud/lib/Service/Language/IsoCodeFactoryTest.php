<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Language;

class IsoCodeFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function languagesProvider()
    {
        return [
            'Lowercase language name' => ['english', 'en'],
            'Language with white spaces' => [' english ', 'en'],
            'Language name not in lowercase' => ['eNgLiSh', 'en'],
            'Language name not in uppercase' => ['ENGLISH', 'en'],
            'Another language name' => ['russian', 'ru'],
            'Unsupported language' => ['php', null],
        ];
    }

    public function testCreateInstance()
    {
        $factory = new IsoCodeFactory();

        $this->assertInstanceOf(IsoCode::class, $factory->createInstance());
    }

    /**
     * @dataProvider languagesProvider
     */
    public function testCreateInstanceFile($name, $code)
    {
        $factory = new IsoCodeFactory();
        
        $this->assertEquals($code, $factory->createInstance()->getCode($name));
    }
}
