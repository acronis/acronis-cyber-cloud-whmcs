<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Localization;

use AcronisCloud\Service\Locator;
use Symfony\Component\Translation\Translator;

trait TranslatorAwareTrait
{
    /**
     * @return Translator
     */
    protected function getTranslator()
    {
        return Locator::getInstance()->get(TranslatorFactory::NAME);
    }
}