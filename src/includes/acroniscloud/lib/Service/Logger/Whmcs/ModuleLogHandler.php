<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Logger\Whmcs;

use AcronisCloud\Service\Dispatcher\DispatcherFactory;
use AcronisCloud\Service\Dispatcher\Request;
use AcronisCloud\Service\Locator;
use AcronisCloud\Service\Logger\DatabaseLogging;
use AcronisCloud\Util\Arr;
use Monolog\Handler\AbstractProcessingHandler;

class ModuleLogHandler extends AbstractProcessingHandler
{
    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param array $record
     * @return void
     */
    protected function write(array $record)
    {
        if (!$record['formatted'] || !($record['formatted'] instanceof ModuleLogEntry)) {
            return;
        }

        /** @var ModuleLogEntry $entry */
        $entry = $record['formatted'];

        $request = $this->getRequest();
        $action = implode(' | ', [
            'Request ID: ' . Arr::getByPath($record, 'extra.uid'),
            'Module: ' . ($request ? $request->getModuleName() : ''),
            'Function: ' . ($request ? $request->getModuleActionName() : ''),
            'Type of call: ' . $entry->getAction(),
        ]);

        DatabaseLogging::runWithoutLogs(function () use ($action, $entry) {
            logModuleCall(
                ACRONIS_CLOUD_SERVICE_NAME,
                $action,
                $entry->getRequest(),
                $entry->getResponse(),
                $entry->getProcessedData(),
                $entry->getReplaceVars()
            );
        });
    }

    /**
     * @return Request|null
     */
    private function getRequest()
    {
        static $dispatcher = null;
        if (is_null($dispatcher)) {
            $dispatcher = Locator::getInstance()->get(DispatcherFactory::NAME);
        }

        return $dispatcher->getRequest();
    }
}