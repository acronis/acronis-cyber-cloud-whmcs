<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
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