<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. All rights reserved
 */

namespace Acronis\UsageReport\Model;

interface ReportEntryInterface
{
    /**
     * Statuses are ordered by corresponding steps of usage processing
     */
    const REPORT_STATUS_ERROR = -1;
    const REPORT_STATUS_NEW = 1;
    const REPORT_STATUS_ORDERED = 2;
    const REPORT_STATUS_READY = 3;
    const REPORT_STATUS_DOWNLOADED = 4;
    const REPORT_STATUS_AGGREGATED = 5;
    const REPORT_STATUS_ERASED = 6;
    const REPORT_STATUS_MARK_FOR_DELETION = 7; // used when current process cannot delete the files

    const ALLOWED_STATUSES = [
        self::REPORT_STATUS_ERROR => 'Error',
        self::REPORT_STATUS_NEW => 'New',
        self::REPORT_STATUS_ORDERED => 'Ordered',
        self::REPORT_STATUS_READY => 'Ready for downloading',
        self::REPORT_STATUS_DOWNLOADED => 'Downloaded and saved',
        self::REPORT_STATUS_AGGREGATED => 'Aggregated',
        self::REPORT_STATUS_ERASED => 'Erased',
        self::REPORT_STATUS_MARK_FOR_DELETION => 'To be deleted'
    ];

    public function getId();

    public function getDate();

    public function getStatus();

    public function getStatusDescription();

    public function getReportId();

    public function getStoredReportId();

    public function getDatacenter();

    public function getDatacenterId();

    public function getFilePath();

    public function isError();

    public function isOrdered();

    public function isReady();

    public function isDownloaded();

    public function isAggregated();

    public function erase();

    public function ordered($reportId);

    public function ready($storedReportId);

    public function error();

    public function downloaded($filePath);

    public function markForDeletion();
}