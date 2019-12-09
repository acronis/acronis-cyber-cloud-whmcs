<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Util;

class Str
{
    /**
     * Format message corresponding passed arguments
     * Usages:
     *      format('hello') -> 'hello'
     *      format('hello %s and %s', 'John', 'James') -> 'hello John and James'
     *      format(
     *          'hello :manager_name and :developer_name',
     *          [':manager_name' => 'John', ':developer_name' => 'James']
     *      ) -> 'hello John and James'
     *
     * @param $message
     * @return string
     */
    public static function format($message)
    {
        if (func_num_args() === 1) {
            return $message;
        }
        $args = func_get_args();
        if (is_array($args[1])) {
            return strtr($message, $args[1]);
        }

        return call_user_func_array('sprintf', $args);
    }

    public static function startsWith($haystack, $needle)
    {
        return mb_substr($haystack, 0, mb_strlen($needle)) === $needle;
    }

    /**
     * Checks if $haystack string ends with $needle
     *
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    public static function endsWith($haystack, $needle)
    {
        return $needle === ''
            || (($temp = strlen($haystack) - strlen($needle)) >= 0
            && strpos($haystack, $needle, $temp) !== false);
    }

    /**
     * @param string $haystack
     * @param string $regexp
     * @param int $groupNumber
     * @return bool|array
     */
    public static function findTag($haystack, $tagRegexp, $offset = 0, $groupNumber = 0)
    {
        $match = static::getRegexpMatchWithCapturedOffset($haystack, $tagRegexp, $offset, $groupNumber);

        if (is_null($match)) {
            return null;
        }

        return [
            'tag' => $match[0],
            'start_position' => $match[1],
            'end_position' => $match[1] + mb_strlen($match[0]),
        ];
    }

    /**
     * @param string $haystack
     * @param string $regexp
     * @param int $initialOffset
     * @param int $groupNumber
     * @return null
     */
    private static function getRegexpMatchWithCapturedOffset($haystack, $regexp, $initialOffset = 0, $groupNumber = 0)
    {
        if ($initialOffset !== 0) {
            $haystack = mb_substr($haystack, $initialOffset);
        }

        preg_match($regexp, $haystack, $matches, PREG_OFFSET_CAPTURE);

        if (!isset($matches[$groupNumber])) {
            return null;
        }

        // We should add initial offset if we've cut some symbols via mb_substr()
        // to get regexp match start offset of original $haystack
        $byteOffset = $matches[$groupNumber][1];
        $charOffset = mb_strlen(substr($haystack, 0, $byteOffset)) + $initialOffset;
        $matches[$groupNumber][1] = $charOffset;

        return $matches[$groupNumber];
    }
}
