<?php
/**
 * @Copyright © 2002-2018 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\UsageReport;

use AcronisCloud\Service\Locator;
use Acronis\UsageReport\Manager;

trait UsageReportManagerAwareTrait
{
    /**
     * @return Manager
     */
    protected function getUsageReportManager()
    {
        return Locator::getInstance()->get(UsageReportManagerFactory::NAME);
    }
}