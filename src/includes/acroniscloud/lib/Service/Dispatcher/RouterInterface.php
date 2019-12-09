<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
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