<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Dispatcher;

interface ResponseInterface
{
    /**
     * @param $data
     */
    public function setData($data);

    /**
     * @return mixed
     */
    public function getData();

    /**
     * @return mixed
     */
    public function render();

    /**
     * @return bool
     */
    public function isPartial();
}