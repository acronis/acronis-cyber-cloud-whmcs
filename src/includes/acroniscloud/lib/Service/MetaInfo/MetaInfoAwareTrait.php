<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\MetaInfo;

use AcronisCloud\Service\Locator;

trait MetaInfoAwareTrait
{
    /**
     * @return MetaInfo
     */
    protected function getMetaInfo()
    {
        return Locator::getInstance()->get(MetaInfoFactory::NAME);
    }
}