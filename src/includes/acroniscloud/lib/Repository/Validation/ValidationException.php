<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Repository\Validation;

use Throwable;

class ValidationException extends \Exception
{
    /**
     * @var string
     */
    private $errorName;

    /**
     * @var array
     */
    private $data;

    public function __construct($errorName, array $data)
    {
        $this->errorName = $errorName;
        $this->data = $data;
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getErrorName()
    {
        return $this->errorName;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }
}