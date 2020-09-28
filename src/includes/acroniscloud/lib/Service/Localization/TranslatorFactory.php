<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Localization;

use AcronisCloud\Service\FactoryInterface;
use AcronisCloud\Util\WHMCS\Lang;
use Symfony\Component\Translation\Loader\PoFileLoader;
use AcronisCloud\Service\Localization\Translator as Translator;
use Symfony\Component\Translation\Translator as SymfonyTranslator;

class TranslatorFactory implements FactoryInterface
{
    const NAME = 'l10n';

    const FILE_FORMAT = 'po';

    /**
     * @return mixed
     */
    public function createInstance()
    {
        $whmcsLocale = $this->getLocale();
        $translator = $this->getTranslator($whmcsLocale);
        $translator->addLoader(static::FILE_FORMAT, new PoFileLoader());

        $translations = $this->getTranslations();
        foreach ($translations as $locale => $file) {
            $translator->addResource(static::FILE_FORMAT, $file, $locale);
        }

        return $translator;
    }

    /**
     * @return string
     */
    protected function getLocale()
    {
        return Lang::getLocale();
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

    /**
     * @param string $whmcsLocale
     * @return mixed
     */
    private function getTranslator($whmcsLocale)
    {
        if (class_exists('Illuminate\Translation\Translator')) {
            return new Translator($whmcsLocale);
        }

        return new SymfonyTranslator($whmcsLocale);
    }
}