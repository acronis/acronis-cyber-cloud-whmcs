<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
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