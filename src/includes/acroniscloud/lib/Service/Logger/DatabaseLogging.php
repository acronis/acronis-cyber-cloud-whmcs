<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Logger;

use AcronisCloud\Service\Locator;
use AcronisCloud\Service\Logger\Whmcs\ScopeDbQueryFormatter;
use WHMCS\Database\Capsule;

class DatabaseLogging
{
    private static $enabled = false;

    public static function runWithLogs($fn)
    {
        return static::call($fn, true);
    }

    public static function runWithoutLogs($fn)
    {
        return static::call($fn, false);
    }

    private static function call($fn, $enabled)
    {
        static::registerListener();

        $currentStatus = static::$enabled;
        static::$enabled = $enabled;

        try {
            $result = $fn();
        } finally {
            static::$enabled = $currentStatus;
        }

        return $result;
    }

    private static function registerListener()
    {
        static $registered = false;
        if ($registered) {
            return;
        }

        $logger = Locator::getInstance()->get(LoggerFactory::NAME);

        /** @var \Illuminate\Database\Connection $connection */
        $connection = Capsule::connection();
        $connection->listen(function ($query) use ($logger) {
            if (static::$enabled) {
                $logger->debug(
                    'Database query: {query} Bindings: {bindings}',
                    [
                        'module_log' => ScopeDbQueryFormatter::NAME,
                        'query' => $query->sql,
                        'bindings' => $query->bindings,
                    ]
                );
            }
        });

        $registered = true;
    }
}