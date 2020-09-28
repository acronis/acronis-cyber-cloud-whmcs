<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. All rights reserved
 */

namespace Acronis\UsageReport\Model;

interface ReportEntryRepositoryInterface
{

    /**
     * @return ReportEntryInterface[]
     */
    public function getAllReports();

    /**
     * @param \DateTime $date
     * @param bool $fetchErased
     *
     * @return ReportEntryInterface[]
     */
    public function getReportsTillDate($date, $fetchErased = false);

    /**
     * @param int $datacenterId
     * @param string $date
     * @param bool $fetchErased
     *
     * @return ReportEntryInterface|null
     */
    public function getReportByDatacenterIdAndDate($datacenterId, $date, $fetchErased = false);

    /**
     * @param int $datacenterId
     * @param string $date
     * @return ReportEntryInterface
     */
    public function createFromDatacenterId($datacenterId, $date);

    public function truncate();
}