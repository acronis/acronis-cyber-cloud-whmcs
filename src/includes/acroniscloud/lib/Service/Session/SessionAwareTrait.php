<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
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