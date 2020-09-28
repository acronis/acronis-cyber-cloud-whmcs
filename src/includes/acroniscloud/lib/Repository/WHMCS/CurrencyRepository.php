<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Repository\WHMCS;

use AcronisCloud\Model\WHMCS\Currency;
use AcronisCloud\Repository\AbstractRepository;

class CurrencyRepository extends AbstractRepository
{
    /**
     * @param $id
     * @return Currency
     */
    public function getCurrency($id)
    {
        return Currency::where(Currency::COLUMN_ID, $id)
            ->first();
    }
}