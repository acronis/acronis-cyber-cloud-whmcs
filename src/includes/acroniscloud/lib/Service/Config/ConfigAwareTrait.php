<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Config;

use AcronisCloud\Service\Locator;

trait ConfigAwareTrait
{
    /**
     * @return ConfigAccessor
     */
    protected function getConfig()
    {
        return Locator::getInstance()->get(ConfigFactory::NAME);
    }
}