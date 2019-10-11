<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Config;

use AcronisCloud\Service\Database\Repository\RepositoryAwareTrait;
use AcronisCloud\Service\FactoryInterface;

class AddonConfigFactory implements FactoryInterface
{
    use RepositoryAwareTrait;

    const NAME = 'addon_config';

    /**
     * @return AddonConfigAccessor
     */
    public function createInstance()
    {
        return new AddonConfigAccessor($this->getSettings());
    }

    protected function getSettings()
    {
        return $this->getRepository()
            ->getAddonModuleRepository()
            ->getSettings();
    }
}