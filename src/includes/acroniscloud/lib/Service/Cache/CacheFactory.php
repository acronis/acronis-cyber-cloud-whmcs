<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Cache;

use AcronisCloud\Service\Config\ConfigAwareTrait;
use AcronisCloud\Service\Config\Settings\CacheSettings;
use AcronisCloud\Service\Config\Settings\CacheStorageSettings;
use AcronisCloud\Service\FactoryInterface;
use AcronisCloud\Util\Str;
use Exception;

class CacheFactory implements FactoryInterface
{
    const NAME = 'cache';

    use ConfigAwareTrait;

    /**
     * @return CacheInterface
     * @throws Exception
     */
    public function createInstance()
    {
        $settings = $this->getConfig()->getCacheSettings();

        if (!$settings->getEnabled()) {
            return new NoCache();
        }

        switch ($settings->getStorageType()) {
            case null:
                if (extension_loaded('apc') && ini_get('apc.enabled')) {
                    return new ApcuCacheStorage($settings->getNamespace(), $settings->getDefaultTtl());
                } else {
                    return new NoCache();
                }
            case CacheSettings::STORAGE_TYPE_APCU:
                return new ApcuCacheStorage($settings->getNamespace(), $settings->getDefaultTtl());
            default:
                throw new Exception(Str::format(
                    'Cache storage type "%s" is not supported.',
                    $settings->getStorageType()
                ));
        }
    }
}