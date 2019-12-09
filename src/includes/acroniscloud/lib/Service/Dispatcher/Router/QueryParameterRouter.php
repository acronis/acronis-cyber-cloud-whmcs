<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Dispatcher\Router;

use AcronisCloud\Service\Dispatcher\RequestInterface;
use AcronisCloud\Util\Arr;
use AcronisCloud\Util\Str;
use RuntimeException;

class QueryParameterRouter extends Router
{
    const PARAMETER_NAME_ACTION = 'action';
    const PARAMETER_VALUE_INDEX = 'index';

    /** @var string */
    private $parameterName;
    /** @var string|null */
    private $defaultValue;
    /** @var string|null */
    private $unknownActionHandlerName;

    public function __construct(
        array $rules,
        $unknownActionHandlerName = null,
        $parameterName = self::PARAMETER_NAME_ACTION,
        $defaultParameterValue = self::PARAMETER_VALUE_INDEX
    ) {
        parent::__construct($rules);

        $this->unknownActionHandlerName = $unknownActionHandlerName;
        $this->parameterName = $parameterName;
        $this->defaultValue = $defaultParameterValue;
    }

    protected function resolveActionData(RequestInterface $request, array $rules)
    {
        $actionName = $request->getQueryParameter($this->parameterName, $this->defaultValue);
        if (!$actionName) {
            throw new RuntimeException(Str::format(
                'Parameter %s must be presented.',
                $this->parameterName
            ));
        }

        $actionData = Arr::get($rules, $actionName);

        if (!$actionData) {
            $this->getLogger()->warning(
                'There is no rule for query parameter {0} with value {1}.',
                [$this->parameterName, $actionName]
            );
            if ($this->unknownActionHandlerName) {
                $this->getLogger()->debug(
                    'Get a rule for {0}.',
                    [$this->unknownActionHandlerName]
                );
                $actionData = Arr::get($rules, $this->unknownActionHandlerName);
            }
        }

        if (!$actionData) {
            $moduleName = $request->getModuleName();
            $functionName = $request->getModuleActionName();

            throw  new RuntimeException(Str::format(
                'Action name "%s" is not listed in the list of controllers for function "%s.%s".',
                $actionName, $moduleName, $functionName
            ));
        }

        return $actionData;
    }
}