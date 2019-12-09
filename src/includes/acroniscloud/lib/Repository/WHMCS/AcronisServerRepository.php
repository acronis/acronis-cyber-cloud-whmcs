<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Repository\WHMCS;

use AcronisCloud\Model\WHMCS\Server;
use AcronisCloud\Repository\AbstractRepository;
use Illuminate\Database\Eloquent\Collection;

class AcronisServerRepository extends AbstractRepository
{
    /**
     * @return Collection
     */
    public function all()
    {
        return Server::where('type', ACRONIS_CLOUD_SERVICE_NAME)
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
}