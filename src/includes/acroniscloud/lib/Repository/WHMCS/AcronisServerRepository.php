<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
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