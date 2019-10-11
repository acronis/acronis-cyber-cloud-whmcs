<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
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