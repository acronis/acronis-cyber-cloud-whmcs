<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
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