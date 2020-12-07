<?php
/**
 * @Copyright © 2003-2020 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Model;

use Acronis\UsageReport\Model\ReportEntryInterface;
use AcronisCloud\Model\WHMCS\Server;

class Report extends AbstractModel implements ReportEntryInterface
{
    const TABLE = 'acroniscloud_service_reports';

    const COLUMN_DATE = 'date';
    const COLUMN_DATACENTER_ID = 'datacenter_id';
    const COLUMN_STATUS = 'status';
    const COLUMN_REPORT_ID = 'report_id';
    const COLUMN_STORED_REPORT_ID = 'stored_report_id';
    const COLUMN_FILE_PATH = 'file_path';
    const COLUMN_INSTANCE_ID = 'instance_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        self::COLUMN_DATE,
        self::COLUMN_DATACENTER_ID,
        self::COLUMN_STATUS,
        self::COLUMN_REPORT_ID,
        self::COLUMN_STORED_REPORT_ID,
        self::COLUMN_FILE_PATH,
        self::COLUMN_INSTANCE_ID,
    ];

    /**
     * Hide unneeded for UI columns
     *
     * @var array
     */
    protected $hidden = [
        self::CREATED_AT,
        self::UPDATED_AT,
    ];

    /**
     * @var array
     */
    protected $casts = [
        self::COLUMN_DATACENTER_ID => self::TYPE_INTEGER,
        self::COLUMN_INSTANCE_ID => self::TYPE_INTEGER,
    ];

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->getAttributeValue(static::COLUMN_DATE);
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->getAttributeValue(static::COLUMN_STATUS);
    }

    /**
     * @return string
     */
    public function getStatusDescription()
    {
        return \array_key_exists($this->getStatus(), static::ALLOWED_STATUSES)
            ? static::ALLOWED_STATUSES[$this->getStatus()]
            : 'Unknown';
    }

    /**
     * @return mixed
     */
    public function getDatacenterId()
    {
        return $this->getAttributeValue(static::COLUMN_DATACENTER_ID);
    }

    /**
     * @return string
     *
     * @return Server
     */
    public function getDatacenter()
    {
        return $this->hasOne(Server::class, Server::COLUMN_ID, static::COLUMN_DATACENTER_ID)->getResults();
    }

    /**
     * @return string
     */
    public function getReportId()
    {
        return $this->getAttributeValue(static::COLUMN_REPORT_ID);
    }

    /**
     * @return string
     */
    public function getStoredReportId()
    {
        return $this->getAttributeValue(static::COLUMN_STORED_REPORT_ID);
    }

    /**
     * @return string
     */
    public function getInstanceId()
    {
        return $this->getAttributeValue(static::COLUMN_INSTANCE_ID);
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->getAttributeValue(static::COLUMN_FILE_PATH);
    }

    /**
     * @return bool
     */
    public function isDownloaded()
    {
        return $this->getStatus() >= self::REPORT_STATUS_DOWNLOADED;
    }

    /**
     * @return bool
     */
    public function isError()
    {
        return $this->getStatus() == self::REPORT_STATUS_ERROR;
    }

    /**
     * @return bool
     */
    public function isOrdered()
    {
        return $this->getStatus() >= self::REPORT_STATUS_DOWNLOADED;
    }

    /**
     * @return bool
     */
    public function isReady()
    {
        return $this->getStatus() >= self::REPORT_STATUS_READY;
    }

    /**
     * @return bool
     */
    public function isAggregated()
    {
        return $this->getStatus() == self::REPORT_STATUS_AGGREGATED;
    }

    public function erase()
    {
        $this->setAttribute(static::COLUMN_FILE_PATH, null);
        $this->setAttribute(static::COLUMN_STATUS, self::REPORT_STATUS_ERASED);
        $this->save();
    }

    public function markForDeletion()
    {
        $this->setAttribute(static::COLUMN_STATUS, self::REPORT_STATUS_MARK_FOR_DELETION);
        $this->save();
    }

    public function ordered($reportId)
    {
        $this->setAttribute(static::COLUMN_REPORT_ID, $reportId);
        $this->setAttribute(static::COLUMN_STATUS, self::REPORT_STATUS_ORDERED);
        $this->save();
    }

    public function ready($storedReportId)
    {
        $this->setAttribute(static::COLUMN_STORED_REPORT_ID, $storedReportId);
        $this->setAttribute(static::COLUMN_STATUS, self::REPORT_STATUS_READY);
        $this->save();
    }

    public function error()
    {
        $this->setAttribute(static::COLUMN_STATUS, self::REPORT_STATUS_ERROR);
        $this->save();
    }

    public function downloaded($filePath)
    {
        $this->setAttribute(static::COLUMN_FILE_PATH, $filePath);
        $this->setAttribute(static::COLUMN_STATUS, self::REPORT_STATUS_DOWNLOADED);
        $this->save();
    }

    public function aggregated()
    {
        $this->setAttribute(static::COLUMN_STATUS, self::REPORT_STATUS_AGGREGATED);
        $this->save();
    }
}
