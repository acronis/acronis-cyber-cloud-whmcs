<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Dispatcher\Response;

class HtmlResponse extends HttpResponse
{
    public function __construct($data = null, $status = self::HTTP_OK, array $headers = [])
    {
        $headers[static::HEADER_CONTENT_TYPE] = 'text/html; charset=UTF-8';

        parent::__construct($data, $status, $headers);
    }
}