<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Database\Repository;

use AcronisCloud\Service\Locator;

trait RepositoryAwareTrait
{
    /**
     * @return RepositoryFactory
     */
    protected function getRepository()
    {
        return Locator::getInstance()->get(RepositoryFactory::NAME);
    }
}