<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Dispatcher\Router;

use AcronisCloud\Service\Dispatcher\RequestInterface;
use AcronisCloud\Util\Arr;
use AcronisCloud\Util\Str;
use RuntimeException;

class ModuleRouter extends Router
{
    protected function resolveActionData(RequestInterface $request, array $rules)
    {
        $moduleName = $request->getModuleName();

        $actionData = Arr::get($rules, $moduleName);
        if (!$actionData) {
            throw  new RuntimeException(Str::format(
                'Module type "%s" is not listed in the list of controllers.',
                $moduleName
            ));
        }

        return $actionData;
    }
}