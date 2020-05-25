<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Model\WHMCS;

use AcronisCloud\Model\AbstractModel;

class CustomField extends AbstractModel
{
    const TABLE = 'tblcustomfields';

    const COLUMN_TYPE = 'type';
    const COLUMN_RELID = 'relid';
    const COLUMN_FIELDNAME = 'fieldname';
    const COLUMN_FIELDTYPE = 'fieldtype';
    const COLUMN_DESCRIPTION = 'description';
    const COLUMN_REGEXPR = 'regexpr';
    const COLUMN_ADMINONLY = 'adminonly';
    const COLUMN_REQUIRED = 'required';
    const COLUMN_SHOWORDER = 'showorder';
    const COLUMN_SHOWINVOICE = 'showinvoice';
    const COLUMN_SORTORDER = 'sortorder';

    const TYPE_PRODUCT = 'product';
    const FIELD_TYPE_TEXT = 'text';
    const FIELD_TYPE_PASSWORD = 'password';

    const SETTING_ON = 'on';

    protected $fillable = [
        self::COLUMN_TYPE,
        self::COLUMN_RELID,
        self::COLUMN_FIELDNAME,
        self::COLUMN_FIELDTYPE,
        self::COLUMN_DESCRIPTION,
        self::COLUMN_ADMINONLY,
        self::COLUMN_REQUIRED,
        self::COLUMN_SHOWORDER,
        self::COLUMN_SORTORDER,
        self::COLUMN_REGEXPR,
    ];
}