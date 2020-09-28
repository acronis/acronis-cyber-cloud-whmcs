<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\UsageReport\Metrics\Units;

use WHMCS\UsageBilling\Metrics\Units\AbstractUnit;

class Seconds extends AbstractUnit
{
    public function __construct(
        $name = 'Seconds',
        $singlePerUnitName = 'Second',
        $pluralPerUnitName = 'Seconds',
        $prefix = null,
        $suffix = 'sec'
    ) {
        parent::__construct($name, $singlePerUnitName, $pluralPerUnitName, $prefix, $suffix);
    }

    public function type()
    {
        return 'int';
    }
}