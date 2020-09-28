<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Repository\WHMCS;

use AcronisCloud\Model\WHMCS\Product;
use AcronisCloud\Model\WHMCS\Server;
use AcronisCloud\Model\WHMCS\Service;
use AcronisCloud\Repository\AbstractRepository;
use Illuminate\Database\Eloquent\Builder;

class ServiceRepository extends AbstractRepository
{
    /**
     * @param int $serviceId
     * @return Service
     */
    public function find($serviceId)
    {
        return Service::find($serviceId);
    }

    /**
     * @param int $userId
     * @return Service[]
     */
    public function getClientServicesWithServers($userId)
    {
        return $this->getClientServicesQuery($userId)
            ->get()
            ->all();
    }

    /**
     * @param int $userId
     * @param int $id
     * @return Service
     */
    public function getClientServiceWithServersById($userId, $id)
    {
        return $this->getClientServicesQuery($userId)
            ->where(Service::COLUMN_ID, $id)
            ->get()
            ->first();
    }

    /**
     * @param $userId
     * @return Builder
     */
    protected function getClientServicesQuery($userId)
    {
        return Service::where(Service::COLUMN_USER_ID, $userId)
            ->whereHas(Service::RELATION_PRODUCT, function ($query) {
                $query->where(Product::COLUMN_SERVER_TYPE, ACRONIS_CLOUD_SERVICE_NAME);
            })
            ->with([
                Service::RELATION_CLOUD_SERVER => function ($query) {
                    $query->where(Server::COLUMN_TYPE, ACRONIS_CLOUD_SERVICE_NAME);
                }
            ]);
    }
}