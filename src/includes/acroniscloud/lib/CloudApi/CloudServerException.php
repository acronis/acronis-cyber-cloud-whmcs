<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\CloudApi;

use Exception;
use Throwable;

class CloudServerException extends Exception
{
    const CODE_EMPTY_SERVER = 1;
    const CODE_EMPTY_HOSTNAME = 2;
    const CODE_EMPTY_USERNAME = 4;
    const CODE_EMPTY_PASSWORD = 8;

    /** @var CloudServerInterface */
    private $server;

    public function __construct($message = '', $code = 0, CloudServerInterface $server = null, Throwable $previous = null)
    {
        $this->server = $server;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return CloudServerInterface
     */
    public function getServer()
    {
        return $this->server;
    }
}