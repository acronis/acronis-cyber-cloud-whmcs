<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

use AcronisCloud\Service\Dispatcher\Dispatcher;
use AcronisCloud\Service\Dispatcher\DispatcherFactory;
use AcronisCloud\Service\Dispatcher\RequestInterface;
use AcronisCloud\Service\Locator;

require_once __DIR__ . '/../acroniscloud/bootstrap.php';

$hooks = [
    'ServerAdd',
    'ServerDelete',
    'ServerEdit',
    'ClientEdit',
    'ProductEdit',
    'ServiceDelete',
    'AdminAreaHeaderOutput',
    'ClientAreaHeaderOutput',
    'OrderProductUpgradeOverride',
];

$outputHooks = [
    'AdminAreaHeaderOutput',
    'ClientAreaHeaderOutput',
];

foreach ($hooks as $hook) {
    add_hook($hook, 1, function ($parameters) use ($hook, $outputHooks) {
        /** @var Dispatcher $dispatcher */
        $response = '';
        try {
            $dispatcher = Locator::getInstance()->get(DispatcherFactory::NAME);

            $response = $dispatcher->dispatch(
                __FILE__,
                ACRONIS_CLOUD_SERVICE_NAME . RequestInterface::ACTION_NAME_DELIMITER . $hook,
                $parameters
            );
        } catch (Exception $e) {
            // always suppress exceptions for hooks not to affect other WHMCS modules
        }

        return in_array($hook, $outputHooks) ? $response : $parameters;
    });
}