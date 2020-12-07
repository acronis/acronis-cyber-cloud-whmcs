<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. All rights reserved
 */

namespace Acronis\UsageReport\Console\Command;

use Acronis\UsageReport\Model\ReportEntryRepositoryInterface;
use Acronis\UsageReport\ReportStorageInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AcronisCloud\Service\Logger\LoggerAwareTrait;
use AcronisCloud\Service\UsageReport\UsageReportManagerAwareTrait;

final class FlushAll extends Command
{
    use LoggerAwareTrait;
    use UsageReportManagerAwareTrait;

    /**
     * @var ReportEntryRepositoryInterface
     */
    private $reportEntryRepository;

    /**
     * @var ReportStorageInterface
     */
    private $reportStorage;

    /**
     * @param ReportEntryRepositoryInterface $reportEntryRepository
     * @param ReportStorageInterface $reportStorage
     */
    public function __construct($reportEntryRepository, $reportStorage)
    {
        $this->reportEntryRepository = $reportEntryRepository;
        $this->reportStorage = $reportStorage;

        parent::__construct($this->getCommandName());
    }

    protected function configure()
    {
        $this
            ->setName($this->getCommandName())
            ->setDescription($this->getCommandDescription())
            ->setHelp($this->getCommandHelp());

        parent::configure();
    }

    protected function getCommandName()
    {
        return 'usage-report:flushall';
    }

    protected function getCommandDescription()
    {
        return 'Command for usage report flushing.';
    }

    protected function getCommandHelp()
    {
        return <<<TEXT
<info>
This command drops report table and creates a new one from migration, removes all 
downloaded reports, invalidates all report cache and storage from Redis.
</info>
TEXT;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->flushReportsTable();
        $this->cleanReportDownloadPath();
        $this->invalidateReportData();

        return 0;
    }

    private function flushReportsTable()
    {
        $this->getLogger()->notice('Truncating reports table.');
        $this->reportEntryRepository->truncate();
        $this->getLogger()->notice('Truncated reports table.');
    }

    private function cleanReportDownloadPath()
    {
        $this->getUsageReportManager()->cleanReportDownloadPath();
    }

    private function invalidateReportData()
    {
        $this->getLogger()->notice('Invalidating reports storage.');
        $this->reportStorage->deleteAll();
    }

}