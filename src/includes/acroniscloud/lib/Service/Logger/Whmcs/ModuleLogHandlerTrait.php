<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Logger\Whmcs;

use AcronisCloud\Repository\AbstractRepository;
use AcronisCloud\Service\Dispatcher\DispatcherFactory;
use AcronisCloud\Service\Dispatcher\Request;
use AcronisCloud\Service\Locator;
use AcronisCloud\Service\Logger\DatabaseLogging;
use AcronisCloud\Util\Arr;
use WHMCS\Database\Capsule;

trait ModuleLogHandlerTrait
{
    /**
     * Writes the record down to the log of the implementing handler
     *
     * @param array $record
     * @return void
     */
    protected function internalWrite(array $record)
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
            // required to prevent insert index pollution (for more info, read comment above setLastInsertId() implementation)
            AbstractRepository::setLastInsertId(Capsule::connection()->getPdo()->lastInsertId());
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