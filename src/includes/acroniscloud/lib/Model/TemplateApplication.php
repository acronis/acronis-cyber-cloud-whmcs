<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;
use WHMCS\Module\Server\AcronisCloud\Controller\ClientAreaApi;

class TemplateApplication extends AbstractModel implements StatusInterface
{
    const TABLE = 'acroniscloud_service_template_applications';

    const COLUMN_TYPE = 'type';
    const COLUMN_EDITIONS = 'editions';
    const COLUMN_EDITIONS_SIZE = 1024;
    const COLUMN_STATUS = 'status';
    const COLUMN_TEMPLATE_ID = 'template_id';

    /** @var string Used for unique key name in migration to create the table */
    const UNIQUE_TEMPLATE_TYPE = 'application_template_id_type_unique';

    /** @var string Used to access POST request's payload data and relationship method */
    const RELATION_OFFERING_ITEMS = 'offeringItems';

    public $timestamps = false;

    /**
     * Indicates whether attributes are snake cased on arrays.
     *
     * @var bool
     */
    public static $snakeAttributes = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        self::COLUMN_TYPE,
        self::COLUMN_EDITIONS,
        self::COLUMN_TEMPLATE_ID,
        self::COLUMN_STATUS,
    ];

    /**
     *  Hide unneeded for UI columns
     *
     * @var array
     */
    protected $hidden = [
        self::COLUMN_ID,
        self::COLUMN_TEMPLATE_ID,
        self::RELATION_OFFERING_ITEMS,
    ];


    /**
     * Accessor (getOfferingItemsAttribute) that is appended to the model. Result is that column
     * 'offeringItems' is renamed to 'offering_items' when running select
     *
     * @var array
     */
    protected $appends = [
        ClientAreaApi::PROPERTY_OFFERING_ITEMS
    ];

    /**
     * Cast the editions to native array type.
     *
     * @var array
     */
    protected $casts = [
        self::COLUMN_EDITIONS => self::TYPE_ARRAY,
    ];

    /**
     * Get the comments for the blog post.
     *
     * @return HasMany
     */
    public function offeringItems()
    {
        return $this->hasMany(TemplateOfferingItem::class);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getAttributeValue(static::COLUMN_TYPE);
    }

    /**
     * @return array
     */
    public function getEditions()
    {
        return $this->getAttributeValue(static::COLUMN_EDITIONS);
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->getAttributeValue(static::COLUMN_STATUS) === static::STATUS_ACTIVE;
    }

    /**
     * Get the offering items with snake_case naming of the property.
     *
     * @return HasMany
     */
    public function getOfferingItemsAttribute()
    {
        return $this->offeringItems()->getResults();
    }
}
