<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

use AcronisCloud\Service\BuildInfo\BuildInfoFactory;
use AcronisCloud\Service\Cache\CacheFactory;
use AcronisCloud\Service\Config\AddonConfigFactory;
use AcronisCloud\Service\Config\ConfigFactory;
use AcronisCloud\Service\Database\Repository\RepositoryFactory;
use AcronisCloud\Service\Dispatcher\DispatcherFactory;
use AcronisCloud\Service\Errors\ProvisioningErrorsFactory;
use AcronisCloud\Service\Language\IsoCodeFactory;
use AcronisCloud\Service\Localization\TranslatorFactory;
use AcronisCloud\Service\Logger\LoggerFactory;
use AcronisCloud\Service\MetaInfo\MetaInfoFactory;
use AcronisCloud\Service\Session\SessionFactory;
use AcronisCloud\Service\UsageReport\UsageReportManagerFactory;
use AcronisCloud\Service\UsageReport\MetricsFetcherFactory;

return [
    AddonConfigFactory::NAME => new AddonConfigFactory(),
    BuildInfoFactory::NAME => new BuildInfoFactory(),
    CacheFactory::NAME => new CacheFactory(),
    ConfigFactory::NAME => new ConfigFactory(),
    DispatcherFactory::NAME => new DispatcherFactory(),
    LoggerFactory::NAME => new LoggerFactory(),
    MetaInfoFactory::NAME => new MetaInfoFactory(),
    RepositoryFactory::NAME => new RepositoryFactory(),
    TranslatorFactory::NAME => new TranslatorFactory(),
    SessionFactory::NAME => new SessionFactory(),
    ProvisioningErrorsFactory::NAME => new ProvisioningErrorsFactory(),
    UsageReportManagerFactory::NAME => new UsageReportManagerFactory(),
    MetricsFetcherFactory::NAME => new MetricsFetcherFactory(),
];