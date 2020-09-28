<?php
/**
 * @Copyright © 2002-2018 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\UsageReport;

use AcronisCloud\Service\Config\ConfigSchema;
use Acronis\UsageReport\Manager;
use Acronis\UsageReport\Service\FileManager;
use AcronisCloud\Service\Config\ConfigAwareTrait;
use AcronisCloud\Service\Database\Repository\RepositoryAwareTrait;
use AcronisCloud\Service\FactoryInterface;

class UsageReportManagerFactory implements FactoryInterface
{
    use ConfigAwareTrait;
    use RepositoryAwareTrait;

    const NAME = 'usage_report';

    /**
     * @return Manager
     * @throws \Exception
     */
    public function createInstance()
    {
        return new Manager(
            $this->getConfig()->getUsageReportSettings(),
            $this->getRepository()->getUsageReportRepository(),
            new FileManager()
        );
    }
}