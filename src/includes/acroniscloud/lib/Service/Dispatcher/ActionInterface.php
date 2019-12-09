<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Dispatcher;

interface ActionInterface
{
    /**
     * @return string | null
     */
    public function getAllowedRequestMethod();

    /**
     * @return string
     */
    public function getControllerName();

    /**
     * @return string
     */
    public function getActionName();

    /**
     * @param string $requestMethod
     * @throws RequestException
     */
    public function checkRequestIsSupported($requestMethod);
}