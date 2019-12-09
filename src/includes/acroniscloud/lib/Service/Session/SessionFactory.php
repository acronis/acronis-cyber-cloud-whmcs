<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Session;

use AcronisCloud\Service\FactoryInterface;

class SessionFactory implements FactoryInterface
{
    const NAME = 'session';

    /**
     * @return SessionAccessor
     */
    public function createInstance()
    {
        return new SessionAccessor();
    }
}