<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Language;

use AcronisCloud\Service\Locator;

trait IsoCodeAwareTrait
{
    /**
     * @return IsoCode
     */
    protected function getLanguageIsoCode()
    {
        return Locator::getInstance()->get(IsoCodeFactory::NAME);
    }
}