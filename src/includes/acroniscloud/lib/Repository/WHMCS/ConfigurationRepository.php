<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Repository\WHMCS;

use AcronisCloud\Model\WHMCS\Configuration;
use AcronisCloud\Repository\AbstractRepository;
use Illuminate\Database\Eloquent\Model;

class ConfigurationRepository extends AbstractRepository
{
    const VALUE_ON = 'on';

    const SETTING_LANGUAGE = 'Language';
    const SETTING_VERSION = 'Version';
    const SETTING_MODULE_DEBUG_MODE = 'ModuleDebugMode';

    /**
     * @param string $setting
     * @return Model
     */
    public function findBySetting($setting)
    {
        return Configuration::where(Configuration::COLUMN_SETTING, $setting)->first();
    }

    /**
     * @param string $setting
     * @return mixed
     */
    public function getSettingValue($setting)
    {
        $setting = $this->findBySetting($setting);

        return $setting
            ? $setting->value
            : null;
    }

    /**
     * @return string|null
     */
    public function getLanguage()
    {
        return $this->getSettingValue(static::SETTING_LANGUAGE);
    }

    /**
     * @return string|null
     */
    public function getVersion()
    {
        return $this->getSettingValue(static::SETTING_VERSION);
    }

    /**
     * @return string|null
     */
    public function isModuleDebugModeEnabled()
    {
        return $this->getSettingValue(static::SETTING_MODULE_DEBUG_MODE) === static::VALUE_ON;
    }
}