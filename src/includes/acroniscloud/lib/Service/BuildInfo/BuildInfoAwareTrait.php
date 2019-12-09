<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\BuildInfo;

use AcronisCloud\Service\Locator;

trait BuildInfoAwareTrait
{
    /**
     * @return BuildInfoParser
     */
    protected function getBuildInfo()
    {
        return Locator::getInstance()->get(BuildInfoFactory::NAME);
    }
}