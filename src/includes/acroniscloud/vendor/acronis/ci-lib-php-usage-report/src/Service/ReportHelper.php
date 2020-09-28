<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. All rights reserved
 */

namespace Acronis\UsageReport\Service;

use Acronis\UsageReport\ManagerInterface;
use Acronis\UsageReport\Model\ReportEntryInterface;
use Acronis\UsageReport\Model\ReportEntryRepositoryInterface;
use Acronis\UsageReport\ReportStorageInterface;
use Acronis\UsageReport\UsageReportSettingsInterface;
use AcronisCloud\Service\Config\ConfigAwareTrait;
use AcronisCloud\Service\Logger\LoggerAwareTrait;
use Acronis\UsageReport\Exception\ManagerException;
use AcronisCloud\Service\UsageReport\UsageReportManagerAwareTrait;
use AcronisCloud\Util\Time;

class ReportHelper
{
    use LoggerAwareTrait;
    use ConfigAwareTrait;
    use UsageReportManagerAwareTrait;

    /**
     * @var ReportEntryRepositoryInterface
     */
    private $reportEntryRepository;

    /**
     * @var ManagerInterface
     */
    private $reportManager;

    /**
     * @var UsageReportSettingsInterface
     */
    private $usageReportSettings;

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

        $this->usageReportSettings = $this->getConfig()->getUsageReportSettings();
    }

    public function getOrCreateCurrentReportEntry($datacenterId)
    {
        $date = Time::getCurrentDate();

        $reportEntry = $this->reportEntryRepository->getReportByDatacenterIdAndDate($datacenterId, $date);

        return $reportEntry ?: $this->reportEntryRepository->createFromDatacenterId($datacenterId, $date);
    }

    /**
     * @param ReportEntryInterface $reportEntry
     *
     * @throws ManagerException
     */
    public function orderReportIfNotOrdered($reportEntry)
    {
        if (!$reportEntry->isOrdered()) {
            $this->getUsageReportManager()->orderReport($reportEntry);
        } else {
            $this->getLogger()->notice('Skipping report ordering since it\'s already ordered.');
        }
    }

    /**
     * @param ReportEntryInterface $reportEntry
     *
     * @throws ManagerException
     */
    public function waitReportIsReadyIfNotReadyYet($reportEntry)
    {
        if (!$reportEntry->isReady() && !$this->waitReportIsReady($reportEntry)) {
            if ($reportEntry->isError()) {
                $this->getLogger()->error('Report generation failed or something went wrong.');
                return;
            }

            $this->getLogger()->notice('Report is not ready yet.');
            return;
        }
    }

    /**
     * @param ReportEntryInterface $reportEntry
     *
     * @throws ManagerException
     */
    public function downloadReportIfNotDownloaded($reportEntry)
    {
        if (!$reportEntry->isDownloaded()) {
            $this->getUsageReportManager()->downloadReport($reportEntry);
        }
    }

    /**
     * @param ReportEntryInterface $reportEntry
     *
     * @return bool
     *
     * @throws ManagerException
     */
    public function waitReportIsReady($reportEntry)
    {
        $this->getLogger()->notice('Waiting for stored report.');

        $retriesLimit = $this->usageReportSettings->getRetriesLimit();
        $retryTimeout = $this->usageReportSettings->getRetryTimeout();

        for ($retries = 0; $retries < $retriesLimit; $retries++) {
            $this->checkForStoredReport($reportEntry, $retries);

            if ($reportEntry->isError()) {
                return false;
            }

            if ($reportEntry->isReady()) {
                $this->getLogger()->notice('Found stored report.');
                return true;
            }

            $this->getLogger()->notice('Waiting {0} seconds before next retry.', [$retryTimeout]);

            sleep($retryTimeout);
        }

        return false;
    }

    /**
     * @param ReportEntryInterface $reportEntry
     * @param int $retries
     *
     * @throws ManagerException
     */
    private function checkForStoredReport($reportEntry, $retries = 0)
    {
        $retriesStr = $retries ? sprintf(' (%s retry)', $retries) : '';
        $this->getLogger()->notice(
            'Getting list of stored reports from Acronis Cyber Cloud{0}.',
            [$retriesStr]
        );

        $this->getUsageReportManager()->checkForStoredReport($reportEntry);
    }

}