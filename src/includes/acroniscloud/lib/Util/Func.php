<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Util;

use RuntimeException;

class Func
{
    /**
     * @param int $deep
     * @return string
     */
    public static function getCallerName($deep = 2)
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, $deep + 1);
        $callerInfo = Arr::get($backtrace, $deep);

        if (!isset($callerInfo['function'])) {
            throw new RuntimeException('Unable to resolve a caller name.');
        }

        if (isset($callerInfo['class'])) {
            return $callerInfo['class'] . '::' . $callerInfo['function'];
        }

        return $callerInfo['function'];
    }

    /**
     * @param object $object
     * @param string $methodName
     * @param array $parameters
     * @return mixed
     */
    public static function call($object, $methodName, array $parameters = [])
    {
        if (!method_exists($object, $methodName)) {
            throw new RuntimeException(Str::format(
                'Class "%s" does not have method "%s".',
                get_class($object), $methodName
            ));
        }

        return call_user_func([$object, $methodName], ...$parameters);
    }
}