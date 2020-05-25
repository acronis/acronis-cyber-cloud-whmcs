<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

use AcronisCloud\Service\Dispatcher\Dispatcher;
use AcronisCloud\Service\Dispatcher\DispatcherFactory;
use AcronisCloud\Service\Locator;

require_once __DIR__ . '/../../../includes/acroniscloud/bootstrap.php';

function acroniscloud_config()
{
    /** @var Dispatcher $dispatcher */
    $dispatcher = Locator::getInstance()->get(DispatcherFactory::NAME);

    return $dispatcher->dispatch(__FILE__, __FUNCTION__);
}

function acroniscloud_activate()
{
    /** @var Dispatcher $dispatcher */
    $dispatcher = Locator::getInstance()->get(DispatcherFactory::NAME);

    return $dispatcher->dispatch(__FILE__, __FUNCTION__);
}

function acroniscloud_deactivate()
{
    /** @var Dispatcher $dispatcher */
    $dispatcher = Locator::getInstance()->get(DispatcherFactory::NAME);

    return $dispatcher->dispatch(__FILE__, __FUNCTION__);
}

function acroniscloud_output()
{
    /** @var Dispatcher $dispatcher */
    $dispatcher = Locator::getInstance()->get(DispatcherFactory::NAME);

    echo $dispatcher->dispatch(__FILE__, __FUNCTION__);
}


function acroniscloud_upgrade($parameters)
{
    /** @var Dispatcher $dispatcher */
    $dispatcher = Locator::getInstance()->get(DispatcherFactory::NAME);

    echo $dispatcher->dispatch(__FILE__, __FUNCTION__, $parameters);
}

