<?php
/**
 * @Copyright © 2003-2020 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace Acronis\UsageReport;

interface UsageReportSettingsInterface
{
    const PROPERTY_RETRIES_LIMIT = 'retries_limit';
    const PROPERTY_RETRY_TIMEOUT = 'retry_timeout';
    const PROPERTY_DOWNLOAD_PATH = 'download_path';
    const PROPERTY_REPORTS_TTL_DAYS = 'downloaded_files_ttl_in_days';

    /**
     * @return int
     */
    public function getRetriesLimit();

    /**
     * @return int
     */
    public function getRetryTimeout();

    /**
     * @return string
     */
    public function getReportsBasePath();

    /**
     * @return int
     */
    public function getReportsTtlInDays();

}