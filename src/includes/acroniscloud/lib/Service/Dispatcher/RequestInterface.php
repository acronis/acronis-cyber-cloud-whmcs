<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Dispatcher;

interface RequestInterface
{
    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const PATCH = 'PATCH';
    const DELETE = 'DELETE';

    // $_SERVER properties
    const REQUEST_METHOD = 'REQUEST_METHOD';
    const HTTP_X_REQUESTED_WITH = 'HTTP_X_REQUESTED_WITH';
    const CONTENT_TYPE = 'CONTENT_TYPE';
    const CONTENT_TYPE_JSON = 'application/json';

    const ACTION_NAME_DELIMITER = '_';
    const MODULE_HOOKS = 'hooks';

    /**
     * @return string
     */
    public function getFile();

    /**
     * @param string $fileName
     * @return self
     */
    public function setFile($fileName);

    /**
     * @return string
     */
    public function getFunction();

    /**
     * @param string $functionName
     * @return self
     */
    public function setFunction($functionName);

    /**
     * @param mixed $parameters
     * @return self
     */
    public function setParameters($parameters);

    /**
     * @return  mixed
     */
    public function getParameters();

    /**
     * @return string
     */
    public function getServiceName();

    /**
     * @return string|null
     */
    public function getModuleName();

    /**
     * @return string|null
     */
    public function getModuleActionName();

    /**
     * @return bool
     */
    public function isAjaxRequest();

    /**
     * @return bool
     */
    public function isPostRequest();

    /**
     * @return bool
     */
    public function isGetRequest();

    /**
     * @return string
     */
    public function getRequestMethod();

    /**
     * @return string
     */
    public function getRequestUrl();

    /**
     * @return string
     */
    public function getRequestContentType();

    /**
     * @return array
     */
    public function getQueryParameters();

    /**
     * @param string $key
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getQueryParameter($key, $defaultValue = null);

    /**
     * @return mixed
     */
    public function getBodyParameters();

    /**
     * @param string $key
     * @param mixed $defaultValue
     * @return mixed
     */
    public function getBodyParameter($key, $defaultValue = null);

    /**
     * @param string $key
     * @return bool
     */
    public function hasQueryParameter($key);

    /**
     * @param string $key
     * @return bool
     */
    public function hasBodyParameter($key);
}