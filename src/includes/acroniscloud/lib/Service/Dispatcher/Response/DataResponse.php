<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Dispatcher\Response;

use AcronisCloud\Service\Dispatcher\ResponseInterface;

class DataResponse implements ResponseInterface
{
    const ERROR = 'error';
    const INFO = 'info';
    const SUCCESS = 'success';

    /** @var mixed */
    private $data;

    /**
     * Response constructor.
     *
     * @param mixed $data
     */
    public function __construct($data = null)
    {
        $this->setData($data);
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function render()
    {
        return $this->renderData();
    }

    /**
     * @return mixed
     */
    protected function renderData()
    {
        return $this->getData();
    }

    /**
     * @return bool
     */
    public function isPartial()
    {
        return false;
    }
}