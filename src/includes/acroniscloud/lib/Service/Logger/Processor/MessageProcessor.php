<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Logger\Processor;

use AcronisCloud\Util\Str;

class MessageProcessor
{
    const OPEN_TAG = '{';
    const CLOSE_TAG = '}';

    public function __invoke(array $record)
    {
        $message = $record['message'];

        if (!is_string($message)) {
            $record['message'] = $this->stringify($message);

            return $record;
        }

        if (strpos($message, static::OPEN_TAG) === false) {
            // Do nothing if there are no placeholders
            return $record;
        }

        $replacements = [];
        foreach ($record['context'] as $key => $value) {
            $replacements[$this->getPlaceholderName($key)] = $this->stringify($value);
        }

        $record['message'] = Str::format($message, $replacements);

        return $record;
    }

    protected function getPlaceholderName($key)
    {
        return static::OPEN_TAG . $key . static::CLOSE_TAG;
    }

    /**
     * @param $value
     * @return string
     */
    protected function stringify($value)
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_object($value)) {
            if ($value instanceof \Closure) {
                return '[instanceof Closure]';
            }
            if ($value instanceof \DateTime) {
                return $value->format('c');
            }
            if (method_exists($value, '__toString')) {
                return (string)$value;
            }

            return $this->encode($value);
        }

        if (is_null($value) || is_scalar($value) || is_array($value)) {
            return $this->encode($value);
        }

        return '[' . gettype($value) . ']';
    }

    /**
     * @param $value
     * @return false|string
     */
    protected function encode($value)
    {
        return json_encode($value, JSON_UNESCAPED_SLASHES);
    }
}