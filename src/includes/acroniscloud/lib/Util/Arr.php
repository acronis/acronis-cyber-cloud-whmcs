<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Util;

use ArrayAccess;
use InvalidArgumentException;

class Arr
{
    const VALUE_DELIMITER = '=';
    const PAIR_DELIMITER = ';';

    /**
     * @param array $values
     * @return string
     */
    public static function encode(array $values)
    {
        $pairs = [];
        foreach ($values as $k => $v) {
            $key = static::encodeValue($k);
            $value = static::encodeValue($v);

            $pairs[] = is_null($v) ? $key : $key . static::VALUE_DELIMITER . $value;
        }

        return implode(static::PAIR_DELIMITER, $pairs);
    }

    /**
     * @param string $string
     * @return array
     */
    public static function decode($string)
    {
        if (!is_string($string) || $string === '') {
            return [];
        }

        $pairs = preg_split('/(?<!\\\\)' . static::PAIR_DELIMITER . '/', $string);

        $values = [];
        foreach ($pairs as $pair) {
            $parts = preg_split('/(?<!\\\\)' . static::VALUE_DELIMITER . '/', $pair, 2);
            $key = static::decodeValue(static::get($parts, 0));
            $value = static::get($parts, 1);
            if ($value) {
                $value = static::decodeValue($value);
            }

            $values[$key] = $value;
        }

        return $values;
    }

    /**
     * @param array|ArrayAccess|object $array
     * @param $key
     * @return bool
     */
    public static function has($array, $key)
    {
        if (is_array($array)) {
            return array_key_exists($key, $array);
        }

        if (is_object($array)) {
            if ($array instanceof ArrayAccess) {
                return $array->offsetExists($key);
            } else {
                // First part catches the case when a property exists but a value equals NULL
                // Second part catches the case when a property does not exist but there is magic method __isset
                return array_key_exists($key, get_object_vars($array))
                    || isset($array->{$key});
            }
        }

        return false;
    }

    /**
     * @param array $array
     * @param $key
     * @param mixed $defaultValue
     * @return mixed
     */
    public static function get($array, $key, $defaultValue = null)
    {
        if (!static::has($array, $key)) {
            return $defaultValue;
        }

        if (is_array($array)) {
            return $array[$key];
        }

        if (is_object($array)) {
            if ($array instanceof ArrayAccess) {
                return $array->offsetGet($key);
            } else {
                return $array->{$key};
            }
        }

        return $defaultValue;
    }

    /**
     * get array value by nested path, separated by '.'
     * @example Arr::getByPath(['a' => ['b' => 1]], 'a.b'); // returns 1
     *
     * @param $array
     * @param $path
     * @param mixed $default
     * @return array|mixed|string
     */
    public static function getByPath($array, $path, $default = null)
    {
        $explodedPath = explode('.', $path);
        $temp = $array;
        foreach ($explodedPath as $key) {
            if (static::has($temp, $key)) {
                $temp = static::get($temp, $key);
            } else {
                return $default;
            }
        }

        return $temp;
    }

    /**
     * @param $key
     * @param array $array
     * @param int $value
     */
    public static function set(&$array, $key, $value)
    {
        if (is_array($array)) {
            $array[$key] = $value;
        }

        if (is_object($array)) {
            if ($array instanceof ArrayAccess) {
                $array->offsetSet($key, $value);
            } else {
                $array->{$key} = $value;
            }
        }
    }

    /**
     * Gets array/object key, 0 by default, increments by value, sets key.
     *
     * @param array $array
     * @param string $key
     * @param int $value
     */
    public static function increment(&$array, $key, $value)
    {
        $increasedValue = static::get($array, $key, 0) + $value;
        static::set($array, $key, $increasedValue);
    }

    /**
     * Example:
     *     $limits = ['a' => 11, 'b' => 22, 'c' => 33, 'd' => 44,];
     *     $usages = ['a' => 1,  'b' => 2,  'c' => 3,  'e' => 4,];
     *     $counters = Arr::merge([
     *          'limit' => $limits,
     *          'usage' => $usages,
     *     ], [
     *          'usage' => 0,
     *     ]);
     * Variable '$counters' equals to
     * [
     *     'a' => ['limit' => 11, 'usage' => 1,],
     *     'b' => ['limit' => 22, 'usage' => 2,],
     *     'c' => ['limit' => 33, 'usage' => 3,],
     *     'd' => ['limit' => 44, 'usage' => 0,],
     *     'e' => ['usage' => 4,],
     * ]
     *
     * @param array $arrays
     * @param array $defaultValues
     * @return array
     */
    public static function merge(array $arrays, array $defaultValues = [])
    {
        $rootKeys = array_keys(array_merge(...array_values($arrays)));

        $results = [];
        foreach ($rootKeys as $rootKey) {
            $values = [];
            foreach ($arrays as $key => $array) {
                if (static::has($array, $rootKey)) {
                    $values[$key] = static::get($array, $rootKey);
                    continue;
                }

                if (static::has($defaultValues, $key)) {
                    $values[$key] = static::get($defaultValues, $key);
                    continue;
                }
                // otherwise do not create the key in values
            }

            $results[$rootKey] = $values;
        }

        return $results;
    }

    /**
     * Returns the value of the first element in the provided array that satisfies the provided testing function.
     * Otherwise, null is returned.
     *
     * @param array| object $array
     * @param callable $fn
     * @return mixed|null
     */
    public static function find(&$array, $fn)
    {
        foreach ($array as $key => $item) {
            if ($fn($item, $key, $array)) {
                return $item;
            }
        }

        return null;
    }

    /**
     * Example:
     *     $countries = [
     *        ['country_code' => 'US', 'currency_code' => 'USD', 'name' => 'United States',],
     *        ['country_code' => 'RU', 'currency_code' => 'RUB', 'name' => 'Russia',],
     *        ['country_code' => 'FR', 'currency_code' => 'EUR', 'name' => 'France',],
     *     ];
     *     $currencies = Arr::map($countries, 'country_code', 'currency_code')
     * Variable '$currencies' equals to
     * [
     *     'US' => 'USD',
     *     'RU' => 'RUB',
     *     'FR' => 'EUR',
     * ]
     *
     * @param array $items
     * @param callable|string $keyProperty
     * @param callable|string $valueProperty
     * @param bool $unique true - throw an exception if the key is not unique
     * @return array
     */
    public static function map(array $items, $keyProperty, $valueProperty, $unique = true)
    {
        $map = [];
        foreach ($items as $itemKey => $item) {
            $key = static::retrieveProperty($keyProperty, $item, $itemKey);
            $value = static::retrieveProperty($valueProperty, $item, $itemKey);

            if ($unique && static::has($map, $key)) {
                throw new InvalidArgumentException(Str::format(
                    'Key "%s" is not unique.',
                    $key
                ));
            }

            $map[$key] = $value;
        }

        return $map;
    }

    private static function encodeValue($value)
    {
        return addcslashes($value, static::PAIR_DELIMITER . static::VALUE_DELIMITER);
    }

    private static function decodeValue($value)
    {
        return stripcslashes($value);
    }

    private static function retrieveProperty($property, $item, $key)
    {
        if (!is_string($property) && is_callable($property)) {
            return $property($item, $key);
        }

        return static::get($item, $property, null);
    }
}
