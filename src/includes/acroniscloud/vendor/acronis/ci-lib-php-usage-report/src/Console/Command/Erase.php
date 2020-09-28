<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. All rights reserved
 */

namespace Acronis\UsageReport\Console\Command;

use Acronis\UsageReport\ManagerInterface;
use AcronisCloud\Service\Config\ConfigAwareTrait;
use AcronisCloud\Service\UsageReport\UsageReportManagerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class Erase extends Command
{
    use UsageReportManagerAwareTrait;
    use ConfigAwareTrait;

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
        return 'usage-report:erase';
    }

    protected function getCommandDescription()
    {
        return sprintf(
            'Command for usage reports erasing, keeping only reports for last "%s" days.',
            $this->getConfig()->getUsageReportSettings()->getReportsTtlInDays()
        );
    }

    protected function getCommandHelp()
    {
        return <<<TEXT
<info>
This command cleans downloaded usage report. Reports ordered some number of days before 
(specified as "downloaded_files_ttl_in_days" in application.yaml)
would be marked with ERASED status and its downloaded .csv.gz files would be deleted. 
In the end of the execution command will remove empty folders from reports download path.
</info>
TEXT;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->getUsageReportManager()->eraseReports();
    }
}