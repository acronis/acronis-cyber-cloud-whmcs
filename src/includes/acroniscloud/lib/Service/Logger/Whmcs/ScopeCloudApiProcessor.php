<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
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