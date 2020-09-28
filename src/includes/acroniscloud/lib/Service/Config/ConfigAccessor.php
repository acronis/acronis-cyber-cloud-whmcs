<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Config;

use AcronisCloud\Service\Config\Settings\CacheSettings;
use AcronisCloud\Service\Config\Settings\CloudApiSettings;
use AcronisCloud\Service\Config\Settings\LoggerSettings;
use AcronisCloud\Service\Config\Settings\ProductSettings;
use Acronis\UsageReport\Service\ReportSettings;
use AcronisCloud\Util\Arr;
use AcronisCloud\Util\MemoizeTrait;
use AcronisCloud\Util\Str;
use RuntimeException;

class ConfigAccessor
{
    const SECTION_LOGGER = 'logger';
    const SECTION_CACHE = 'cache';
    const SECTION_CLOUD_API = 'cloud_api';
    const SECTION_PRODUCT = 'product';
    const SECTION_USAGE_REPORT = 'usage_report';

    const CONFIG_SETTINGS = [
        self::SECTION_LOGGER => LoggerSettings::class,
        self::SECTION_CACHE => CacheSettings::class,
        self::SECTION_CLOUD_API => CloudApiSettings::class,
        self::SECTION_PRODUCT => ProductSettings::class,
        self::SECTION_USAGE_REPORT => ReportSettings::class,
    ];

    private $data;

    use MemoizeTrait;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return LoggerSettings
     */
    public function getLoggerSettings()
    {
        return $this->getSettings(static::SECTION_LOGGER);
    }

    /**
     * @return CacheSettings
     */
    public function getCacheSettings()
    {
        return $this->getSettings(static::SECTION_CACHE);
    }

    /**
     * @return CloudApiSettings
     */
    public function getCloudApiSettings()
    {
        return $this->getSettings(static::SECTION_CLOUD_API);
    }

    /**
     * @return ProductSettings
     */
    public function getProductSettings()
    {
        return $this->getSettings(static::SECTION_PRODUCT);
    }

    /**
     * @return ReportSettings
     */
    public function getUsageReportSettings()
    {
        return $this->getSettings(static::SECTION_USAGE_REPORT)
            ->withBasePath(ACRONIS_CLOUD_INCLUDES_DIR);
    }

    private function getSettings($sectionName)
    {
        return $this->memoize(function () use ($sectionName) {
            $settingsClass = Arr::get(static::CONFIG_SETTINGS, $sectionName);
            if (!$settingsClass) {
                throw new RuntimeException(Str::format(
                    'Class is not defined for config section "%s".',
                    $sectionName
                ));
            }
            $section = $this->getConfigSection($sectionName);

            return new $settingsClass($section);
        }, $sectionName);
    }

    private function getConfigSection($sectionName)
    {
        $section = Arr::get($this->data, $sectionName);
        if (!is_array($section)) {
            return [];
        }

        return $section;
    }
}