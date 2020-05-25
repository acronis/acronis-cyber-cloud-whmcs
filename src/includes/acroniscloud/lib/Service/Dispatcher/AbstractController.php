<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Dispatcher;

use AcronisCloud\Service\Dispatcher\Response\DataResponse;
use Exception;

abstract class AbstractController implements ControllerInterface
{

    /**
     * @param Exception $e
     * @param ActionInterface $action
     * @param RequestInterface $request
     * @return mixed|ResponseInterface
     * @throws Exception
     */
    public function handleException(
        Exception $e,
        ActionInterface $action,
        RequestInterface $request
    ) {
        throw $e;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponseStrategy()
    {
        return new DataResponse();
    }

    /**
     * @param string $url
     * @param array|bool $queryParameters
     */
    protected function redirect($url, $queryParameters = false)
    {
        if (is_array($queryParameters)) {
            $params = http_build_query($queryParameters);
            $url = strpos($url, '?')
                ? $url . '&' . $params
                : $url . '?' . $params;
        }
        ob_clean();
        header('Location: ' . $url, true, 303);
        die();
    }
}