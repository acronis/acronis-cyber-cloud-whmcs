<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. All rights reserved
 */

namespace Acronis\UsageReport;

use Acronis\UsageReport\Csv\ReportRowWrapper;

interface FormatParserInterface
{
    /**
     * @param string[] $columnNames
     */
    public function setColumnNames($columnNames);

    /**
     * @param $row
     * @return ReportRowWrapper
     * @throws \Exception
     */
    public function parseRow($row);
}