<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. All rights reserved
 */

namespace Acronis\UsageReport\Service;

use Acronis\UsageReport\Service\ReportHelper;
use AcronisCloud\Service\Logger\LoggerAwareTrait;
use Acronis\UsageReport\Exception\ManagerException;

class ReportProcessor
{
    use LoggerAwareTrait;

    /**
     * @var ReportHelper
     */
    private $reportHelper;

    /**
     * @param ReportHelper $reportHelper
     */
    public function __construct($reportHelper)
    {
        $this->reportHelper = $reportHelper;
    }

    /**
     * @param string|int $datacenterId
     *
     * @throws ManagerException
     */
    public function process($datacenterId)
    {
        $reportEntry = $this->reportHelper->getOrCreateCurrentReportEntry($datacenterId);

        $this->reportHelper->orderReportIfNotOrdered($reportEntry);
        $this->reportHelper->waitReportIsReadyIfNotReadyYet($reportEntry);
        $this->reportHelper->downloadReportIfNotDownloaded($reportEntry);

        if (!$reportEntry->isDownloaded()) {
            $this->getLogger()->notice('Report was not downloaded.');
        }
    }
}