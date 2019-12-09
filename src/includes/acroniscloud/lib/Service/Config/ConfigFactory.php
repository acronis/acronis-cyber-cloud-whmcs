<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Config;

use AcronisCloud\Service\FactoryInterface;
use Symfony\Component\Yaml\Yaml;

class ConfigFactory implements FactoryInterface
{
    const NAME = 'config';

    /**
     * @return ConfigAccessor
     */
    public function createInstance()
    {
        return new ConfigAccessor($this->getConfigData());
    }

    protected function getConfigData()
    {
        $configPath = ACRONIS_CLOUD_INCLUDES_DIR . '/config.yaml';

        return (is_file($configPath)) ? Yaml::parse(file_get_contents($configPath)) : [];
    }
}