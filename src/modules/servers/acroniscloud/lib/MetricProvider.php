<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace WHMCS\Module\Server\AcronisCloud;

use Acronis\UsageReport\Manager;
use AcronisCloud\Service\Locator;
use AcronisCloud\Service\Logger\LoggerAwareTrait;
use AcronisCloud\Service\UsageReport\Metrics\UsageMetric;
use AcronisCloud\Service\UsageReport\MetricsFetcher;
use AcronisCloud\Service\UsageReport\MetricsFetcherFactory;
use AcronisCloud\Service\UsageReport\Metrics\MetricUnitBuilder;
use AcronisCloud\Service\UsageReport\Metrics\Minutes;
use AcronisCloud\Service\UsageReport\UsageReportManagerFactory;
use AcronisCloud\Util\UomConverter;
use WHMCS\UsageBilling\Contracts\Metrics\MetricInterface;
use WHMCS\UsageBilling\Contracts\Metrics\ProviderInterface;
use WHMCS\UsageBilling\Metrics\Metric;
use WHMCS\UsageBilling\Metrics\Units\Accounts;
use WHMCS\UsageBilling\Metrics\Usage;

/**
 * Acronis Cyber Cloud Metric Provider
 *
 * @link https://developers.whmcs.com/provisioning-modules/usage-metrics/
 * @link https://docs.whmcs.com/Usage_Billing
 */
class MetricProvider implements ProviderInterface
{
    use LoggerAwareTrait;

    /**
     * @param array $moduleParams Array containing the module's params.
     *    $params = [
     *      'whmcsVersion'
     *      'server'
     *      'serverip'
     *      'serverid'
     *      'serverhostname'
     *      'serverusername'
     *      'serverpassword'
     *      'serveraccesshash'
     *      'serversecure'
     *      'serverhttpprefix'
     *      'serverport'
     *      'action'
     *    ]
     */
    private $moduleParams;

    /**
     * @param array $moduleParams
     */
    public function __construct($moduleParams)
    {
        $this->moduleParams = $moduleParams;
    }

    /**
     * All metrics supported by provider, keyed by metric system name
     *
     * Method invoked in contexts specifically about Products.
     * Must always return a valid list for all potential servers that use the module.
     *
     * Metric Interface:
     *
     *   __construct (
     *       $systemName,                   Canonical name to be used by code and between systems
     *       $displayName = null,           Name to render in UI to users
     *       $type = null,                  One of the TYPE_* constants of this instance
     *       UnitInterface $units = null,
     *       UsageInterface $usage = null
     *   )
     *
     * Metric types:
     *
     *   - MetricInterface::TYPE_SNAPSHOT      For metrics that are related to non-ephemeral entities, such as mailboxes, disk usage, or databases
     *   - MetricInterface::TYPE_PERIOD_MONTH  For metrics that are related to usage that is an accumulative measure at the remote system, like bandwidth
     *
     * Unit types:
     *
     *   - Accounts       not documented
     *   - Domains        not documented
     *
     *   - Bytes
     *   - KiloBytes
     *   - MegaBytes
     *   - GigaBytes
     *
     *   - FloatingPoint
     *   - WholeNumber
     *
     * @link https://classdocs.whmcs.com/7.10/WHMCS/UsageBilling/Metrics/Metric.html
     * @link https://classdocs.whmcs.com/7.10/WHMCS/UsageBilling/Metrics/Units_ns.html
     *
     * @return Metric[]
     */
    public function metrics()
    {
        $this->getLogger()->notice('MetricProvider::metrics() is called');

        return $this->getMetricsFromMeta();
    }

    /**
     * All usage of the provider, keyed by tenant id
     *
     * This method is used in a global context, such as by the cron when polling for all metric information
     *
     * @return array
     */
    public function usage()
    {
        $this->getLogger()->notice('MetricProvider::usage() is called');

        $serverData = $this->fetchMetrics();

        $usage = [];

        foreach ($serverData as $identifier => $data) {
            $usage[$identifier] = $this->wrapUserData($data);
        }

        return $usage;
    }

    /**
     * Method invoked in the context of a service with a server
     *
     * @param $tenant
     *
     * @return array
     */
    public function tenantUsage($tenant)
    {
        $this->getLogger()->notice('MetricProvider::tenantUsage() is called');

        $userData = $this->fetchMetrics($tenant);

        return $this->wrapUserData($userData);
    }

    /**
     * @param $data
     *
     * @return array
     */
    private function wrapUserData($data)
    {
        $wrapped = [];

        foreach ($this->metrics() as $metric) {
            $key = $metric->systemName();

            if (array_key_exists($key, $data)) {
                $value = $data[$key];

                $metric = $metric->withUsage(
                    new Usage($metric->getConvertedValue($value))
                );
            }

            $wrapped[] = $metric;
        }

        return $wrapped;
    }

    /**
     * @param $tenant
     *
     * @return array
     */
    private function fetchMetrics($tenant = null)
    {
        $metrics = $this->getMetricsFetcher()->fetchForToday();

        if (null !== $tenant) {
            $metrics = \array_key_exists($tenant, $metrics) ? $metrics[$tenant] : [];
        }

        return $metrics;
    }

    /**
     * @return UsageMetric[]
     *
     * @throws \Exception
     */
    private function getMetricsFromMeta()
    {
        $metricsMeta = require ACRONIS_CLOUD_INCLUDES_DIR . '/meta/metrics.php';

        $metrics = [];

        $metricBuilder = new MetricUnitBuilder();

        foreach ($metricsMeta as $metricData) {
            $systemName = $metricData['system_name'];
            $displayName = $metricData['display_name'];
            $type = $metricData['type'];

            $unit = \array_key_exists('metric_unit', $metricData)
                ? $metricData['metric_unit']
                : $metricData['unit'];

            $usageMetric = new UsageMetric(
                $systemName,
                $displayName,
                $type,
                $metricBuilder->createMetricUnitFromType($unit)
            );

            $usageMetric->withUnit($metricData['unit']);
            $usageMetric->withMetricUnit($metricData['metric_unit']);

            $metrics[] = $usageMetric;
        }

        return $metrics;
    }

    /**
     * @return MetricsFetcher
     */
    private function getMetricsFetcher()
    {
        return Locator::getInstance()->get(MetricsFetcherFactory::NAME);
    }
}