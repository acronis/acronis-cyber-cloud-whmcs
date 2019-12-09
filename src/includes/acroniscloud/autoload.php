<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require __DIR__ . '/../../vendor/autoload.php';
$loader->addPsr4('AcronisCloud\\', __DIR__ . '/lib');
$loader->addPsr4('Acronis\\Cloud\\Client\\', __DIR__ . '/vendor/acronis/ci-lib-php-cloud-client/lib');
