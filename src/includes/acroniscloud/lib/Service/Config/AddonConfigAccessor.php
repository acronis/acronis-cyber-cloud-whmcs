<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Config;

use AcronisCloud\Repository\WHMCS\AddonModuleRepository;
use AcronisCloud\Service\Config\Settings\LogScopeSettings;
use AcronisCloud\Util\Arr;
use AcronisCloud\Util\MemoizeTrait;

class AddonConfigAccessor
{
    const SETTING_LOGGING_DB_QUERY = 'logging_db_query';
    const SETTING_LOGGING_WHMCS_API = 'logging_whmcs_api';
    const SETTING_LOGGING_CLOUD_API = 'logging_cloud_api';

    const ON = 'on';

    /** @var array */
    private $settings;

    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return bool
     */
    public function isLoggingDbQuery()
    {
        return Arr::get($this->settings, self::SETTING_LOGGING_DB_QUERY, '') === self::ON;
    }

    /**
     * @return bool
     */
    public function isLoggingWhmcsApi()
    {
        return Arr::get($this->settings, self::SETTING_LOGGING_WHMCS_API, '') === self::ON;
    }

    /**
     * @return bool
     */
    public function isLoggingCloudApi()
    {
        return Arr::get($this->settings, self::SETTING_LOGGING_CLOUD_API, '') === self::ON;
    }
}