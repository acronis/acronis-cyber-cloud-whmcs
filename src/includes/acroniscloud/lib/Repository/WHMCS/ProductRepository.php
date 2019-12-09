<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Repository\WHMCS;

use AcronisCloud\Model\WHMCS\Product;
use AcronisCloud\Repository\AbstractRepository;
use Illuminate\Database\Eloquent\Model;

class ProductRepository extends AbstractRepository
{
    /**
     * @param $id
     * @return Model
     */
    public function find($id)
    {
        return Product::find($id);
    }
}