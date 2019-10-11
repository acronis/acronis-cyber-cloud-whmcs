<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
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