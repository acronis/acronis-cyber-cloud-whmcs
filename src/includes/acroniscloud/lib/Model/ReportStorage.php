<?php
/**
 * @Copyright © 2003-2020 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Model;

class ReportStorage extends AbstractModel
{
    const TABLE = 'acroniscloud_service_report_storage';

    const COLUMN_KEY = 'key';
    const COLUMN_VALUE = 'value';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        self::COLUMN_KEY,
        self::COLUMN_VALUE,
    ];

    /**
     * Hide unneeded for UI columns
     *
     * @var array
     */
    protected $hidden = [
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->getAttributeValue(static::COLUMN_KEY);
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->getAttributeValue(static::COLUMN_VALUE);
    }

}
