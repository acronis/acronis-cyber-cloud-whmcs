<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Config\Settings;

use AcronisCloud\Util\Arr;
use AcronisCloud\Util\Str;
use Exception;

class CacheSettings extends AbstractSettings
{
    const PROPERTY_ENABLED = 'enabled';
    const PROPERTY_DEFAULT_TTL = 'default_ttl';
    const PROPERTY_NAMESPACE = 'namespace';
    const PROPERTY_STORAGE_TYPE = 'storage_type';

    /**
     * Default values for this section
     */
    const DEFAULT_TTL = 7200;
    const DEFAULT_NAMESPACE = ACRONIS_CLOUD_SERVICE_NAME;

    const STORAGE_TYPE_APCU = 'apcu';

    /**
     * @return bool
     */
    public function getEnabled()
    {
        return (bool) Arr::get($this->settings, static::PROPERTY_ENABLED, false);
    }

    /**
     * @return int
     */
    public function getDefaultTtl()
    {
        return (int) Arr::get($this->settings, static::PROPERTY_DEFAULT_TTL, static::DEFAULT_TTL);
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return Arr::get($this->settings, static::PROPERTY_NAMESPACE, static::DEFAULT_NAMESPACE);
    }

    /**
     * @return string
     */
    public function getStorageType()
    {
        return Arr::get($this->settings, static::PROPERTY_STORAGE_TYPE, null);
    }
}