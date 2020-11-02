<?php
/**
 * @Copyright © 2003-2020 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace Acronis\UsageReport\Service;

use AcronisCloud\Util\Arr;
use Acronis\UsageReport\UsageReportSettingsInterface;

class ReportSettings implements UsageReportSettingsInterface
{
    /**
     * @var array
     */
    private $settings;

    /**
     * @param array $settings
     */
    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return int
     */
    public function getRetriesLimit()
    {
        return Arr::get($this->settings, static::PROPERTY_RETRIES_LIMIT, false);
    }

    /**
     * @return int
     */
    public function getRetryTimeout()
    {
        return Arr::get($this->settings, static::PROPERTY_RETRY_TIMEOUT, false);
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    public function getReportsBasePath()
    {
        $downloadPath = Arr::get($this->settings, static::PROPERTY_DOWNLOAD_PATH, false);

        return $this->getAbsolutePath($downloadPath);
    }

    /**
     * @return bool
     */
    public function getReportsTtlInDays()
    {
        return Arr::get($this->settings, static::PROPERTY_REPORTS_TTL_DAYS, false);
    }

    /**
     * @param $filePath
     * @return string
     * @throws \Exception
     */
    public function getAbsolutePath($filePath)
    {
        $dirPath = dirname($filePath);
        $fileName = basename($filePath);
        $dirRealPath = realpath($dirPath);

        if ($dirRealPath === false) {
            throw new \Exception(\sprintf(
                'There is no access to folder "%s" with specified file "%s".',
                $dirRealPath, $fileName
            ));
        }

        return $dirRealPath . \DIRECTORY_SEPARATOR . $fileName;
    }

}