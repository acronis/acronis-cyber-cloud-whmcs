<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Util;

class UomConverter
{
    const STORAGE_MULTIPLIER = 1024;

    const BYTES = 'bytes';
    const KILOBYTES = 'kilobytes';
    const MEGABYTES = 'megabytes';
    const GIGABYTES = 'gigabytes';
    const TERABYTES = 'terabytes';

    const SECONDS = 'seconds';
    const MINUTES = 'minutes';
    const HOURS = 'hours';
    const DAYS = 'days';

    const QUANTITY = 'quantity';
    const FEATURE = 'feature';

    const STORAGE = [
        self::BYTES,
        self::KILOBYTES,
        self::MEGABYTES,
        self::GIGABYTES,
        self::TERABYTES,
    ];

    const TIME = [
        self::SECONDS,
        self::MINUTES,
        self::HOURS,
        self::DAYS,
    ];

    const KIND_QUANTITY = 'quantity';
    const KIND_FEATURE = 'feature';
    const KIND_TIME = 'time';
    const KIND_STORAGE = 'storage';

    const PROPERTY_VALUE = 'value';
    const PROPERTY_MEASUREMENT_UNIT = 'measurement_unit';

    /**
     * Convert storage and time between measurements
     *
     * @param int|float $value
     * @param string $from
     * @param string $to
     * @return int|float|null
     */
    public static function convert($value, $from, $to)
    {
        $uomFromKind = static::getMeasurementKind($from);
        $uomToKind = static::getMeasurementKind($to);

        if (is_null($uomFromKind)) {
            throw new \InvalidArgumentException(Str::format(
                'Original measurement "%s" is not supported.',
                $from
            ));
        }

        if (is_null($uomToKind)) {
            throw new \InvalidArgumentException(Str::format(
                'Target measurement "%s" is not supported.',
                $to
            ));
        }

        if ($uomFromKind !== $uomToKind) {
            throw new \InvalidArgumentException(Str::format(
                'Unable convert "%s" to "%s".',
                $from, $to
            ));
        }

        if ($uomFromKind === static::KIND_STORAGE) {
            return static::convertStorage($value, $from, $to);
        }

        if ($uomFromKind === static::KIND_TIME) {
            return static::convertTime($value, $from, $to);
        }

        return $value;
    }

    /**
     * Convert storage between measurements
     *
     * @param int|float $value
     * @param string $from
     * @param string $to
     * @return int|float|null
     */
    public static function convertStorage($value, $from, $to)
    {
        $measurements = static::getStorageMeasurements();

        return static::convertValue($measurements, $value, $from, $to);
    }

    /**
     * Convert time between measurements
     *
     * @param int|float $value
     * @param string $from
     * @param string $to
     * @return int|float|null
     */
    public static function convertTime($value, $from, $to)
    {
        $measurements = static::getTimeMeasurements();

        return static::convertValue($measurements, $value, $from, $to);
    }

    /**
     * Round up storage
     *
     * @param int|float $value
     * @param $measurementUnit
     * @return void
     */
    protected static function convertUpStorage(&$value, &$measurementUnit)
    {
        $measurements = array_keys(static::getStorageMeasurements());
        // remove first unit, as it matches base $measurementUnit
        array_shift($measurements);
        do {
            $unit = array_shift($measurements);
            if ($unit === $measurementUnit) {
                continue;
            }
            $value = static::convertStorage($value, $measurementUnit, $unit);
            $measurementUnit = $unit;
        } while (count($measurements) && $value > static::STORAGE_MULTIPLIER);
    }

    /**
     * Returns formatted measurement name
     *
     * @param string $name
     * @return string
     */
    public static function formatMeasurementName($name)
    {
        return trim(strtolower($name));
    }

    /**
     * Resolves measurement kind by name
     *
     * @param string $name
     * @return string|null
     */
    public static function getMeasurementKind($name)
    {
        $uomName = self::formatMeasurementName($name);

        if ($uomName === static::QUANTITY) {
            return static::KIND_QUANTITY;
        }

        if ($uomName === static::FEATURE) {
            return static::KIND_FEATURE;
        }

        if (in_array($uomName, static::STORAGE)) {
            return static::KIND_STORAGE;
        }

        if (in_array($uomName, static::TIME)) {
            return static::KIND_TIME;
        }

        return null;
    }

    private static function convertValue($measurements, $value, $from, $to)
    {
        $uomFromName = static::formatMeasurementName($from);
        $uomToName = static::formatMeasurementName($to);

        if (!isset($measurements[$uomFromName])) {
            throw new \InvalidArgumentException(Str::format(
                'Original measurement "%s" is not supported.',
                $uomFromName
            ));
        }

        if (!isset($measurements[$uomToName])) {
            throw new \InvalidArgumentException(Str::format(
                'Target measurement "%s" is not supported.',
                $uomToName
            ));
        }

        if ($value == 0 || $uomFromName === $uomToName) {
            return $value;
        }

        $uomFromValue = $measurements[$uomFromName];
        $uomToValue = $measurements[$uomToName];

        return $value * $uomFromValue / $uomToValue;
    }

    private static function getStorageMeasurements()
    {
        static $measurements = null;
        if (is_null($measurements)) {
            $multiplierValue = 1;
            $measurements = [
                self::BYTES => $multiplierValue,
                self::KILOBYTES => ($multiplierValue *= self::STORAGE_MULTIPLIER),
                self::MEGABYTES => ($multiplierValue *= self::STORAGE_MULTIPLIER),
                self::GIGABYTES => ($multiplierValue *= self::STORAGE_MULTIPLIER),
                self::TERABYTES => ($multiplierValue * self::STORAGE_MULTIPLIER),
            ];
        }

        return $measurements;
    }

    private static function getTimeMeasurements()
    {
        static $measurements = null;
        if (is_null($measurements)) {
            $multiplierValue = 1;
            $measurements = [
                self::SECONDS => $multiplierValue,
                self::MINUTES => ($multiplierValue *= 60),
                self::HOURS => ($multiplierValue *= 60),
                self::DAYS => ($multiplierValue *= 24),
            ];
        }

        return $measurements;
    }
}