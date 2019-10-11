<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Model\WHMCS;

use AcronisCloud\Model\AbstractModel;

/**
 * @property ProductConfigGroup $group
 */
class ProductConfigOption extends AbstractModel
{
    const TABLE = 'tblproductconfigoptions';

    const COLUMN_GID = 'gid';
    const COLUMN_OPTION_NAME = 'optionname';
    const COLUMN_OPTION_TYPE = 'optiontype';
    const COLUMN_QTY_MINIMUM = 'qtyminimum';
    const COLUMN_QTY_MAXIMUM = 'qtymaximum';
    const COLUMN_ORDER = 'order';
    const COLUMN_HIDDEN = 'hidden';

    const RELATION_SUB_OPTIONS = 'subOptions';

    const OPTION_TYPE_CHECKBOX = 3;
    const OPTION_TYPE_QUANTITY = 4;

    public $timestamps = false;

    protected $fillable = [
        self::COLUMN_GID,
        self::COLUMN_OPTION_NAME,
        self::COLUMN_OPTION_TYPE,
        self::COLUMN_QTY_MINIMUM,
        self::COLUMN_QTY_MAXIMUM,
        self::COLUMN_ORDER,
        self::COLUMN_HIDDEN,
    ];

    /**
     * List of sub-options that this option has
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subOptions()
    {
        return $this->hasMany(
            'AcronisCloud\Model\WHMCS\ProductConfigSubOption',
            ProductConfigSubOption::COLUMN_CONFIG_ID
        );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function group()
    {
        return $this->hasOne(
            'AcronisCloud\Model\WHMCS\ProductConfigGroup',
            ProductConfigGroup::COLUMN_ID,
            static::COLUMN_GID
        );
    }
}