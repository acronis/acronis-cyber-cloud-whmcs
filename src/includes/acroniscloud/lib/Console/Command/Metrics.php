<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Console\Command;

use Acronis\UsageReport\Report\ReportProcessor;
use AcronisCloud\Service\UsageReport\MetricsFetcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

final class Metrics extends Command
{
    /**
     * @var MetricsFetcher
     */
    private $usageReportFetcher;

    /**
     * @param $usageReportFetcher
     */
    public function __construct($usageReportFetcher)
    {
        $this->usageReportFetcher = $usageReportFetcher;

        parent::__construct($this->getCommandName());
    }

    protected function configure()
    {
        $this->setDescription('Command for metrics fetching.');

        parent::configure();
    }

    protected function getCommandName()
    {
        return 'usage-report:metrics';
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->usageReportFetcher->fetchForToday();
    }
}