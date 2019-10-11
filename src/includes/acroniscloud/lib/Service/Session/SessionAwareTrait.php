<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Session;

use AcronisCloud\Service\Locator;

trait SessionAwareTrait
{
    /**
     * @return SessionAccessor
     */
    protected function getSession()
    {
        return Locator::getInstance()->get(SessionFactory::NAME);
    }
}