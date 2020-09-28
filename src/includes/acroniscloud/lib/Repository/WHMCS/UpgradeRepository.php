<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Repository\WHMCS;

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
            ->orderBy(Upgrade::COLUMN_ID, 'desc')
            ->first();
    }
}