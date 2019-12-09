<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Dispatcher\Router;

use AcronisCloud\Service\Dispatcher\Action;
use AcronisCloud\Service\Dispatcher\ActionInterface;
use AcronisCloud\Service\Dispatcher\RequestInterface;
use AcronisCloud\Service\Dispatcher\RouterInterface;
use AcronisCloud\Service\Logger\LoggerAwareTrait;
use AcronisCloud\Util\MemoizeTrait;

abstract class Router implements RouterInterface
{
    use MemoizeTrait,
        LoggerAwareTrait;

    /** @var array */
    private $rules;

    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    public function getAction(RequestInterface $request)
    {
        $action = $this->resolveActionData($request, $this->getRules());

        if ($this->isRouter($action)) {
            return $action->getAction($request);
        }

        if (!$this->isAction($action)) {
             $action = $this->createAction($action);
        }

        return $action;
    }

    abstract protected function resolveActionData(RequestInterface $request, array $rules);

    protected function getRules()
    {
        return $this->rules;
    }

    private function isRouter($value)
    {
        return $value instanceof RouterInterface;
    }

    private function isAction($value)
    {
        return $value instanceof ActionInterface;
    }

    private function createAction(array $data)
    {
        return new Action(...$data);
    }
}