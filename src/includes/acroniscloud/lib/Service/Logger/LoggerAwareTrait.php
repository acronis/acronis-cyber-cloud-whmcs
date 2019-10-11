<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
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