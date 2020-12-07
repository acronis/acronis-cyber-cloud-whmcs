<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Repository;

use Acronis\UsageReport\Model\ReportEntryInterface;
use Acronis\UsageReport\Model\ReportEntryRepositoryInterface;
use AcronisCloud\Model\Report;
use WHMCS\Database\Capsule;

class ReportRepository extends AbstractRepository implements ReportEntryRepositoryInterface
{
    /**
    * {@inheritdoc}
    */
    public function getAllReports()
    {
        return Report::all();
    }

    /**
    * {@inheritdoc}
    */
    public function getReportsTillDate($date, $fetchErased = false)
    {
        $statement = Report::where(Report::COLUMN_DATE, '<=', $date)
            ->orWhere(Report::COLUMN_STATUS, '=', Report::REPORT_STATUS_MARK_FOR_DELETION);

        if (!$fetchErased) {
            $statement = $statement->where(Report::COLUMN_STATUS, '!=', Report::REPORT_STATUS_ERASED);
        }

        return $statement->get();
    }

    /**
    * {@inheritdoc}
    */
    public function getReportByDatacenterIdAndDate($datacenterId, $date, $fetchErased = false)
    {
        $statement = Report::where(Report::COLUMN_DATACENTER_ID, '=', $datacenterId)
            ->where('date', '=', $date);

        if (!$fetchErased) {
            $statement = $statement->where(Report::COLUMN_STATUS, '!=', Report::REPORT_STATUS_ERASED);
        }

        return $statement->first();
    }

    /**
     * {@inheritdoc}
     */
    public function createFromDatacenterId($datacenterId, $date)
    {
        return Report::create([
            Report::COLUMN_STATUS => 3,
            Report::COLUMN_DATACENTER_ID => $datacenterId,
            Report::COLUMN_DATE => $date,
        ]);
    }

    public function truncate()
    {
        Report::truncate();
    }
}