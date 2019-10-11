<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
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