<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Dispatcher;

use AcronisCloud\Service\Dispatcher\Response\HttpResponse;
use AcronisCloud\Service\Dispatcher\Response\StatusCodeInterface;
use AcronisCloud\Util\Arr;
use AcronisCloud\Util\MemoizeTrait;
use RuntimeException;

class Request implements RequestInterface
{
    use MemoizeTrait;

    /** @var string */
    private $file;
    /** @var string */
    private $function;
    /** @var mixed */
    private $parameters;

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param string $fileName
     * @return self
     */
    public function setFile($fileName)
    {
        $this->file = $fileName;

        return $this;
    }

    /**
     * @return string
     */
    public function getFunction()
    {
        return $this->function;
    }

    /**
     * @param string $functionName
     * @return self
     */
    public function setFunction($functionName)
    {
        $this->function = $functionName;

        return $this;
    }

    /**
     * @param mixed $parameters
     * @return self
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @return  mixed
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return ACRONIS_CLOUD_SERVICE_NAME;
    }

    /**
     * @return string|null
     */
    public function getModuleName()
    {
        return $this->memoize(function () {
            $serviceName = $this->getServiceName();
            $parts = explode(DIRECTORY_SEPARATOR, $this->getFile());
            foreach ($parts as $index => $part) {
                if ($part === static::MODULE_HOOKS) {
                    return static::MODULE_HOOKS;
                }
                if ($part === $serviceName) {
                    return Arr::get($parts, $index - 1);
                }
            }

            return null;
        });
    }

    /**
     * @return string|null
     */
    public function getModuleActionName()
    {
        return $this->memoize(function () {
            $parts = explode(static::ACTION_NAME_DELIMITER, $this->getFunction(), 2);

            return Arr::get($parts, 1);
        });
    }

    /**
     * @return bool
     */
    public function isAjaxRequest()
    {
        return strtolower(Arr::get($_SERVER, static::HTTP_X_REQUESTED_WITH, '')) === 'xmlhttprequest';
    }

    /**
     * @return bool
     */
    public function isPostRequest()
    {
        return $this->getRequestMethod() === static::POST;
    }

    /**
     * @return bool
     */
    public function isGetRequest()
    {
        return $this->getRequestMethod() === static::GET;
    }

    /**
     * @return string
     */
    public function getRequestMethod()
    {
        $requestMethod = Arr::get($_SERVER, static::REQUEST_METHOD);
        if (!$requestMethod) {
            throw new RuntimeException('Request method is not presented.');
        }

        return mb_strtoupper($requestMethod);
    }

    /**
     * @return string
     */
    public function getRequestUrl()
    {
        return $_SERVER['REQUEST_URI'];
    }

    /**
     * @return string
     */
    public function getRequestContentType()
    {
        return Arr::get($_SERVER, static::CONTENT_TYPE);
    }

    /**
     * @return array
     */
    public function getQueryParameters()
    {
        return $_GET;
    }

    /**
     * @param string $key
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getQueryParameter($key, $defaultValue = null)
    {
        return Arr::get($this->getQueryParameters(), $key, $defaultValue);
    }

    /**
     * @return mixed
     */
    public function getBodyParameters()
    {
        return $this->memoize(function() {
            if (!$this->isPostRequest()) {
                throw new RuntimeException('Cannot read post body of non-POST request.', StatusCodeInterface::HTTP_METHOD_NOT_ALLOWED);
            }

            $contentType = $this->getRequestContentType();
            if (strpos($contentType, static::CONTENT_TYPE_JSON) !== false) {
                $json = json_decode(file_get_contents('php://input'), true);
                $this->throwExceptionForLastJsonError();
                return $json;
            } else if (strpos($contentType, 'application/x-www-form-urlencoded') !== false || strpos($this->getRequestContentType(), 'multipart/form-data') !== false) {
                return $_POST;
            }

            throw new RequestException('Cannot access body of request.', [], StatusCodeInterface::HTTP_UNSUPPORTED_MEDIA_TYPE);
        });
    }

    /**
     * @throws RequestException
     */
    public function throwExceptionForLastJsonError()
    {
        $error = json_last_error();
        if ($error === JSON_ERROR_NONE) {
            return;
        }
        $message = json_last_error_msg();
        throw new RequestException($message, [], $error);
    }

    /**
     * @param string $key
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getBodyParameter($key, $defaultValue = null)
    {
        return Arr::get($this->getBodyParameters(), $key, $defaultValue);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasQueryParameter($key)
    {
        return Arr::has($_GET, $key);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function hasBodyParameter($key)
    {
        return Arr::has($this->getBodyParameters(), $key);
    }
}