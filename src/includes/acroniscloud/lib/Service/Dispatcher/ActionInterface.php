<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
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