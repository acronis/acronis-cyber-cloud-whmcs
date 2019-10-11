<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Model;

class TemplateOfferingItem extends AbstractModel implements StatusInterface
{
    const TABLE = 'acroniscloud_service_template_offering_items';

    const COLUMN_ID = 'id';
    const COLUMN_NAME = 'name';
    const COLUMN_STATUS = 'status';
    const COLUMN_QUOTA_VALUE = 'quota_value';
    const COLUMN_MEASUREMENT_UNIT = 'measurement_unit';
    const COLUMN_INFRA_ID = 'infra_id';
    const COLUMN_APPLICATION_ID = 'template_application_id';

    const FOREIGN_KEY_CONSTRAIN = 'template_application_id_foreign';

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        self::COLUMN_NAME,
        self::COLUMN_INFRA_ID,
        self::COLUMN_STATUS,
        self::COLUMN_QUOTA_VALUE,
        self::COLUMN_MEASUREMENT_UNIT,
    ];

    /**
     *  Hide unneeded for UI columns
     *
     * @var array
     */
    protected $hidden = [
        self::COLUMN_ID,
        self::COLUMN_APPLICATION_ID,
    ];

    /**
     * @return int
     */
    public function getId()
    {
        return $this->getAttributeValue(static::COLUMN_ID);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getAttributeValue(static::COLUMN_NAME);
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->getAttributeValue(static::COLUMN_STATUS) === static::STATUS_ACTIVE;
    }

    /**
     * @return int|null
     */
    public function getQuotaValue()
    {
        return $this->getAttributeValue(static::COLUMN_QUOTA_VALUE);
    }

    /**
     * @return string
     */
    public function getMeasurementUnit()
    {
        return $this->getAttributeValue(static::COLUMN_MEASUREMENT_UNIT);
    }

    /**
     * @return string|null
     */
    public function getInfraId()
    {
        return $this->getAttributeValue(static::COLUMN_INFRA_ID);
    }
}
