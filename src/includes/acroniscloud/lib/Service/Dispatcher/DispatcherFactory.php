<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Dispatcher;

use AcronisCloud\Service\FactoryInterface;

class DispatcherFactory implements FactoryInterface
{
    const NAME = 'dispatcher';

    public function createInstance()
    {
        $controllers = require ACRONIS_CLOUD_INCLUDES_DIR . '/controllers.php';

        return new Dispatcher($controllers);
    }
}