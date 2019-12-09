<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Logger;

use AcronisCloud\Service\Locator;
use Monolog\Logger;

trait LoggerAwareTrait
{
    /**
     * @return Logger
     */
    protected function getLogger()
    {
        return Locator::getInstance()->get(LoggerFactory::NAME);
    }
}