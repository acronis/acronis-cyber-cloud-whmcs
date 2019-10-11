<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\MetaInfo;

use AcronisCloud\Util\Arr;
use AcronisCloud\Util\UomConverter;

class ConfigurableOption
{
    const PROPERTY_FRIENDLY_NAME = 'friendly_name';
    const PROPERTY_MEASUREMENT_UNIT = 'measurement_unit';
    const PROPERTY_MEASUREMENT_UNIT_NAME = 'measurement_unit_name';

    /** @var array */
    private $data;

    /**
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getFriendlyName()
    {
        return Arr::get($this->data, static::PROPERTY_FRIENDLY_NAME, '');
    }

    /**
     * @return string
     */
    public function getMeasurementUnit()
    {
        return (string)Arr::get($this->data, static::PROPERTY_MEASUREMENT_UNIT, UomConverter::FEATURE);
    }

    /**
     * @return string
     */
    public function getMeasurementUnitName()
    {
        return Arr::get($this->data, static::PROPERTY_MEASUREMENT_UNIT_NAME, '');
    }
}