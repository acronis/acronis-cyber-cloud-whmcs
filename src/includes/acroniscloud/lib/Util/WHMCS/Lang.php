<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Util\WHMCS;

use AcronisCloud\Util\Arr;

class Lang
{
    const LOCALE_EN_US = 'en_US';
    const LANGUAGES_REPLACEMENTS = [
        'no' => 'nb',
    ];

    public static function getLocale()
    {
        $locale = static::isAdmin()
            ? \AdminLang::trans('locale')
            : \Lang::trans('locale');

        return $locale ? static::normalizeLocale($locale) : static::LOCALE_EN_US;
    }

    public static function getLanguageLocale($language)
    {
        $whmcsLocales = \Lang::getLocales();
        $languagesCodes = Arr::map($whmcsLocales, 'language', 'locale', false);
        $locale = Arr::get($languagesCodes, $language);

        return $locale ? static::normalizeLocale($locale) : static::LOCALE_EN_US;
    }

    public static function trans($id, array $parameters = [], $domain = null, $locale = null)
    {
        return static::isAdmin()
            ? \AdminLang::trans($id, $parameters, $domain, $locale)
            : \Lang::trans($id, $parameters, $domain, $locale);
    }

    private static function normalizeLocale($locale)
    {
        $locale = str_replace('-', '_', $locale);
        $localeParts = explode('_', $locale, 2);
        $languageCode = strtolower(Arr::get($localeParts, 0));
        if (!$languageCode) {
            return static::LOCALE_EN_US;
        }

        // fixing WHMCS outdated names
        $languageCode = Arr::get(static::LANGUAGES_REPLACEMENTS, $languageCode, $languageCode);

        $countryCode = strtoupper(Arr::get($localeParts, 1));
        if (!$countryCode) {
            return $languageCode;
        }

        return $languageCode . '_' . $countryCode;
    }

    private static function isAdmin()
    {
        global $_ADMINLANG;

        return !empty($_ADMINLANG);
    }
}