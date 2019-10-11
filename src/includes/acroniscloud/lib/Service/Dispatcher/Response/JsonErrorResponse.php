<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Dispatcher\Response;

use Acronis\Cloud\Client\HttpException;
use Acronis\Cloud\Client\IOException;
use AcronisCloud\Service\Dispatcher\RequestException;
use AcronisCloud\Util\Arr;
use Exception;

class JsonErrorResponse extends JsonResponse
{
    /** @var array */
    private $details;

    public function __construct(Exception $exception, array $headers = [])
    {
        $exception = $this->prepareException($exception);
        $status = $exception->getCode() ?: static::HTTP_INTERNAL_SERVER_ERROR;
        $this->details = $exception instanceof RequestException ? $exception->getDetails() : [];
        if ($status === static::HTTP_METHOD_NOT_ALLOWED) {
            $allowedMethods = Arr::get($this->details, static::HEADER_ALLOW, []);
            $headers[static::HEADER_ALLOW] = implode(',', $allowedMethods);
        }

        parent::__construct($exception->getMessage(), $status, $headers);
    }

    /**
     * @return string
     */
    protected function renderData()
    {
        return json_encode($this->getErrorData());
    }

    protected function getErrorData()
    {
        $responseData = [
            'error' => [
                'code' => $this->getStatus(),
                'message' => $this->getData(),
            ]
        ];
        if ($this->details) {
            $responseData['error']['details'] = $this->details;
        }

        return $responseData;
    }

    /**
     * @param Exception $e
     * @return Exception
     */
    private function prepareException(Exception $e)
    {
        if ($e instanceof HttpException) {
            return new RequestException(
                $e->getMessage(),
                ['error_code' => $this->prepareHttpExceptionErrorCode($e)],
                $e->getCode(),
                $e
            );
        }

        if ($e instanceof IOException) {
            return new RequestException(
                $e->getMessage(),
                ['error_code' => ErrorCodeInterface::ERROR_API_CONNECTION_PROBLEM],
                $e->getCode(),
                $e
            );
        }

        return $e;
    }

    /**
     * @param HttpException $e
     * @return string
     */
    private function prepareHttpExceptionErrorCode(HttpException $e)
    {
        if (500 <= $e->getCode() && $e->getCode() < 600) {
            return ErrorCodeInterface::ERROR_API_INTERNAL_SERVER_ERROR;
        } else if (400 <= $e->getCode() && $e->getCode() < 500) {
            switch ($e->getCode()) {
                case StatusCodeInterface::HTTP_FORBIDDEN:
                    return ErrorCodeInterface::ERROR_API_FORBIDDEN;
                case StatusCodeInterface::HTTP_UNAUTHORIZED:
                    return ErrorCodeInterface::ERROR_API_UNAUTHORIZED;
                case StatusCodeInterface::HTTP_NOT_FOUND:
                    return ErrorCodeInterface::ERROR_API_NOT_FOUND;
                case StatusCodeInterface::HTTP_CONFLICT:
                    return ErrorCodeInterface::ERROR_API_CONFLICT;
                default:
                    return ErrorCodeInterface::ERROR_API_BAD_REQUEST;
            }
        }

        return null;
    }
}