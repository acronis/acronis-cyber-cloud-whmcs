<?php
/**
 * @Copyright © 2003-2020 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace WHMCS\Module\Server\AcronisCloud\Controller;

use Acronis\UsageReport\Console\Command\Master;
use Acronis\UsageReport\Service\ReportHelper;
use Acronis\UsageReport\Service\ReportProcessor;
use AcronisCloud\Model\ReportStorage;
use AcronisCloud\Repository\ReportRepository;
use AcronisCloud\Repository\ReportStorageRepository;
use AcronisCloud\Repository\WHMCS\AcronisServerRepository;
use AcronisCloud\Service\Dispatcher\AbstractController;
use AcronisCloud\Service\Dispatcher\RequestInterface;
use AcronisCloud\Service\Logger\LoggerAwareTrait;
use AcronisCloud\Service\UsageReport\UsageReportManagerAwareTrait;
use WHMCS\Module\Server\AcronisCloud\MetricProvider;
use WHMCS\UsageBilling\Contracts\Metrics\ProviderInterface;
use Acronis\UsageReport\Exception\ManagerException;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class Report extends AbstractController
{
    use LoggerAwareTrait;
    use UsageReportManagerAwareTrait;

    /**
     * @param RequestInterface $request
     *
     * @return ProviderInterface
     */
    public function getMetricProvider($request)
    {
        return new MetricProvider($request->getParameters());
    }

    /**
     * The AfterCronJob hook point will be called each time cron.php is invoked.
     *
     * Therefore if the user has the cron set up to run every 5 minutes
     * as WHMCS recommends then this will be called every 5 minutes.
     *
     * @param RequestInterface $request
     *
     * @throws ManagerException
     */
    public function afterCronJob($request)
    {
        $this->getLogger()->notice('hook: afterCronJob | Running Master command');

        $master = new Master(new AcronisServerRepository());
        $master->run(new ArgvInput(), new ConsoleOutput());

        $this->getLogger()->notice('hook: afterCronJob COMPLETE');
    }

    /**
     * Runs at the very end of the daily automation cron execution.
     *
     * @param RequestInterface $request
     */
    public function dailyCronJob($request)
    {
        $this->getLogger()->notice('hook: dailyCronJob | Erasing usage reports');

        $this->getUsageReportManager()->eraseReports();
    }

}