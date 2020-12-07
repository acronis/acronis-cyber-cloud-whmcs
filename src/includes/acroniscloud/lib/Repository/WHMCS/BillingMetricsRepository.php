<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Repository\WHMCS;

use AcronisCloud\Model\WHMCS\BillingMetrics;
use AcronisCloud\Repository\AbstractRepository;

class BillingMetricsRepository extends AbstractRepository
{
    /**
     * @param $id
     * @return bool
     */
    public function activeAcronisMetricsExist()
    {
        return BillingMetrics::where(BillingMetrics::COLUMN_TYPE, BillingMetrics::TYPE_PRODUCT)
            ->where(BillingMetrics::COLUMN_MODULE, ACRONIS_CLOUD_SERVICE_NAME)
            ->where(BillingMetrics::COLUMN_IS_HIDDEN, BillingMetrics::NOT_HIDDEN)
            ->exists();
    }
}