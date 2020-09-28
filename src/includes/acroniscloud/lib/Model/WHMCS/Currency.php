<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Model\WHMCS;

use AcronisCloud\Model\AbstractModel;

class Currency extends AbstractModel
{
    const TABLE = 'tblcurrencies';

    const COLUMN_CODE = 'code';

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->getAttributeValue(static::COLUMN_CODE);
    }
}