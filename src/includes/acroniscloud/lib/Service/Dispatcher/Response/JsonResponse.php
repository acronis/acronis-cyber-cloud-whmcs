<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Dispatcher\Response;

use AcronisCloud\Service\Dispatcher\Request;

class JsonResponse extends HttpResponse
{
    public function __construct($data = null, $status = self::HTTP_OK, array $headers = [], $isPartial = true)
    {
        $headers[static::HEADER_CONTENT_TYPE] = Request::CONTENT_TYPE_JSON;

        parent::__construct($data, $status, $headers, $isPartial);
    }

    /**
     * @return string
     */
    protected function renderData()
    {
        return json_encode($this->getData());
    }
}