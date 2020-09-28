<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Logger\Whmcs;

use Monolog\Handler\AbstractProcessingHandler;

class ModuleLogHandler extends AbstractProcessingHandler
{
    use ModuleLogHandlerTrait;

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param array $record
     * @return void
     */
    protected function write(array $record): void
    {
        $this->internalWrite($record);
    }
}