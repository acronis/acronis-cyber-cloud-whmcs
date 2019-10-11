<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Cache;

use AcronisCloud\Service\Locator;

trait CacheAwareTrait
{
    /**
     * @return CacheInterface
     */
    protected function getCache()
    {
        return Locator::getInstance()->get(CacheFactory::NAME);
    }
}