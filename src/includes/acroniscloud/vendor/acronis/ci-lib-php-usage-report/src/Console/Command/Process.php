<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. All rights reserved
 */

namespace Acronis\UsageReport\Console\Command;

use Acronis\UsageReport\Report\ReportProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Acronis\UsageReport\Exception\ManagerException;

final class Process extends Command
{
    const COMMAND_NAME = 'usage-report:process';

    const ARGUMENT_DATACENTER = 'datacenterId';
    const ARGUMENT_DATACENTER_DESCRIPTION = 'ID (UUID) of datacenter resource.';

    /**
     * @var ReportProcessor
     */
    private $reportProcessor;

    /**
     * @param $reportProcessor
     */
    public function __construct($reportProcessor)
    {
        $this->reportProcessor = $reportProcessor;

        parent::__construct($this->getCommandName());
    }

    protected function configure()
    {
        $this
            ->setDescription($this->getCommandDescription())
            ->setHelp($this->getCommandHelp())
            ->addArgument(
                static::ARGUMENT_DATACENTER,
                InputArgument::REQUIRED,
                static::ARGUMENT_DATACENTER_DESCRIPTION
            );

        parent::configure();
    }

    protected function getCommandName()
    {
        return static::COMMAND_NAME;
    }

    protected function getCommandDescription()
    {
        return 'Command for usage report processing.';
    }

    protected function getCommandHelp()
    {
        return <<<TEXT
<info>
This command orders an usage report, downloads and aggregates it.
The results of the aggregation are stored in the key-value storage.
</info>
TEXT;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @throws ManagerException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $datacenterId = $this->getArgumentDatacenterId($input);

        $this->reportProcessor->process($datacenterId);
    }

    private function getArgumentDatacenterId(InputInterface $input)
    {
        return $input->getArgument(static::ARGUMENT_DATACENTER);
    }
}