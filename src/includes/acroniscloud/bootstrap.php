<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

use AcronisCloud\Service\Locator;

if (!defined('WHMCS')) {
    die('This file cannot be accessed directly.');
}
require_once __DIR__ . '/constants.php';
require_once __DIR__ . '/autoload.php';

$services = require __DIR__ . '/services.php';
$locator = Locator::getInstance();
foreach ($services as $serviceName => $serviceFactory) {
    $locator->addFactory($serviceName, $serviceFactory);
}