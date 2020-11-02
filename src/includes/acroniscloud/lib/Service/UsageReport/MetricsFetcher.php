<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\UsageReport;

use Acronis\UsageReport\Csv\CsvFormatParser;
use Acronis\UsageReport\Csv\CsvReportIterator;
use Acronis\UsageReport\Model\DatacenterRepositoryInterface;
use Acronis\UsageReport\Model\ReportEntryRepositoryInterface;
use Acronis\UsageReport\ReportRowWrapperInterface;
use AcronisCloud\Model\WHMCS\Service;
use AcronisCloud\Repository\ReportStorageRepository;
use AcronisCloud\Repository\WHMCS\ServiceRepository;
use AcronisCloud\Service\Logger\LoggerAwareTrait;
use AcronisCloud\Util\Time;
use WHMCS\Module\Server\AcronisCloud\Product\CustomFields;

class MetricsFetcher
{
    use LoggerAwareTrait;

    const TENANT_TYPE_CUSTOMER = 'customer';

    const USAGE_STRATEGY_ABSOLUTE = 'absolute';
    const USAGE_STRATEGY_DELTA = 'delta';

    const USAGE_KIND_FOR_CUSTOMER = 'total';
    const USAGE_KIND_FOR_PARTNER_AND_RESELLER = 'production';

    /**
     * @var ReportEntryRepositoryInterface
     */
    private $reportRepository;

    /**
     * @var DatacenterRepositoryInterface
     */
    private $datacenterRepository;

    /**
     * @var ReportStorageRepository
     */
    private $reportStorageRepository;

    /**
     * @var string[]
     */
    private $deltaUsageItems = [
        'compute_points',
        'pw_compute_points',
        'dre_compute_points',
        'p_dre_compute_points',
    ];

    /**
     * @var array
     */
    private $tenants;

    public function __construct(
        ReportEntryRepositoryInterface $reportRepository,
        ReportStorageRepository $reportStorageRepository,
        DatacenterRepositoryInterface $datacenterRepository
    ) {
        $this->reportRepository = $reportRepository;
        $this->reportStorageRepository = $reportStorageRepository;
        $this->datacenterRepository = $datacenterRepository;
        $this->tenants = $this->getTenants();
    }

    public function fetchForToday()
    {
        $metrics = $this->reportStorageRepository->get($this->getKey());

        if ($metrics !== null) {
            return \unserialize($metrics);
        }

        $metrics = [];

        foreach ($this->datacenterRepository->getDatacenters() as $datacenter) {
            $report = $this->reportRepository->getReportByDatacenterIdAndDate(
                $datacenter->getId(),
                Time::getCurrentDate()
            );

            if (null !== $report && $report->isDownloaded()) {

                $reportIterator = $this->createReportIterator($report->getFilePath());

                foreach ($reportIterator as $rowIndex => $reportRow) {

                    if (\array_key_exists($reportRow->getTenantId(), $this->tenants)) {
                        $this->getLogger()->notice(
                            'Started report row #{0} aggregation".',
                            [$rowIndex + 1]
                        );

                        $this->calculateReportRowUsage($metrics, $reportRow);

                        $this->getLogger()->notice(
                            'Finished report row #{0} aggregation.',
                            [$rowIndex + 1]
                        );
                    }
                }
            }
        }

        if (!empty($metrics)) {
            $this->reportStorageRepository->set($this->getKey(), \serialize($metrics));
        }

        return $metrics;
    }

    /**
     * @param string $filePath
     *
     * @return CsvReportIterator
     */
    private function createReportIterator($filePath)
    {
        return new CsvReportIterator($filePath, new CsvFormatParser());
    }

    /**
     * @return string
     */
    private function getKey()
    {
        return \sprintf('report-%s', Time::getCurrentDate());
    }

    /**
     * @param array $metrics
     * @param ReportRowWrapperInterface $reportRow
     */
    private function calculateReportRowUsage(&$metrics, $reportRow)
    {
        $metrics[$reportRow->getTenantId()][$this->getMetricName($reportRow)]
            += $reportRow->getUsage(
                $this->getUsageStrategy($reportRow->getOfferingItemName()),
                $this->getUsageCountingKind($reportRow->getTenantKind())
        );
    }

    /**
     * @param string $tenantType
     * @return string
     */
    private function getUsageCountingKind($tenantType)
    {
        if ($tenantType === static::TENANT_TYPE_CUSTOMER) {
            return static::USAGE_KIND_FOR_CUSTOMER;
        }

        return static::USAGE_KIND_FOR_PARTNER_AND_RESELLER;
    }

    /**
     * @param string $offeringItemName
     *
     * @return string
     */
    private function getUsageStrategy($offeringItemName)
    {
        return \in_array($offeringItemName, $this->deltaUsageItems)
            ? static::USAGE_STRATEGY_DELTA
            : static::USAGE_STRATEGY_ABSOLUTE;
    }

    /**
     * @param ReportRowWrapperInterface $reportRow
     *
     * @return string
     */
    private function getMetricName($reportRow)
    {
        $offeringItemName = $reportRow->getOfferingItemName();
        $infraBackendType = $reportRow->getInfraBackendType();

        if ($infraBackendType) {
            return \sprintf('%s_%s', $offeringItemName, $infraBackendType);
        }

        return $offeringItemName;
    }

    private function getTenants()
    {
        $tenants = [];

        foreach (Service::all() as $service) {
            $customFields = new CustomFields($service->getProductId(), $service->getId());

            $tenantId = $customFields->getTenantId();

            if ($tenantId) {
                $tenants[$tenantId] = true;
            }
        }

        return $tenants;

    }
}