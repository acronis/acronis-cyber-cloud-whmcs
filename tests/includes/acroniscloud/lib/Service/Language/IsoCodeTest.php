<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Language;

class IsoCodeTest extends \PHPUnit_Framework_TestCase
{
    private $supportedLanguages = [
        'english' => 'en',
        'russian' => 'ru',
        'macedonian' => 'mk',
        'norwegian' => 'no',
        'portuguese-br' => 'pt-BR',
        'portuguese-pt' => 'pt-PT',
        'romanian' => 'ro',
    ];

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

    public function defaultLanguagesProvider()
    {
        return [
            'Real language name' => ['russian', 'ru'],
            'Unsupported language' => ['php', 'ru'],
            'Wrong language name' => ['123141//918', 'en'],
            'No language' => ['', 'jp'],
        ];
    }

    /**
     * @dataProvider languagesProvider
     */
    public function testGetCode($name, $code)
    {
        $IsoCode = new IsoCode($this->supportedLanguages);

        $this->assertEquals($code, $IsoCode->getCode($name));
    }

    /**
     * @dataProvider defaultLanguagesProvider
     */
    public function testGetCodeDefault($name, $defaultCode)
    {
        $IsoCode = new IsoCode($this->supportedLanguages);

        $this->assertEquals($defaultCode, $IsoCode->getCode($name, $defaultCode));
    }
}
