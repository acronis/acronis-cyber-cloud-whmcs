<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
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