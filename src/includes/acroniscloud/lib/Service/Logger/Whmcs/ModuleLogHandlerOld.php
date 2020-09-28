<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Logger\Whmcs;

use Monolog\Handler\AbstractProcessingHandler;

class ModuleLogHandlerOld extends AbstractProcessingHandler
{
    use ModuleLogHandlerTrait;

    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param array $record
     * @return void
     */
    protected function write(array $record)
    {
        $this->internalWrite($record);
    }
}