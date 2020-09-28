<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Localization;

use AcronisCloud\Util\WHMCS\Lang;
use Illuminate\Translation\Translator;

class TranslatorFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function localesProvider()
    {
        return [
            'Delimiter / ' => ['en/US', false],
            'Delimiter %' => ['de%DE', false],
            'Number of chars as delimiter' => ['ja)&*()_JP', false],
            'Right locale name' => ['en_US', true],
            'Another locale name' => ['de_DE', true],
        ];
    }

    public function localesMessageProvider()
    {
        return [
            'Default locale' => [
                'en',
                ['Test message.' => 'Test message.',],
            ],
            'Message with numbers' => [
                'de',
                ['Test message with numbers 123456.' => 'Testnachricht mit den Nummern 123456.',],
            ],
            'Upper case message' => [
                'ja',
                ['TEST MESSAGE ALL CAPS' => 'テストメッセージすべて大文字',],
            ],
            'Message without translation' => [
                'es',
                ['Message without translation' => '',],
            ],
            'Two messages and translations' => [
                'fr',
                [
                    'First message' => 'First translation',
                    'Second message' => 'Second translation',
                ],
            ],
            'Locale without translation file' => [
                'notExistingLocale',
                [
                    'Test message without translation' => 'Test message without translation',
                ],
            ],
        ];
    }

    public function testCreateInstance()
    {
        $translatorFactory = $this->getTranslatorFactory();

        $this->assertInstanceOf(Translator::class, $translatorFactory->createInstance());
    }

    public function testCheckDefaultLocale()
    {
        $translatorFactory = $this->getTranslatorFactory();

        $locale = $translatorFactory->createInstance()->getLocale();

        $this->assertEquals(Lang::LOCALE_EN_US, $locale);
    }

    /**
     * @dataProvider localesProvider
     */
    public function testCheckInvalidLocale($locale, $isValid)
    {
        $translatorFactory = $this->getTranslatorFactory($locale);

        if (!$isValid) {
            $this->expectException(\InvalidArgumentException::class);
        }
        $obj = $translatorFactory->createInstance()->getLocale();
    }

    /**
     * @dataProvider localesMessageProvider
     */
    public function testCheckLocale($locale, $message)
    {
        $translatorFactory = $this->getTranslatorFactory($locale);

        $result = $translatorFactory->createInstance();

        $this->assertEquals($locale, $result->getLocale());
    }

    /**
     * @dataProvider localesMessageProvider
     */
    public function testCheckTranslationMessage($locale, $messages)
    {
        $translatorFactory = $this->getTranslatorFactory($locale);

        foreach ($messages as $message => $translation) {
            $content[$message] = $translatorFactory->createInstance()->trans($message);
        }
        $this->assertEquals($messages, $content);
    }

    /**
     * @param string $locale
     * @return \PHPUnit_Framework_MockObject_MockObject|TranslatorFactory
     */
    private function getTranslatorFactory($locale = Lang::LOCALE_EN_US)
    {
        $translatorFactory = $this->getMockBuilder(TranslatorFactory::class)
            ->setMethods(['getLocale'])
            ->getMock();
        $translatorFactory->expects($this->any())
            ->method('getLocale')
            ->will($this->returnValue($locale));

        return $translatorFactory;
    }
}
