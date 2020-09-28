<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\UsageReport;

use AcronisCloud\Service\Database\Repository\RepositoryAwareTrait;
use AcronisCloud\Service\FactoryInterface;

class MetricsFetcherFactory implements FactoryInterface
{
    use RepositoryAwareTrait;

    const NAME = 'metrics_fetcher';

    /**
     * @return MetricsFetcher
     */
    public function createInstance()
    {
        $repository = $this->getRepository();

        return new MetricsFetcher(
            $repository->getUsageReportRepository(),
            $repository->getReportStorageRepository(),
            $repository->getAcronisServerRepository()
        );
    }
}