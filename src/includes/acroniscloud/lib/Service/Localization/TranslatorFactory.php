<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Localization;

use AcronisCloud\Service\FactoryInterface;
use AcronisCloud\Util\Arr;
use Symfony\Component\Translation\Loader\PoFileLoader;
use Symfony\Component\Translation\Translator;

class TranslatorFactory implements FactoryInterface
{
    const NAME = 'l10n';

    const FILE_FORMAT = 'po';
    const LOCALE_EN_US = 'en_US';

    /**
     * @return Translator
     */
    public function createInstance()
    {
        $whmcsLocale = $this->getLocale();
        $translator = new Translator($whmcsLocale);
        $translator->addLoader(static::FILE_FORMAT, new PoFileLoader());

        $translations = $this->getTranslations();
        foreach ($translations as $locale => $file) {
            $translator->addResource(static::FILE_FORMAT, $file, $locale);
        }

        return $translator;
    }

    /**
     * @return array
     */
    private function getTranslations()
    {
        $extension = '.' . static::FILE_FORMAT;
        $translations = [];
        foreach (glob(ACRONIS_CLOUD_L10N_DIR . '/*' . $extension) as $file) {
            $locale = basename($file, $extension);
            $translations[$locale] = $file;
        }

        return $translations;
    }

    private function getLocale()
    {
        global $_ADMINLANG;

        // TODO: investigate how to get locale for Client Area
        return Arr::get($_ADMINLANG, 'locale', static::LOCALE_EN_US);
    }
}