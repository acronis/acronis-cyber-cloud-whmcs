<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Dispatcher\Router;

use AcronisCloud\Service\Dispatcher\RequestInterface;
use AcronisCloud\Util\Arr;
use AcronisCloud\Util\Str;

class ModuleActionRouter extends Router
{
    protected function resolveActionData(RequestInterface $request, array $rules)
    {
        $moduleAction = $request->getModuleActionName();

        $actionData = Arr::get($rules, $moduleAction);
        if (!$actionData) {
            $moduleName = $request->getModuleName();

            throw  new \RuntimeException(Str::format(
                'Module action "%s" is not listed in the list of controllers for module type "%s".',
                $moduleAction, $moduleName
            ));
        }

        return $actionData;
    }
}