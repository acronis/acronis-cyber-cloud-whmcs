<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Dispatcher;

use Exception;

interface ControllerInterface
{
    /**
     * @param Exception $e
     * @param ActionInterface $action
     * @param RequestInterface $request
     * @return mixed|ResponseInterface
     */
    public function handleException(
        Exception $e,
        ActionInterface $action,
        RequestInterface $request
    );

    /**
     * @return ResponseInterface
     */
    public function getResponseStrategy();
}