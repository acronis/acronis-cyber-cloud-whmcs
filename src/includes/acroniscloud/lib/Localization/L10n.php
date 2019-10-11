<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Localization;

use AcronisCloud\Service\Localization\TranslatorFactory;
use AcronisCloud\Service\Locator;
use Symfony\Component\Translation\Translator;

class L10n
{
    /**
     * @param string $message
     * @param array $placeholders
     * @return string
     */
    public static function gettext($message, array $placeholders = [])
    {
        $parameters = [];
        foreach ($placeholders as $key => $value) {
            $parameters[static::formatPlaceholderKey($key)] = $value;
        }

        $translator = static::getTranslator();

        return $translator->trans($message, $parameters);
    }

    /**
     * @param string
     */
    public static function setLocale($locale)
    {
        static::getTranslator()->setLocale($locale);
    }

    /**
     * @return string
     */
    public static function getLocale()
    {
        return static::getTranslator()->getLocale();
    }

    /**
     * @return Translator
     */
    public static function getTranslator()
    {
        return Locator::getInstance()->get(TranslatorFactory::NAME);
    }

    /**
     * @param string $key
     * @return string
     */
    private static function formatPlaceholderKey($key)
    {
        return '{' . $key . '}';
    }
}