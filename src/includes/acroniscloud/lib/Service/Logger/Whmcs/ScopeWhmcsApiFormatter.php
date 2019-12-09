<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Logger\Whmcs;

use AcronisCloud\Util\Arr;

class ScopeWhmcsApiFormatter
{
    const NAME = 'whmcs_api';

    /**
     * Formats a log record.
     *
     * @param array $record A record to format
     * @return mixed The formatted record
     */
    public function format(array $record)
    {
        $context = Arr::get($record, 'context', []);

        return new ModuleLogEntry(
            'WHMCS API',
            [
                'method' => Arr::get($context, 'method', ''),
                'parameters' => Arr::get($context, 'parameters'),
            ],
            Arr::get($context, 'result', '')
        );
    }
}