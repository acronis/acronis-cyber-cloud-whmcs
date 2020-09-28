<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Repository\WHMCS;

use Acronis\UsageReport\Model\DatacenterInterface;
use Acronis\UsageReport\Model\DatacenterRepositoryInterface;
use AcronisCloud\Model\WHMCS\Server;
use AcronisCloud\Repository\AbstractRepository;
use Illuminate\Database\Eloquent\Collection;

class AcronisServerRepository extends AbstractRepository implements DatacenterRepositoryInterface
{
    /**
     * @return Collection
     */
    public function all()
    {
        return Server::where(Server::COLUMN_TYPE, ACRONIS_CLOUD_SERVICE_NAME)
            ->get();
    }

    /**
     * @param $id
     * @return Server
     */
    public function find($id)
    {
        return Server::find($id);
    }

    /**
     * @return DatacenterInterface[]|Collection
     */
    public function getDatacenters()
    {
        return Server::where(Server::COLUMN_TYPE, ACRONIS_CLOUD_SERVICE_NAME)
            ->where(Server::COLUMN_DISABLED, false)
            ->get();
    }

    /**
     * @param int $id
     * @return DatacenterInterface
     */
    public function getDatacenterById($id)
    {
        return $this->find($id);
    }
}