<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
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