<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Model\WHMCS;

use AcronisCloud\Model\AbstractModel;

class ProductConfigGroup extends AbstractModel
{
    const TABLE = 'tblproductconfiggroups';

    const COLUMN_ID = 'id';
    const COLUMN_NAME = 'name';
    const COLUMN_DESCRIPTION = 'description';

    public $timestamps = false;

    protected $fillable = [
        self::COLUMN_NAME,
        self::COLUMN_DESCRIPTION,
    ];

    /**
     * List of options that this group has
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function options()
    {
        return $this->hasMany(
            'AcronisCloud\Model\WHMCS\ProductConfigOption',
            ProductConfigOption::COLUMN_GID
        );
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getAttribute(static::COLUMN_NAME);
    }
}