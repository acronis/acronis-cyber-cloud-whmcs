<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\UsageReport;

use Acronis\Cloud\Client\Model\Infra\Infra;
use Acronis\UsageReport\Csv\CsvFormatParser;
use Acronis\UsageReport\Csv\CsvReportIterator;
use Acronis\UsageReport\Model\DatacenterRepositoryInterface;
use Acronis\UsageReport\Model\ReportEntryRepositoryInterface;
use Acronis\UsageReport\ReportRowWrapperInterface;
use AcronisCloud\CloudApi\CloudApiTrait;
use AcronisCloud\Model\WHMCS\Service;
use AcronisCloud\Repository\ReportStorageRepository;
use AcronisCloud\Service\Logger\LoggerAwareTrait;
use AcronisCloud\Util\Arr;
use AcronisCloud\Util\MemoizeTrait;
use AcronisCloud\Util\Time;
use WHMCS\Module\Server\AcronisCloud\Product\CustomFields;

class MetricsFetcher
{
    use LoggerAwareTrait,
        MemoizeTrait,
        CloudApiTrait;

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
     * @var string[]
     */
    private $childStorageMapping = [
        'pg_storage' => 'pg_child_storages',
        'pw_storage' => 'pg_child_storages',
        'pw_dr_storage' => 'pw_dr_child_storages',
        'fc_storage' => 'fc_child_storages'
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

            $this->getLogger()->notice(
                'Processing metrics for datacenter with id {0}.',
                [$datacenter->getId()]
            );
            if (null !== $report && $report->isDownloaded() && filesize($report->getFilePath())) {
                $reportIterator = $this->createReportIterator($report->getFilePath());
                foreach ($reportIterator as $rowIndex => $reportRow) {
                    if (\array_key_exists($reportRow->getTenantId(), $this->tenants)) {
                        $this->getLogger()->notice(
                            'Started report row #{0} aggregation.',
                            [$rowIndex + 1]
                        );
                        $isPlaformOwned = $this->isInfraPlatformOwned($datacenter, $reportRow->getTenantId(), $reportRow->getInfraId());
                        $partnerOIName = !$isPlaformOwned ? $this->getPartnerOfferingItemName($reportRow) : '';
                        $offeringItemName = $partnerOIName ? $partnerOIName : $reportRow->getOfferingItemName();
                        $metricName = $this->getMetricName($reportRow, $offeringItemName);

                        $usage = $this->calculateReportRowUsage($reportRow);
                        $tenantId = $reportRow->getTenantId();
                        $infraId = $reportRow->getInfraId();
                        $metrics[$tenantId][$metricName][$infraId] = $usage;

                        $this->getLogger()->notice(
                            'Finished report row #{0} aggregation with metrics for [{1}, {2}, {3}] = {4}.',
                            [$rowIndex + 1, $tenantId, $metricName, $infraId, $usage]
                        );
                    }
                }
            }
        }
        $metrics = $this->sumByInfra($metrics);

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
     * @param ReportRowWrapperInterface $reportRow
     */
    private function calculateReportRowUsage($reportRow)
    {
        return $reportRow->getUsage(
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
     * @param $offeringItemName
     * @return string
     */
    private function getMetricName($reportRow, $offeringItemName)
    {
        $infraBackendType = $reportRow->getInfraBackendType();
        if ($infraBackendType) {
            return \sprintf('%s_%s', $offeringItemName, $infraBackendType);
        }

        return $offeringItemName;
    }

    /**
     * @param ReportRowWrapperInterface $reportRow
     *
     * @return string
     */
    private function getPartnerOfferingItemName($reportRow)
    {
        $offeringItemName = $reportRow->getOfferingItemName();
        if ($reportRow->getInfraId() && isset($this->childStorageMapping[$offeringItemName])) {
            return $this->childStorageMapping[$offeringItemName];
        }

        return '';
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

    private function isInfraPlatformOwned($datacenter, $tenantId, $infraId)
    {
        if (!$infraId) {
            return false;
        }

        $tenantInfras = $this->memoize(function () use ($datacenter, $tenantId) {
            /** @var Infra[] $tenantInfras */
            $tenantInfras = $this->getCloudApiForServer($datacenter)->fetchTenantInfras($tenantId);
            return Arr::map(
                $tenantInfras,
                function ($infra) { return $infra->getId(); },
                function ($infra) { return $infra->getPlatformOwned(); }
            );
        }, $datacenter->getId() . '_' . $tenantId);

        return Arr::get($tenantInfras, $infraId);
    }

    private function sumByInfra($metrics)
    {
        $summedMetrics = [];
        foreach ($metrics as $tenantId => $namedMetrics) {
            foreach ($namedMetrics as $metricName => $infrasMetrics) {
                $summedMetrics[$tenantId][$metricName] = array_sum($infrasMetrics);
            }
        }

        return $summedMetrics;
    }
}