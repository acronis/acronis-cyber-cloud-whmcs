<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Config;

use AcronisCloud\Service\Locator;

trait AddonConfigAwareTrait
{
    /**
     * @return AddonConfigAccessor
     */
    protected function getAddonConfig()
    {
        return Locator::getInstance()->get(AddonConfigFactory::NAME);
    }
}