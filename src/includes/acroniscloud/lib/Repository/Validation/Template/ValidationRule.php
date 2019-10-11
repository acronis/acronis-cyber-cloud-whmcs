<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Repository\Validation\Template;

use Illuminate\Support\Str;

class ValidationRule
{
    /** @var string */
    private $name;

    /** @var string */
    private $errorMessage;

    /** @var array */
    private $details;

    /**
     * can be the validation function or the predefined result
     *
     * @var callable|string
     */
    private $resolution;

    public function __construct($name, $errorMessage, $resolution)
    {
        $this->name = $name;
        $this->errorMessage = $errorMessage;
        $this->resolution = $resolution;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function getMessageKey()
    {
        return Str::snake($this->name);
    }

    /**
     * @return string
     */
    public function getErrorMessage()
    {
        $details = $this->details
            ? ' Details: ' . json_encode($this->details)
            : '';

        return $this->errorMessage . $details;
    }

    /**
     * @return callable
     */
    public function getResolution()
    {
        return is_callable($this->resolution)
            ? $this->resolution
            : function () {
                return $this->resolution;
            };
    }

    public function setDetails($details)
    {
        $this->details = $details;
    }
}