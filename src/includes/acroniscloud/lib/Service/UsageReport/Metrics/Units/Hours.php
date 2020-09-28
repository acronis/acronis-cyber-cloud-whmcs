<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\UsageReport\Metrics\Units;

use WHMCS\UsageBilling\Metrics\Units\AbstractUnit;

class Hours extends AbstractUnit
{
    public function __construct(
        $name = 'Hours',
        $singlePerUnitName = 'Hour',
        $pluralPerUnitName = 'Hours',
        $prefix = null,
        $suffix = 'h'
    ) {
        parent::__construct($name, $singlePerUnitName, $pluralPerUnitName, $prefix, $suffix);
    }

    public function type()
    {
        return 'float';
    }
}