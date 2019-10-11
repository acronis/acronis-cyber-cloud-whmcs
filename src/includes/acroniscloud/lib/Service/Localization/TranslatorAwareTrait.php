<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
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