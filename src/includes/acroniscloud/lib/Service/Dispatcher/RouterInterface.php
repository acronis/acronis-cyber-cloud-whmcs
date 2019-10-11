<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Dispatcher;

interface RouterInterface
{
    /**
     * @param RequestInterface $request
     * @return ActionInterface
     */
    public function getAction(RequestInterface $request);
}