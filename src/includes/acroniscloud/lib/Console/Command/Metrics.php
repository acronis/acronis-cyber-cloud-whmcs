<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Console\Command;

use Acronis\UsageReport\Report\ReportProcessor;
use AcronisCloud\Service\MetaInfo\MetaInfoAwareTrait;
use AcronisCloud\Service\MetaInfo\OfferingItemMeta;
use AcronisCloud\Service\UsageReport\MetricsFetcher;
use AcronisCloud\Util\Arr;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

final class Metrics extends Command
{
    use MetaInfoAwareTrait;

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
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $oiFriendlyNames = $this->getFriendlyOfferingItemsNames();
        $metrics = $this->usageReportFetcher->fetchForToday();
        $friendlyMetrics = [];
        foreach ($metrics as $tenantId => $offeringItemsMetrics) {
            foreach ($offeringItemsMetrics as $oiName => $metric) {
                $oiName = isset($oiFriendlyNames[$oiName]) ? $oiFriendlyNames[$oiName] : $oiName;
                $friendlyMetrics[$tenantId][$oiName] = $metric;
            }
        }
        print_r($friendlyMetrics);

        return 0;
    }

    private function getFriendlyOfferingItemsNames()
    {
        return Arr::map(
            $this->getMetaInfo()->getOfferingItemsMeta(),
            function ($offeringItemMeta) {
                /** @var OfferingItemMeta $offeringItemMeta */
                return $offeringItemMeta->getOfferingItemName();
            },
            function ($offeringItemMeta) {
                /** @var OfferingItemMeta $offeringItemMeta */
                return $offeringItemMeta->getOfferingItemName() . ': ' . $offeringItemMeta->getOfferingItemFriendlyName();
            }
        );
    }
}