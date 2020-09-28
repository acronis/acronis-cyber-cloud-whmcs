<?php
/**
 * @Copyright © 2002-2018 Acronis International GmbH. All rights reserved
 */

namespace Acronis\UsageReport;

use Acronis\UsageReport\Csv\CsvFormatParser;
use Acronis\UsageReport\Csv\CsvReportIterator;
use Acronis\UsageReport\Exception\ManagerException;
use Acronis\UsageReport\Model\DatacenterInterface;
use Acronis\UsageReport\Model\ReportEntryInterface;
use Acronis\UsageReport\Service\FileManager;
use AcronisCloud\Service\Logger\LoggerAwareTrait;
use Acronis\UsageReport\Model\ReportEntryRepositoryInterface;
use AcronisCloud\Util\Time;
use AcronisCloud\CloudApi\Api;
use AcronisCloud\CloudApi\CloudApiTrait;
use Acronis\Cloud\Client\Model\Reports\Stored\StoredReportParamsItems;
use Acronis\Cloud\Client\Model\Reports\Stored\StoredReportParamsReportFormat;
use Acronis\Cloud\Client\Model\Reports\Stored\StoredReportParamsStatus;
use Acronis\UsageReport\Aggregation\Aggregator;

/**
 * Usage Report Manager, providing basic operations to process usage report , and method
 * getCounters() to get stored in cache counters for subscription
 *
 * @package Acronis\UsageReport
 */
class Manager implements ManagerInterface
{
    use LoggerAwareTrait;
    use CloudApiTrait;

    const DOWNLOADED_CSV_REPORT_FILENAME_PATTERN = '%s.csv.gz';

    /**
     * @var UsageReportSettingsInterface
     */
    private $usageReportSettings;

    /**
     * @var ReportEntryRepositoryInterface
     */
    private $reportRepository;

    /**
     * @var FileManager
     */
    private $fileManager;

    /**
     * @param UsageReportSettingsInterface $usageReportSettings
     * @param ReportEntryRepositoryInterface $reportRepository
     * @param FileManager $fileManager
     */
    public function __construct($usageReportSettings, $reportRepository, $fileManager)
    {
        $this->usageReportSettings = $usageReportSettings;
        $this->reportRepository = $reportRepository;
        $this->fileManager = $fileManager;
    }

    /**
     * @param ReportEntryInterface $reportEntry
     *
     * @throws ManagerException
     */
    public function orderReport($reportEntry)
    {
        $api = $this->getCloudApiForServer($reportEntry->getDatacenter());

        // Remove this method, create method for ordering current report after fixing RAML or lib
        $reportData = $api->orderCurrentUsageReportForAccounts($api->getRootTenantId());

        $reportEntry->ordered($reportData->getId());
    }

    /**
     * @param ReportEntryInterface $reportEntry
     *
     * @throws ManagerException
     */
    public function checkForStoredReport($reportEntry)
    {
        $api = $this->getCloudApiForServer($reportEntry->getDatacenter());

        $storedReport = $this->getStoredReport($reportEntry, $api);

        if ($storedReport->getStatus() === StoredReportParamsStatus::SAVED) {
            $reportEntry->ready($storedReport->getId());
        }

        if ($storedReport->getStatus() === StoredReportParamsStatus::FAILED) {
            $reportEntry->error();
        }

    }

    /**
     * @param ReportEntryInterface $reportEntry
     *
     * @throws ManagerException
     */
    public function downloadReport($reportEntry)
    {
        $api = $this->getCloudApiForServer($reportEntry->getDatacenter());

        $downloadPath = $this->resolveReportDownloadPath($reportEntry);
        if (!file_exists($downloadPath)) {
            mkdir($downloadPath);
        }

        $destinationFilePath = $this->generateReportPath($reportEntry);

        $api->downloadStoredUsageReportArchive(
            $reportEntry->getReportId(),
            $reportEntry->getStoredReportId(),
            $destinationFilePath
        );

        $reportEntry->downloaded($destinationFilePath);
    }

    public function cleanReportDownloadPath()
    {
        $this->getLogger()->notice('Cleaning up reports download path recursively.');

        $this->fileManager->removeFilesRecursively(
            $this->usageReportSettings->getReportsBasePath()
        );
        $this->getLogger()->notice('Done cleaning up reports download path.');
    }

    public function eraseReports()
    {
        $ttl = $this->usageReportSettings->getReportsTtlInDays();

        if ($ttl === -1) {
            return;
        }

        $dateTill = Time::offsetDate(new \DateTime(), -$ttl);

        $reportsToErase = $this->reportRepository->getReportsTillDate($dateTill, false);

        foreach ($reportsToErase as $reportEntry) {
            $this->eraseReport($reportEntry);
        }

        $this->fileManager->removeEmptyFoldersInPath(
            $this->usageReportSettings->getReportsBasePath()
        );
    }

    /**
     * @param ReportEntryInterface $reportEntry
     * @param ApiInterface $api
     * @throws ManagerException
     * @return StoredReportParamsItems
     */
    private function getStoredReport($reportEntry, $api)
    {
        $storedReports = $api->getStoredUsageReports($reportEntry->getReportId());

        $suitableReports = array_filter($storedReports, function ($storedReport) {
            /** @var StoredReportParamsItems $storedReport */
            return $storedReport->getReportFormat() === StoredReportParamsReportFormat::CSV_V2_0;
        });

        if (!count($suitableReports)) {
            throw new ManagerException(sprintf(
                'There are no stored reports of format "%s" for report #%s.',
                StoredReportParamsReportFormat::CSV_V2_0, $reportEntry->getId()
            ));
        }

        return $suitableReports[0];
    }

    /**
     * @param ReportEntryInterface $reportEntry
     */
    private function eraseReport($reportEntry)
    {
        $this->getLogger()->notice(
            'Erasing report entry #{0}.',
            [$reportEntry->getId()]
        );

        if ($reportEntry->isDownloaded()) {

            $filePath = $reportEntry->getFilePath();

            if (file_exists($filePath)) {
                unlink($filePath);

                $this->getLogger()->notice(
                    'Removed report #{0} downloaded file "{1}".',
                    [$reportEntry->getId(), $filePath]
                );
            }

            else {
                $this->getLogger()->warning(
                    'Report downloaded file "{0}" does\'nt exists.',
                    [$filePath]
                );
            }
        }

        $reportEntry->erase();

        $this->getLogger()->notice(
            'Report entry #{0} marked as "erased".',
            [$reportEntry->getId()]
        );
    }

    /**
     * @param ReportEntryInterface $reportEntry
     *
     * @return string
     */
    private function generateReportPath($reportEntry)
    {
        return $this->resolveReportDownloadPath($reportEntry) . \DIRECTORY_SEPARATOR .
            sprintf(
                static::DOWNLOADED_CSV_REPORT_FILENAME_PATTERN,
                $reportEntry->getId()
            );
    }

    /**
     * @param ReportEntryInterface $reportEntry
     *
     * @return string
     */
    private function resolveReportDownloadPath($reportEntry)
    {
        return implode(\DIRECTORY_SEPARATOR, [
            $this->usageReportSettings->getReportsBasePath(),
            $reportEntry->getDate(),
        ]);
    }
}