<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Dispatcher;

use AcronisCloud\Service\Logger\DatabaseLogging;
use AcronisCloud\Service\Logger\LoggerAwareTrait;
use AcronisCloud\Util\Func;
use AcronisCloud\Util\Str;
use Exception;

class Dispatcher
{
    use LoggerAwareTrait;

    /** @var RouterInterface */
    private $router;
    /** @var RequestInterface */
    private $request;

    /**
     * Dispatcher constructor.
     *
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param string $file
     * @param string $function
     * @param mixed $parameters
     * @return mixed
     * @throws Exception
     */
    public function dispatch($file, $function, $parameters = null)
    {
        return DatabaseLogging::runWithLogs(function () use ($file, $function, $parameters) {
            $this->getLogger()->debug(
                'Start dispatching {0}:{1}. Parameters: {2}',
                [$file, $function, $parameters]
            );

            $this->initRequest($file, $function, $parameters);
            $action = $this->resolveAction();
            $response = $this->executeAction($action);

            $result = $this->renderResponse($response, $action);

            $this->getLogger()->debug(
                'End dispatching {0}:{1}. Result: {2}',
                [$file, $function, $result]
            );

            if ($response->isPartial()) {
                $this->getLogger()->debug(
                    'Render a partial response for the request.'
                );

                echo $result;
                die();
            }

            return $result;
        });
    }

    /**
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param string $file
     * @param string $function
     * @param mixed $parameters
     */
    private function initRequest($file, $function, $parameters)
    {
        $this->request = new Request();

        $this->request->setFile($file);
        $this->request->setFunction($function);
        $this->request->setParameters($parameters);
    }

    /**
     * @return ActionInterface
     * @throws Exception
     */
    private function resolveAction()
    {
        try {
            return $this->router->getAction($this->getRequest());
        } catch (Exception $e) {
            $this->getLogger()->error(
                'Unable to resolve an action. Error: {0}',
                [$e->getMessage()]
            );
            $this->getLogger()->debug($e->getTraceAsString());

            throw $e;
        }
    }

    /**
     * @param ActionInterface $action
     * @return ResponseInterface
     * @throws Exception
     */
    private function executeAction(ActionInterface $action)
    {
        try {
            $controller = $this->createControllerInstance($action->getControllerName());
            $actionName = $action->getActionName();

            $request = $this->getRequest();
            try {
                $action->checkRequestIsSupported($request);
                $data = Func::call($controller, $actionName, [$request]);
            } catch (Exception $e) {
                $this->getLogger()->warning(
                    'Handle exception {0} for {1}::{2}. Error: {3}',
                    [get_class($e), $action->getControllerName(), $action->getActionName(), $e->getMessage()]
                );
                $this->getLogger()->debug($e->getTraceAsString());

                $data = $controller->handleException($e, $action, $request);
            }

            if ($this->isResponse($data)) {
                return $data;
            }

            $response = $controller->getResponseStrategy();
            $response->setData($data);
        } catch (Exception $e) {
            $this->getLogger()->error(
                'Unable execute action {0}::{1}. Error: {2}',
                [$action->getControllerName(), $action->getActionName(), $e->getMessage()]
            );
            $this->getLogger()->debug($e->getTraceAsString());

            throw $e;
        }

        return $response;
    }

    /**
     * @param ResponseInterface $response
     * @param ActionInterface $action
     * @return mixed
     * @throws Exception
     */
    private function renderResponse(ResponseInterface $response, ActionInterface $action)
    {
        try {
            $result = $response->render();
        } catch (Exception $e) {
            $this->getLogger()->error(
                'Unable to render a response for action {0}::{1}. Error: {2}',
                [$action->getControllerName(), $action->getActionName(), $e->getMessage()]
            );
            $this->getLogger()->debug($e->getTraceAsString());

            throw $e;
        }

        return $result;
    }

    /**
     * @param string $class
     * @return ControllerInterface
     */
    private function createControllerInstance($class)
    {
        if (class_exists($class)) {
            return new $class();
        } else {
            throw new \RuntimeException(Str::format(
                'Cannot create controller instance, class "%s" not found.',
                $class
            ));
        }
    }

    private function isResponse($value)
    {
        return $value instanceof ResponseInterface;
    }
}