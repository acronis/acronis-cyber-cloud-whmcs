<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
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