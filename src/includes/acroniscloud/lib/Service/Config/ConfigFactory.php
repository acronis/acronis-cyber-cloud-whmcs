<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
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