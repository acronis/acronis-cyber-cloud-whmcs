<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\UsageReport\Metrics;

use AcronisCloud\Util\UomConverter;
use WHMCS\UsageBilling\Metrics\Metric;

/**
 * @method systemName
 * @method withUsage
 */
class UsageMetric extends Metric
{

    /**
     * @var string
     */
    protected $unit;
    
    /**
     * @var string|null
     */
    protected $metricUnit;
    
    /**
     * @param string $unit
     */
    public function withUnit($unit)
    {
        $this->unit = $unit;
    }

    public function withMetricUnit($metricUnit)
    {
        $this->metricUnit = $metricUnit;
    }

    public function getConvertedValue($value)
    {
        if (null === $this->metricUnit) {
            return $value;
        }

        return UomConverter::convert($value, $this->unit, $this->metricUnit);
    }
}