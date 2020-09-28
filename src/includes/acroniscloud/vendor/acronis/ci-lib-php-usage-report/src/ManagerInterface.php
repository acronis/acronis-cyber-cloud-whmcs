<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace Acronis\UsageReport;

use Acronis\UsageReport\Exception\ManagerException;
use Acronis\UsageReport\Model\ReportEntryInterface;

interface  ManagerInterface
{
    /**
     * @param ReportEntryInterface $reportEntry
     * @throws \Acronis\Cloud\Client\ApiException
     * @throws \Acronis\Cloud\Client\HttpException
     * @throws ManagerException
     */
    public function orderReport($reportEntry);

    /**
     * @param ReportEntryInterface $reportEntry
     * @throws ManagerException
     */
    public function checkForStoredReport($reportEntry);

    /**
     * @param ReportEntryInterface $reportEntry
     * @throws \Acronis\Cloud\Client\ApiException
     * @throws \Acronis\Cloud\Client\HttpException
     * @throws ManagerException
     */
    public function downloadReport($reportEntry);

    /**
     * Removes all saved reports and {date} folders from the usage report download path
     * Default path is /tmp/reports/{date}/{id}.csv.gz, see download_path config param
     */
    public function cleanReportDownloadPath();

    public function eraseReports();
}