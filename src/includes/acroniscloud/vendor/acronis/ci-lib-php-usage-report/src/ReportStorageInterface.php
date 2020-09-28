<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. All rights reserved
 */

namespace Acronis\UsageReport;

interface ReportStorageInterface
{
    const TOTAL_STORAGE_REPORT = 'total_storage';

    /**
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value);

    /**
     * @param string $key
     * @return mixed
     */
    public function get($key);

    /**
     * @param string $keyPrefix
     * @return mixed
     */
    public function deleteAll($keyPrefix = '');
}