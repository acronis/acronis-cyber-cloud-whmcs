<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Util\WHMCS;

use AcronisCloud\Util\Arr;

class ConfigurableOptionHelper
{
    const NAMES_DELIMITER = '|';
    const PROPERTIES_DELIMITER = ':';

    const NAME = 'name';
    const FRIENDLY_NAME = 'friendly_name';

    const OFFERING_ITEM_NAME = 'offering_item_name';
    const MEASUREMENT_UNIT = 'measurement_unit';
    const INFRA_ID = 'infra_id';

    /**
     * @param string $offeringItemFriendlyName
     * @param string $offeringItemName
     * @param string $offeringItemUnit
     * @param string $offeringItemInfraId
     * @return string
     */
    public static function getFullName(
        $offeringItemFriendlyName,
        $offeringItemName,
        $offeringItemUnit,
        $offeringItemInfraId = null
    ) {
        // edition can be missing, so using array_filter
        $optionFullName = implode(static::NAMES_DELIMITER, array_filter([
            static::NAME => implode(static::PROPERTIES_DELIMITER, [
                $offeringItemName,
                $offeringItemUnit,
                $offeringItemInfraId,
            ]),
            static::FRIENDLY_NAME => $offeringItemFriendlyName,
        ]));

        return $optionFullName;
    }

    /**
     * @param string $fullName
     * @return array
     */
    public static function parseFullName($fullName)
    {
        $parts = explode(static::NAMES_DELIMITER, $fullName, 2);

        return [
            static::NAME => trim(Arr::get($parts, 0, '')),
            static::FRIENDLY_NAME => trim(Arr::get($parts, 1, '')),
        ];
    }

    /**
     * @param string $name
     * @return array
     */
    public static function parseName($name)
    {
        $parts = explode(static::PROPERTIES_DELIMITER, $name, 3);

        return [
            static::OFFERING_ITEM_NAME => trim(Arr::get($parts, 0, '')),
            static::MEASUREMENT_UNIT => trim(Arr::get($parts, 1, '')),
            static::INFRA_ID => trim(Arr::get($parts, 2, '')),
        ];
    }
}