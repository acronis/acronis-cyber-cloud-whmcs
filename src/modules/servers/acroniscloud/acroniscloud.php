<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

use AcronisCloud\Service\Dispatcher\Dispatcher;
use AcronisCloud\Service\Dispatcher\DispatcherFactory;
use AcronisCloud\Service\Locator;

require_once __DIR__ . '/../../../includes/acroniscloud/bootstrap.php';

function acroniscloud_MetaData()
{
    /** @var Dispatcher $dispatcher */
    $dispatcher = Locator::getInstance()->get(DispatcherFactory::NAME);

    return $dispatcher->dispatch(__FILE__, __FUNCTION__);
}

function acroniscloud_ConfigOptions()
{
    /** @var Dispatcher $dispatcher */
    $dispatcher = Locator::getInstance()->get(DispatcherFactory::NAME);

    return $dispatcher->dispatch(__FILE__, __FUNCTION__);
}

function acroniscloud_CreateAccount($parameters)
{
    /** @var Dispatcher $dispatcher */
    $dispatcher = Locator::getInstance()->get(DispatcherFactory::NAME);

    return $dispatcher->dispatch(__FILE__, __FUNCTION__, $parameters);
}

function acroniscloud_ChangePackage($parameters)
{
    /** @var Dispatcher $dispatcher */
    $dispatcher = Locator::getInstance()->get(DispatcherFactory::NAME);

    return $dispatcher->dispatch(__FILE__, __FUNCTION__, $parameters);
}

function acroniscloud_SuspendAccount($parameters)
{
    /** @var Dispatcher $dispatcher */
    $dispatcher = Locator::getInstance()->get(DispatcherFactory::NAME);

    return $dispatcher->dispatch(__FILE__, __FUNCTION__, $parameters);
}

function acroniscloud_UnsuspendAccount($parameters)
{
    /** @var Dispatcher $dispatcher */
    $dispatcher = Locator::getInstance()->get(DispatcherFactory::NAME);

    return $dispatcher->dispatch(__FILE__, __FUNCTION__, $parameters);
}

function acroniscloud_TerminateAccount($parameters)
{
    /** @var Dispatcher $dispatcher */
    $dispatcher = Locator::getInstance()->get(DispatcherFactory::NAME);

    return $dispatcher->dispatch(__FILE__, __FUNCTION__, $parameters);
}

function acroniscloud_ClientArea()
{
    /** @var Dispatcher $dispatcher */
    $dispatcher = Locator::getInstance()->get(DispatcherFactory::NAME);

    return $dispatcher->dispatch(__FILE__, __FUNCTION__);
}

function acroniscloud_ClientAreaCustomButtonArray()
{
    /** @var Dispatcher $dispatcher */
    $dispatcher = Locator::getInstance()->get(DispatcherFactory::NAME);

    return $dispatcher->dispatch(__FILE__, __FUNCTION__);
}

function acroniscloud_ClientAreaManagement()
{
    /** @var Dispatcher $dispatcher */
    $dispatcher = Locator::getInstance()->get(DispatcherFactory::NAME);

    return $dispatcher->dispatch(__FILE__, __FUNCTION__);
}

function acroniscloud_ServiceSingleSignOn()
{
    /** @var Dispatcher $dispatcher */
    $dispatcher = Locator::getInstance()->get(DispatcherFactory::NAME);

    return $dispatcher->dispatch(__FILE__, __FUNCTION__);
}

function acroniscloud_TestConnection()
{
    /** @var Dispatcher $dispatcher */
    $dispatcher = Locator::getInstance()->get(DispatcherFactory::NAME);

    return $dispatcher->dispatch(__FILE__, __FUNCTION__);
}