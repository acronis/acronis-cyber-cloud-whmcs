<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\MetaInfo;

use AcronisCloud\Util\Arr;
use AcronisCloud\Util\MemoizeTrait;

class OfferingItemMeta extends AbstractMeta
{
    use MemoizeTrait;

    const PROPERTY_APPLICATION_TYPE = 'application_type';
    const PROPERTY_CONFIGURABLE_OPTION = 'configurable_option';
    const PROPERTY_OFFERING_ITEM_NAME = 'offering_item_name';
    const PROPERTY_OFFERING_ITEM_FRIENDLY_NAME = 'offering_item_friendly_name';
    const PROPERTY_MEASUREMENT_UNIT = 'measurement_unit';
    const PROPERTY_EDITION_NAME = 'edition_name';
    const PROPERTY_CHILD_OFFERING_ITEMS = 'child_offering_items';
    const PROPERTY_RESOURCE_TYPE = 'resource_type';
    const PROPERTY_CAPABILITY = 'capability';

    /**
     * @return string
     */
    public function getApplicationType()
    {
        return (string)Arr::get($this->data, static::PROPERTY_APPLICATION_TYPE, '');
    }

    /**
     * @return string
     */
    public function getOfferingItemName()
    {
        return (string)Arr::get($this->data, static::PROPERTY_OFFERING_ITEM_NAME, '');
    }

    /**
     * @return string
     */
    public function getOfferingItemFriendlyName()
    {
        return Arr::get($this->data, static::PROPERTY_OFFERING_ITEM_FRIENDLY_NAME, '');
    }

    /**
     * @return string
     */
    public function getMeasurementUnit()
    {
        return (string)Arr::get($this->data, static::PROPERTY_MEASUREMENT_UNIT, '');
    }

    /**
     * @return string|null
     */
    public function getEditionName()
    {
        return (string)Arr::get($this->data, static::PROPERTY_EDITION_NAME);
    }

    /**
     * @return ConfigurableOption
     */
    public function getConfigurableOption()
    {
        return $this->memoize(function () {
            return new ConfigurableOption(Arr::get($this->data, static::PROPERTY_CONFIGURABLE_OPTION, []));
        });
    }

    /**
     * @return string[]
     */
    public function getChildOfferingItems()
    {
        return Arr::get($this->data, static::PROPERTY_CHILD_OFFERING_ITEMS, []);
    }

    /**
     * @return string
     */
    public function getResourceType()
    {
        return Arr::get($this->data, static::PROPERTY_RESOURCE_TYPE);
    }

    /**
     * @return string
     */
    public function getCapability()
    {
        return Arr::get($this->data, static::PROPERTY_CAPABILITY);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return bool
     */
    public function isInfra()
    {
        return !is_null($this->getCapability());
    }
}