<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Logger\Whmcs;

use AcronisCloud\Util\Arr;
use Monolog\Formatter\FormatterInterface;

class ModuleLogFormatter implements FormatterInterface
{
    private $scopeFormatters = [];

    public function __construct(array $scopeFormatters)
    {
        $this->scopeFormatters = $scopeFormatters;
    }

    /**
     * Formats a log record.
     *
     * @param array $record A record to format
     * @return mixed The formatted record
     */
    public function format(array $record)
    {
        if (!isset($record['context']['module_log'])) {
            return null;
        }
        $scope = $record['context']['module_log'];
        $scopeFormatter = Arr::get($this->scopeFormatters, $scope);

        return $scopeFormatter ? $scopeFormatter->format($record) : null;
    }

    /**
     * Formats a set of log records.
     *
     * @param array $records A set of records to format
     * @return mixed The formatted set of records
     */
    public function formatBatch(array $records)
    {
        $formatted = [];

        foreach ($records as $record) {
            $formatted[] = $this->format($record);
        }

        return $formatted;
    }
}