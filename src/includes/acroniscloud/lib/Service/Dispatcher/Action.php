<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Dispatcher;

use AcronisCloud\Service\Dispatcher\Response\HttpResponse;
use AcronisCloud\Util\Str;

class Action implements ActionInterface
{
    /** @var string */
    private $controllerName;
    /** @var string */
    private $actionName;
    /** @var string */
    private $allowedRequestMethod;

    public function __construct($controllerName, $actionName, $allowedRequestMethod = null)
    {
        $this->controllerName = $controllerName;
        $this->actionName = $actionName;
        $this->allowedRequestMethod = $allowedRequestMethod;
    }

    /**
     * @return string
     */
    public function getControllerName()
    {
        return $this->controllerName;
    }

    /**
     * @return string
     */
    public function getActionName()
    {
        return $this->actionName;
    }

    /**
     * @return string | null
     */
    public function getAllowedRequestMethod()
    {
        return $this->allowedRequestMethod;
    }

    /**
     * @param RequestInterface $request
     * @throws RequestException
     */
    public function checkRequestIsSupported($request)
    {
        if (!is_null($this->allowedRequestMethod) &&
            $this->allowedRequestMethod !== $request->getRequestMethod()
        ) {
            throw new RequestException(
                Str::format('Only %s requests allowed.', $this->allowedRequestMethod),
                [HttpResponse::HEADER_ALLOW => [$this->allowedRequestMethod]],
                HttpResponse::HTTP_METHOD_NOT_ALLOWED
            );
        }
    }
}