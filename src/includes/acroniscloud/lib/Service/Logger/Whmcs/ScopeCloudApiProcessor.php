<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Logger\Whmcs;

class ScopeCloudApiProcessor
{
    public function __invoke(array $record)
    {
        if (isset($record['context']['cloud-api-client'], $record['context']['request'])) {
            $record['context']['module_log'] = ScopeCloudApiFormatter::NAME;
        }

        return $record;
    }
}