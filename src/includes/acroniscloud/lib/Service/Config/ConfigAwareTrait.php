<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
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