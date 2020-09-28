<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. All rights reserved
 */

namespace Acronis\UsageReport\Console\Command;

use Acronis\UsageReport\Model\DatacenterInterface;
use Acronis\UsageReport\Model\DatacenterRepositoryInterface;
use Acronis\UsageReport\Model\ReportEntryInterface;
use Acronis\UsageReport\Model\ReportEntryRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

final class ViewList extends Command
{
    const PROPERTY_DATE = 'date';
    const PROPERTY_ID = 'id';

    const SYNTHETIC_PROPERTY_DC_ID = 'dc_id';
    const SYNTHETIC_PROPERTY_DC_NAME = 'dc_name';
    const SYNTHETIC_PROPERTY_FILE = 'file_path';
    const SYNTHETIC_PROPERTY_STATUS = 'status';

    const PROPERTY_NOT_RESOLVED = '----';

    private static $columns = [
        self::PROPERTY_ID => 'ID',
        self::PROPERTY_DATE => 'Date',
        self::SYNTHETIC_PROPERTY_DC_NAME => 'Datacenter name',
        self::SYNTHETIC_PROPERTY_DC_ID => 'Datacenter ID',
        self::SYNTHETIC_PROPERTY_STATUS => 'Report status',
        self::SYNTHETIC_PROPERTY_FILE => 'File path',
    ];

    /**
     * @var DatacenterInterface[]
     */
    private $datacenters = [];

    /**
     * @var DatacenterRepositoryInterface
     */
    private $datacenterRepository;

    /**
     * @var ReportEntryRepositoryInterface
     */
    private $reportEntryRepository;

    /**
     * @param DatacenterRepositoryInterface $datacenterRepository
     * @param ReportEntryRepositoryInterface $reportEntryRepository
     */
    public function __construct($datacenterRepository, $reportEntryRepository)
    {
        $this->datacenterRepository = $datacenterRepository;
        $this->reportEntryRepository = $reportEntryRepository;

        parent::__construct($this->getCommandName());
    }

    protected function configure()
    {
        $this
            ->setDescription($this->getCommandDescription())
            ->setHelp($this->getCommandHelp());

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $reports = $this->reportEntryRepository->getAllReports();

        if (!count($reports)) {
            $output->writeln('There are no reports to be processed.');
            return;
        }

        $output->writeln('List of reports:');

        $tableOutput = new BufferedOutput();
        $table = $this->buildConsoleTable($reports, $tableOutput);
        $table->render();

        $output->writeln($tableOutput->fetch());
    }

    protected function getCommandName()
    {
        return 'usage-report:view-list';
    }

    protected function getCommandDescription()
    {
        return 'Command for usage report listing.';
    }

    protected function getCommandHelp()
    {
        return <<<TEXT
<info>
This command lists all reports in a friendly way
</info>
TEXT;
    }

    /**
     * @param ReportEntryInterface[] $reports
     * @param $output
     * @return Table
     * @throws \Exception
     */
    private function buildConsoleTable($reports, $output)
    {
        $table = new Table($output);
        $table->setHeaders(array_values(static::$columns));
        foreach ($reports as $report) {
            $table->addRow($this->buildReportRow($report));
        }

        return $table;
    }

    /**
     * @param ReportEntryInterface $report
     * @throws \Exception
     * @return string[]
     */
    private function buildReportRow($report)
    {
        $row = [];
        foreach (array_keys(static::$columns) as $property) {
            switch ($property) {
                case self::SYNTHETIC_PROPERTY_STATUS:
                    $value = $report->getStatusDescription();
                    break;
                case self::SYNTHETIC_PROPERTY_DC_NAME:
                    $dc = $this->getDatacenter($report->getDatacenterId());
                    $value = is_null($dc) ? static::PROPERTY_NOT_RESOLVED : $dc->getName();
                    break;
                case self::SYNTHETIC_PROPERTY_DC_ID:
                    $dc = $this->getDatacenter($report->getDatacenterId());
                    $value = is_null($dc) ? static::PROPERTY_NOT_RESOLVED : $dc->getId();
                    break;
                default:
                    $value = $report->{$property};
            }

            $row[] = $value;
        }

        return $row;
    }

    /**
     * @param $datacenterId
     * @return mixed
     */
    private function getDatacenter($datacenterId)
    {
        if (!isset($this->datacenters[$datacenterId])) {
            $this->datacenters[$datacenterId] = $this->datacenterRepository->getDatacenterById($datacenterId);
        }

        return $this->datacenters[$datacenterId];
    }
}