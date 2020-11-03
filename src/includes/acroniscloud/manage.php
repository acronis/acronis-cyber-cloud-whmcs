#!/usr/bin/env php
<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. This source code is distributed under MIT software license.
 */

require_once __DIR__ . DIRECTORY_SEPARATOR . 'constants.php';
require_once ACRONIS_CLOUD_WHMCS_DIR . DIRECTORY_SEPARATOR . 'init.php';

use AcronisCloud\Repository\WHMCS\AcronisServerRepository;
use AcronisCloud\Repository\ReportStorageRepository;
use AcronisCloud\Repository\ReportRepository;
use AcronisCloud\Service\UsageReport\MetricsFetcher;
use Acronis\UsageReport\Console\Command\Erase;
use Acronis\UsageReport\Console\Command\Process;
use Acronis\UsageReport\Console\Command\Master;
use Acronis\UsageReport\Console\Command\ViewList;
use Acronis\UsageReport\Console\Command\FlushAll;
use AcronisCloud\Console\Command\Metrics;
use Acronis\UsageReport\Service\ReportProcessor;
use Acronis\UsageReport\Service\ReportHelper;
use Symfony\Component\Console\Application;

$datacenterRepository = new AcronisServerRepository();
$reportRepository = new ReportRepository();
$reportStorageRepository = new ReportStorageRepository();

$reportProcessor = new ReportProcessor(
    new ReportHelper($reportRepository, $reportStorageRepository)
);

$usageReportFetcher = new MetricsFetcher(
        $reportRepository,
        $reportStorageRepository,
        $datacenterRepository
);

$application = new Application();
$application->add(new Erase());
$application->add(new ViewList($datacenterRepository, $reportRepository));
$application->add(new Process($reportProcessor, $usageReportFetcher));
$application->add(new Master($datacenterRepository));
$application->add(new FlushAll($reportRepository, $reportStorageRepository));
$application->add(new Metrics($usageReportFetcher));
$application->run();