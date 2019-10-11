<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Localization;

use Symfony\Component\Translation\Translator;

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
                'en_US',
                ['Test message.' => 'Test message.',],
            ],
            'Message with numbers' => [
                'de_DE',
                ['Test message with numbers 123456.' => 'Testnachricht mit den Nummern 123456.',],
            ],
            'Upper case message' => [
                'ja_JP',
                ['TEST MESSAGE ALL CAPS' => 'テストメッセージすべて大文字',],
            ],
            'Message without translation' => [
                'es_ES',
                ['Message without translation' => '',],
            ],
            'Two messages and translations' => [
                'fr_FR',
                [
                    'First message' => 'First translation',
                    'Second message' => 'Second translation',
                ],
            ],
            'Locale without translation file' => [
                'not_existing_locale',
                [
                    'Test message without translation' => 'Test message without translation',
                ],
            ],
        ];
    }

    public function testCreateInstance()
    {
        $translatorFactory = new TranslatorFactory;

        $this->assertInstanceOf(Translator::class, $translatorFactory->createInstance());
    }

    public function testCheckDefaultLocale()
    {
        global $_ADMINLANG;
        unset($_ADMINLANG['locale']);
        $translatorFactory = new TranslatorFactory;

        $locale = $translatorFactory->createInstance()->getLocale();

        $this->assertEquals('en_US', $locale);
    }

    /**
     * @dataProvider localesProvider
     */
    public function testCheckInvalidLocale($locale, $isValid)
    {
        $this->setAdminLocale($locale);
        $translatorFactory = new TranslatorFactory;

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
        $this->setAdminLocale($locale);
        $translatorFactory = new TranslatorFactory;

        $result = $translatorFactory->createInstance();

        $this->assertEquals($locale, $result->getLocale());
    }

    /**
     * @dataProvider localesMessageProvider
     */
    public function testCheckTranslationMessage($locale, $messages)
    {
        $this->setAdminLocale($locale);
        $translatorFactory = new TranslatorFactory;

        foreach ($messages as $message => $translation) {
            $content[$message] = $translatorFactory->createInstance()->trans($message);
        }
        $this->assertEquals($messages, $content);
    }

    private function setAdminLocale($locale)
    {
        global $_ADMINLANG;
        $_ADMINLANG['locale'] = $locale;
    }
}
