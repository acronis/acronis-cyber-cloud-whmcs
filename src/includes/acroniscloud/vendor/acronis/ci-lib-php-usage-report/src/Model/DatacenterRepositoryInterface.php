<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. All rights reserved
 */

namespace Acronis\UsageReport\Model;

interface DatacenterRepositoryInterface
{

    /**
     * @return DatacenterInterface[]
     */
    public function getDatacenters();

    /**
     * @param int $id
     *
     * @return DatacenterInterface
     */
    public function getDatacenterById($id);
}