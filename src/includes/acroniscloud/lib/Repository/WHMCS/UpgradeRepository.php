<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Repository\WHMCS;

use AcronisCloud\Model\WHMCS\Product;
use AcronisCloud\Model\WHMCS\Upgrade;
use AcronisCloud\Repository\AbstractRepository;

class UpgradeRepository extends AbstractRepository
{
    /**
     * @param $id
     * @return Upgrade
     */
    public function find($id)
    {
        return Upgrade::find($id);
    }

    /**
     * @param $id
     * @return Upgrade
     */
    public function findLastForService($serviceId)
    {
        return Upgrade::where(Upgrade::COLUMN_RELID, $serviceId)
            ->where(Upgrade::COLUMN_TYPE, Upgrade::TYPE_PACKAGE)
            ->whereHas(Upgrade::RELATION_ORIGINAL_PRODUCT, function ($query) {
                $query->where(Product::COLUMN_SERVER_TYPE, ACRONIS_CLOUD_SERVICE_NAME);
            })
            ->orderBy(Upgrade::COLUMN_ID, 'desc')
            ->first();
    }
}