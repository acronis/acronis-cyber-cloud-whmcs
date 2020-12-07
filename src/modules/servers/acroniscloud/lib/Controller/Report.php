<?php
/**
 * @Copyright © 2003-2020 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace WHMCS\Module\Server\AcronisCloud\Controller;

use Acronis\UsageReport\Console\Command\Master;
use Acronis\UsageReport\Service\ReportHelper;
use Acronis\UsageReport\Service\ReportProcessor;
use Acronis\UsageReport\UsageReportSettingsInterface;
use AcronisCloud\Model\ReportStorage;
use AcronisCloud\Repository\ReportRepository;
use AcronisCloud\Repository\ReportStorageRepository;
use AcronisCloud\Repository\WHMCS\AcronisServerRepository;
use AcronisCloud\Service\Config\ConfigAwareTrait;
use AcronisCloud\Service\Database\Repository\RepositoryAwareTrait;
use AcronisCloud\Service\Dispatcher\AbstractController;
use AcronisCloud\Service\Dispatcher\RequestInterface;
use AcronisCloud\Service\Logger\LoggerAwareTrait;
use AcronisCloud\Service\UsageReport\UsageReportManagerAwareTrait;
use Symfony\Component\Console\Input\ArrayInput;
use WHMCS\Module\Server\AcronisCloud\MetricProvider;
use WHMCS\UsageBilling\Contracts\Metrics\ProviderInterface;
use Acronis\UsageReport\Exception\ManagerException;
use Symfony\Component\Console\Output\ConsoleOutput;

class Report extends AbstractController
{
    use LoggerAwareTrait;
    use RepositoryAwareTrait;
    use UsageReportManagerAwareTrait;
    use ConfigAwareTrait;

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
        if (!$this->hasBillingMetricsProducts()) {
            $this->getLogger()->notice('hook: afterCronJob | Skipping, no metrics enabled');
            return;
        }

        $this->getLogger()->notice('hook: afterCronJob | Running Master command');
        $cliInterpreter = $this->getConfig()->getUsageReportSettings()->getCliInterpreter();
        $input = new ArrayInput([UsageReportSettingsInterface::PROPERTY_PHP_CLI_INTERPRETER => $cliInterpreter]);

        $master = new Master(new AcronisServerRepository());
        $master->run($input, new ConsoleOutput());

        $this->getLogger()->notice('hook: afterCronJob COMPLETE');
    }

    /**
     * Runs at the very end of the daily automation cron execution.
     *
     * @param RequestInterface $request
     */
    public function dailyCronJob($request)
    {
        if (!$this->hasBillingMetricsProducts()) {
            $this->getLogger()->notice('hook: dailyCronJob | Skipping, no metrics enabled');
            return;
        }

        $this->getLogger()->notice('hook: dailyCronJob | Erasing usage reports');

        $this->getUsageReportManager()->eraseReports();
    }

    /**
     * @return bool
     */
    protected function hasBillingMetricsProducts()
    {
        return $this->getRepository()
            ->getBillingMetricsRepository()
            ->activeAcronisMetricsExist();
    }
}