<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Util\WHMCS;

use AcronisCloud\Service\Locator;
use AcronisCloud\Service\Logger\DatabaseLogging;
use AcronisCloud\Service\Logger\LoggerFactory;
use AcronisCloud\Service\Logger\Whmcs\ScopeWhmcsApiFormatter;

class LocalApi
{
    public static function decryptPassword($password)
    {
        $decrypted = static::call('decryptpassword', ['password2' => $password]);

        return $decrypted['password'];
    }

    public static function encryptPassword($password)
    {
        $encrypted = static::call('encryptpassword', ['password2' => $password]);

        return $encrypted['password'];
    }

    private static function call($method, $parameters)
    {
        $result = DatabaseLogging::runWithoutLogs(function () use ($method, $parameters) {
            return localAPI($method, $parameters);
        });

        $logger = Locator::getInstance()->get(LoggerFactory::NAME);

        $logger->debug('API call to WHMCS platform. METHOD: "{method}" PARAMETERS: {parameters} RESULT: {result}', [
            'module_log' => ScopeWhmcsApiFormatter::NAME,
            'method' => $method,
            'parameters' => $parameters,
            'result' => $result,
        ]);

        return $result;
    }
}