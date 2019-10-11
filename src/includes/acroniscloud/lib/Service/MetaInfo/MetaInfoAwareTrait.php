<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
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