<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Dispatcher\Response;

use AcronisCloud\Util\Str;

class HttpResponse extends DataResponse implements StatusCodeInterface
{
    const HEADER_CONTENT_TYPE = 'Content-Type';
    const HEADER_ALLOW = 'Allow';

    /** @var int */
    private $status;

    /** @var array */
    private $headers;

    /**@var bool */
    private $isPartial;

    /**
     * Response constructor.
     *
     * @param mixed $data
     * @param int $status
     * @param array $headers
     * @param bool $isPartial
     */
    public function __construct($data = null, $status = self::HTTP_OK, array $headers = [], $isPartial = false)
    {
        $this->status = $status;
        $this->headers = $headers;
        $this->isPartial = $isPartial;
        parent::__construct($data);
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function render()
    {
        $status = $this->getStatus();
        http_response_code($status);

        $headers = $this->getHeaders();
        foreach ($headers as $headerName => $headerValue) {
            $header = Str::format(
                '%s: %s',
                $headerName, $headerValue
            );

            header($header);
        }

        return (string)$this->renderData();
    }

    /**
     * @param bool $isPartial
     */
    public function setPartial($isPartial)
    {
        $this->isPartial = $isPartial;
    }

    /**
     * @return bool
     */
    public function isPartial()
    {
        return $this->isPartial;
    }
}