<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
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