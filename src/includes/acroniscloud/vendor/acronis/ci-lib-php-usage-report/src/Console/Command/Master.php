<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. All rights reserved
 */

namespace Acronis\UsageReport\Console\Command;

use Acronis\UsageReport\Model\DatacenterInterface;
use Acronis\UsageReport\Model\DatacenterRepositoryInterface;
use Acronis\UsageReport\UsageReportSettingsInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AcronisCloud\Service\Logger\LoggerAwareTrait;

class Master extends Command
{
    use LoggerAwareTrait;

    /**
     * @var DatacenterRepositoryInterface
     */
    private $datacenterRepository;

    /**
     * @param DatacenterRepositoryInterface $datacenterRepository
     */
    public function __construct($datacenterRepository)
    {
        $this->datacenterRepository = $datacenterRepository;

        parent::__construct($this->getCommandName());
    }

    protected function configure()
    {
        $this
            ->setName($this->getCommandName())
            ->setDescription($this->getCommandDescription())
            ->addArgument(
                UsageReportSettingsInterface::PROPERTY_PHP_CLI_INTERPRETER,
                InputArgument::OPTIONAL,
                'PHP Cli Interpreter used for running report processing jobs.'
            )
            ->setHelp($this->getCommandHelp());

        parent::configure();
    }

    protected function getCommandName()
    {
        return 'usage-report:master';
    }

    protected function getCommandDescription()
    {
        return 'Usage report aggregation master command.';
    }

    protected function getCommandHelp()
    {
        return <<<TEXT
<info>
Usage report aggregation master command. It should be registered as cron job and run successfully at least one time
a day for all datacetners and application instances in order to be able to synchronize usage via OSA periodic task.
</info>
TEXT;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manageCommandPath = realpath(DIR_BIN . \DIRECTORY_SEPARATOR . 'manage.php');

        $childPids = [];

        foreach ($this->datacenterRepository->getDatacenters() as $datacenter) {
            $pid = pcntl_fork();

            if ($pid == -1) {
                $this->getLogger()->error(
                    'Unable to fork child process for managing usage report aggregation with datacenter ID "%s."',
                    $datacenter->getId()
                );
                exit(1);
            } else if ($pid) {
                $childPids[] = $pid;
                continue;
            } else {
                $interpreterArg = UsageReportSettingsInterface::PROPERTY_PHP_CLI_INTERPRETER;
                $interpreter = $input->hasArgument($interpreterArg) ? $input->getArgument($interpreterArg) : 'php';
                pcntl_exec(
                    '/usr/bin/env',
                    [
                        $interpreter,
                        '--no-header',
                        $manageCommandPath,
                        Process::COMMAND_NAME,
                        $datacenter->getId()
                    ]
                );

                return 0;
            }
        }

        foreach ($childPids as $pid) {
            pcntl_waitpid($pid, $status);
        }

        return 0;
    }
}