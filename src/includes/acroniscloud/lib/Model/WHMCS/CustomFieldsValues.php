<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Model\WHMCS;

use AcronisCloud\Model\AbstractModel;

class CustomFieldsValues extends AbstractModel
{
    const TABLE = 'tblcustomfieldsvalues';

    const COLUMN_RELID = 'relid';
    const COLUMN_FIELDID = 'fieldid';
    const COLUMN_VALUE = 'value';

    protected $fillable = [
        self::COLUMN_RELID,
        self::COLUMN_FIELDID,
        self::COLUMN_VALUE,
    ];
}