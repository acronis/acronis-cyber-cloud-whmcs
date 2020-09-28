<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\UsageReport\Metrics;

use AcronisCloud\Util\UomConverter;
use WHMCS\UsageBilling\Metrics\Units\AbstractUnit;

class MetricUnitBuilder
{
    const MAP = [
        UomConverter::BYTES => 'WHMCS\UsageBilling\Metrics\Units\Bytes',
        UomConverter::KILOBYTES => 'WHMCS\UsageBilling\Metrics\Units\KiloBytes',
        UomConverter::MEGABYTES => 'WHMCS\UsageBilling\Metrics\Units\MegaBytes',
        UomConverter::GIGABYTES => 'WHMCS\UsageBilling\Metrics\Units\GigaBytes',
        UomConverter::FEATURE => 'WHMCS\UsageBilling\Metrics\Units\Accounts',
        UomConverter::QUANTITY => 'WHMCS\UsageBilling\Metrics\Units\Accounts',
        UomConverter::SECONDS => 'AcronisCloud\Service\UsageReport\Metrics\Units\Seconds',
        UomConverter::HOURS => 'AcronisCloud\Service\UsageReport\Metrics\Units\Hours',
    ];

    /**
     * @param string $type
     *
     * @return AbstractUnit
     *
     * @throws \Exception
     */
    public function createMetricUnitFromType($type)
    {
        if (!\array_key_exists($type, static::MAP)) {
            throw new \InvalidArgumentException(\sprintf('Unknown metric type: %s', $type));
        }

        $unitType = static::MAP[$type];

        return new $unitType();
    }
}