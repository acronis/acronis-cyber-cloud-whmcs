<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
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