<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Logger\Whmcs;

use AcronisCloud\Util\Arr;

class ScopeCloudApiFormatter
{
    const NAME = 'cloud_api';

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
            'Cloud API',
            Arr::get($context, 'request', []),
            Arr::get($context, 'response', [])
        );
    }
}