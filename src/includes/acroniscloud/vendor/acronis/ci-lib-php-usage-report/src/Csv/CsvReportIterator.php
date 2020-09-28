<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. All rights reserved
 */

namespace Acronis\UsageReport\Csv;

use Acronis\UsageReport\FormatParserInterface;
use Acronis\UsageReport\Csv\ReportRowWrapper;
use RuntimeException;

class CsvReportIterator extends CsvFileIterator
{
    private $firstRowOffset;

    private $formatParser;

    /**
     * @param string $filePath
     * @param FormatParserInterface $formatParser
     * @throws RuntimeException
     */
    public function __construct($filePath, $formatParser)
    {
        $this->formatParser = $formatParser;
        $fileHandler = gzopen($filePath, 'r');
        parent::__construct($fileHandler);

        $columnNames = $this->readNext();
        $this->formatParser->setColumnNames($columnNames);
        $this->firstRowOffset = ftell($this->fileHandler);
    }

    public function rewind()
    {
        parent::rewind();
        fseek($this->fileHandler, $this->firstRowOffset);
    }

    /**
     * @return ReportRowWrapper|null
     * @throws RuntimeException
     * @throws \Exception
     */
    public function current()
    {
        $row = parent::current();

        return $this->formatParser->parseRow($row);
    }
}