<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
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