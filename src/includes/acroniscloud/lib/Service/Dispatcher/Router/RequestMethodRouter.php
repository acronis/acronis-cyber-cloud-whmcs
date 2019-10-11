<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Dispatcher\Router;

use AcronisCloud\Service\Dispatcher\RequestInterface;
use AcronisCloud\Util\Arr;
use AcronisCloud\Util\Str;
use RuntimeException;

class RequestMethodRouter extends Router
{
    /** @var string|null */
    private $unknownActionHandlerName;

    public function __construct(
        array $rules,
        $unknownActionHandlerName = null
    ) {
        parent::__construct($rules);

        $this->unknownActionHandlerName = $unknownActionHandlerName;
    }

    protected function resolveActionData(RequestInterface $request, array $rules)
    {
        $requestMethod = $request->getRequestMethod();

        $actionData = Arr::get($rules, $requestMethod);
        if (!$actionData) {
            $this->getLogger()->warning(
                'There is no rule for the request method {0}.',
                [$requestMethod]
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
            $moduleActionName = $request->getModuleActionName();

            throw new RuntimeException(Str::format(
                'Request method "%s" is not listed in the list of controllers for function "%s.%s".',
                $requestMethod, $moduleName, $moduleActionName
            ));
        }

        return $actionData;
    }
}