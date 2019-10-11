<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Localization;

use AcronisCloud\Service\Localization\TranslatorAwareTrait;

trait GetTextTrait
{
    use TranslatorAwareTrait;

    /**
     * @param string $message
     * @param array $placeholders
     * @return string
     */
    protected function gettext($message, array $placeholders = [])
    {
        return L10n::gettext($message, $placeholders);
    }

    /**
     * @param string
     */
    protected function setLocale($locale)
    {
        L10n::setLocale($locale);
    }

    /**
     * @return string
     */
    protected function getLocale()
    {
        return L10n::getLocale();
    }
}