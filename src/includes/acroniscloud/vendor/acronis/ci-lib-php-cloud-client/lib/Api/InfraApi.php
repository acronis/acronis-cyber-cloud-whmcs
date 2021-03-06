<?php
/**
 * InfraApi
 * PHP version 5
 *
 * @category Class
 * @package  Acronis\Cloud\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * REST API v2 description for Multi-service Portal
 *
 * No description provided (generated by Swagger Codegen https://github.com/swagger-api/swagger-codegen)
 *
 * OpenAPI spec version: 2
 * 
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 *
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace Acronis\Cloud\Client\Api;

use \Acronis\Cloud\Client\ApiClient;
use \Acronis\Cloud\Client\HttpException;
use \Acronis\Cloud\Client\HttpResponse;
use \Acronis\Cloud\Client\Configuration;
use \Acronis\Cloud\Client\ObjectSerializer;

/**
 * InfraApi Class Doc Comment
 *
 * @category Class
 * @package  Acronis\Cloud\Client
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class InfraApi
{
    /**
     * API Client
     *
     * @var \Acronis\Cloud\Client\ApiClient instance of the ApiClient
     */
    protected $apiClient;

    /**
     * Constructor
     *
     * @param ApiClient|null $apiClient The api client to use
     */
    public function __construct(ApiClient $apiClient = null)
    {
        if ($apiClient === null) {
            $apiClient = new ApiClient();
        }

        $this->apiClient = $apiClient;
    }

    /**
     * Get API client
     *
     * @return ApiClient get the API client
     */
    public function getApiClient()
    {
        return $this->apiClient;
    }

    /**
     * Set the API client
     *
     * @param ApiClient $apiClient set the API client
     *
     * @return InfraApi
     */
    public function setApiClient(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
        return $this;
    }

    /**
     * Operation deleteInfraByInfraId
     *
     * InfraByInfraId
     *
     * @param string $infra_id  (required)
     * @param int $version Growing number of infrastructure component&#39;s version (required)
     * @param bool $force When force&#x3D;true is passed delete will ignore existing usage if there are no customers having offering items associated with this infra component turned on (optional)
     * @throws \Acronis\Cloud\Client\ApiException on non-2xx response
     * @return HttpResponse
     */
    public function deleteInfraByInfraId($infra_id, $version, $force = null)
    {
        // verify the required parameter 'infra_id' is set
        if ($infra_id === null) {
            throw new \InvalidArgumentException('Missing the required parameter $infra_id when calling deleteInfraByInfraId');
        }
        // verify the required parameter 'version' is set
        if ($version === null) {
            throw new \InvalidArgumentException('Missing the required parameter $version when calling deleteInfraByInfraId');
        }
        // parse inputs
        $resourcePath = "/infra/{infra_id}";
        $httpBody = '';
        $queryParams = [];
        $headerParams = [];
        $formParams = [];
        $_header_accept = $this->apiClient->selectHeaderAccept(['application/json']);
        if (!is_null($_header_accept)) {
            $headerParams['Accept'] = $_header_accept;
        }
        $headerParams['Content-Type'] = $this->apiClient->selectHeaderContentType(['application/json']);

        // query params
        if ($version !== null) {
            $queryParams['version'] = $this->apiClient->getSerializer()->toQueryValue($version);
        }
        // query params
        if ($force !== null) {
            $queryParams['force'] = $this->apiClient->getSerializer()->toQueryValue($force);
        }
        // path params
        if ($infra_id !== null) {
            $resourcePath = str_replace(
                "{" . "infra_id" . "}",
                $this->apiClient->getSerializer()->toPathValue($infra_id),
                $resourcePath
            );
        }

        // for model (json/xml)
        if (isset($_tempBody)) {
            $httpBody = $_tempBody; // $_tempBody is the method argument, if present
        } elseif (count($formParams) > 0) {
            $httpBody = $formParams; // for HTTP post (form)
        }
        // this endpoint requires OAuth (access token)
        if (strlen($this->apiClient->getConfig()->getAccessToken()) !== 0) {
            $headerParams['Authorization'] = 'Bearer ' . $this->apiClient->getConfig()->getAccessToken();
        }
        // make the API Call
        try {
            list($response, $statusCode, $httpHeader) = $this->apiClient->callApi(
                $resourcePath,
                'DELETE',
                $queryParams,
                $httpBody,
                $headerParams,
                null,
                '/infra/{infra_id}'
            );

            return new HttpResponse($statusCode, $httpHeader, null);
        } catch (HttpException $e) {
            switch ($e->getCode()) {
                case 401:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Acronis\Cloud\Client\Model\Common\Error', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 403:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Acronis\Cloud\Client\Model\Common\Error', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 404:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Acronis\Cloud\Client\Model\Common\Error', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 409:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Acronis\Cloud\Client\Model\Common\Error', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
            }

            throw $e;
        }
    }

    /**
     * Operation getInfra
     *
     * Infra
     *
     * @param string $uuids Comma separated UUIDs of infrastructure components (required)
     * @param string $if_modified_since  (optional)
     * @throws \Acronis\Cloud\Client\ApiException on non-2xx response
     * @return HttpResponse
     */
    public function getInfra($uuids, $if_modified_since = null)
    {
        // verify the required parameter 'uuids' is set
        if ($uuids === null) {
            throw new \InvalidArgumentException('Missing the required parameter $uuids when calling getInfra');
        }
        // parse inputs
        $resourcePath = "/infra";
        $httpBody = '';
        $queryParams = [];
        $headerParams = [];
        $formParams = [];
        $_header_accept = $this->apiClient->selectHeaderAccept(['application/json']);
        if (!is_null($_header_accept)) {
            $headerParams['Accept'] = $_header_accept;
        }
        $headerParams['Content-Type'] = $this->apiClient->selectHeaderContentType(['application/json']);

        // query params
        if ($uuids !== null) {
            $queryParams['uuids'] = $this->apiClient->getSerializer()->toQueryValue($uuids);
        }
        // header params
        if ($if_modified_since !== null) {
            $headerParams['If-Modified-Since'] = $this->apiClient->getSerializer()->toHeaderValue($if_modified_since);
        }

        // for model (json/xml)
        if (isset($_tempBody)) {
            $httpBody = $_tempBody; // $_tempBody is the method argument, if present
        } elseif (count($formParams) > 0) {
            $httpBody = $formParams; // for HTTP post (form)
        }
        // this endpoint requires OAuth (access token)
        if (strlen($this->apiClient->getConfig()->getAccessToken()) !== 0) {
            $headerParams['Authorization'] = 'Bearer ' . $this->apiClient->getConfig()->getAccessToken();
        }
        // make the API Call
        try {
            list($response, $statusCode, $httpHeader) = $this->apiClient->callApi(
                $resourcePath,
                'GET',
                $queryParams,
                $httpBody,
                $headerParams,
                '\Acronis\Cloud\Client\Model\Infra\InfraBatch',
                '/infra'
            );

            return new HttpResponse($statusCode, $httpHeader, $this->apiClient->getSerializer()->deserialize($response, '\Acronis\Cloud\Client\Model\Infra\InfraBatch', $httpHeader));
        } catch (HttpException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Acronis\Cloud\Client\Model\Infra\InfraBatch', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 401:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Acronis\Cloud\Client\Model\Common\Error', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 403:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Acronis\Cloud\Client\Model\Common\Error', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 404:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Acronis\Cloud\Client\Model\Common\Error', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
            }

            throw $e;
        }
    }

    /**
     * Operation getInfraByInfraId
     *
     * InfraByInfraId
     *
     * @param string $infra_id  (required)
     * @param string $if_modified_since  (optional)
     * @throws \Acronis\Cloud\Client\ApiException on non-2xx response
     * @return HttpResponse
     */
    public function getInfraByInfraId($infra_id, $if_modified_since = null)
    {
        // verify the required parameter 'infra_id' is set
        if ($infra_id === null) {
            throw new \InvalidArgumentException('Missing the required parameter $infra_id when calling getInfraByInfraId');
        }
        // parse inputs
        $resourcePath = "/infra/{infra_id}";
        $httpBody = '';
        $queryParams = [];
        $headerParams = [];
        $formParams = [];
        $_header_accept = $this->apiClient->selectHeaderAccept(['application/json']);
        if (!is_null($_header_accept)) {
            $headerParams['Accept'] = $_header_accept;
        }
        $headerParams['Content-Type'] = $this->apiClient->selectHeaderContentType(['application/json']);

        // header params
        if ($if_modified_since !== null) {
            $headerParams['If-Modified-Since'] = $this->apiClient->getSerializer()->toHeaderValue($if_modified_since);
        }
        // path params
        if ($infra_id !== null) {
            $resourcePath = str_replace(
                "{" . "infra_id" . "}",
                $this->apiClient->getSerializer()->toPathValue($infra_id),
                $resourcePath
            );
        }

        // for model (json/xml)
        if (isset($_tempBody)) {
            $httpBody = $_tempBody; // $_tempBody is the method argument, if present
        } elseif (count($formParams) > 0) {
            $httpBody = $formParams; // for HTTP post (form)
        }
        // this endpoint requires OAuth (access token)
        if (strlen($this->apiClient->getConfig()->getAccessToken()) !== 0) {
            $headerParams['Authorization'] = 'Bearer ' . $this->apiClient->getConfig()->getAccessToken();
        }
        // make the API Call
        try {
            list($response, $statusCode, $httpHeader) = $this->apiClient->callApi(
                $resourcePath,
                'GET',
                $queryParams,
                $httpBody,
                $headerParams,
                '\Acronis\Cloud\Client\Model\Infra\Infra',
                '/infra/{infra_id}'
            );

            return new HttpResponse($statusCode, $httpHeader, $this->apiClient->getSerializer()->deserialize($response, '\Acronis\Cloud\Client\Model\Infra\Infra', $httpHeader));
        } catch (HttpException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Acronis\Cloud\Client\Model\Infra\Infra', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 401:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Acronis\Cloud\Client\Model\Common\Error', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 403:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Acronis\Cloud\Client\Model\Common\Error', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 404:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Acronis\Cloud\Client\Model\Common\Error', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
            }

            throw $e;
        }
    }

    /**
     * Operation postInfra
     *
     * Infra
     *
     * @param \Acronis\Cloud\Client\Model\Infra\InfraPost $body  (required)
     * @throws \Acronis\Cloud\Client\ApiException on non-2xx response
     * @return HttpResponse
     */
    public function postInfra($body)
    {
        // verify the required parameter 'body' is set
        if ($body === null) {
            throw new \InvalidArgumentException('Missing the required parameter $body when calling postInfra');
        }
        // parse inputs
        $resourcePath = "/infra";
        $httpBody = '';
        $queryParams = [];
        $headerParams = [];
        $formParams = [];
        $_header_accept = $this->apiClient->selectHeaderAccept(['application/json']);
        if (!is_null($_header_accept)) {
            $headerParams['Accept'] = $_header_accept;
        }
        $headerParams['Content-Type'] = $this->apiClient->selectHeaderContentType(['application/json']);

        // body params
        $_tempBody = null;
        if (isset($body)) {
            $_tempBody = $body;
        }

        // for model (json/xml)
        if (isset($_tempBody)) {
            $httpBody = $_tempBody; // $_tempBody is the method argument, if present
        } elseif (count($formParams) > 0) {
            $httpBody = $formParams; // for HTTP post (form)
        }
        // this endpoint requires OAuth (access token)
        if (strlen($this->apiClient->getConfig()->getAccessToken()) !== 0) {
            $headerParams['Authorization'] = 'Bearer ' . $this->apiClient->getConfig()->getAccessToken();
        }
        // make the API Call
        try {
            list($response, $statusCode, $httpHeader) = $this->apiClient->callApi(
                $resourcePath,
                'POST',
                $queryParams,
                $httpBody,
                $headerParams,
                '\Acronis\Cloud\Client\Model\Infra\Infra',
                '/infra'
            );

            return new HttpResponse($statusCode, $httpHeader, $this->apiClient->getSerializer()->deserialize($response, '\Acronis\Cloud\Client\Model\Infra\Infra', $httpHeader));
        } catch (HttpException $e) {
            switch ($e->getCode()) {
                case 201:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Acronis\Cloud\Client\Model\Infra\Infra', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 401:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Acronis\Cloud\Client\Model\Common\Error', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 403:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Acronis\Cloud\Client\Model\Common\Error', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 415:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Acronis\Cloud\Client\Model\Common\Error', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
            }

            throw $e;
        }
    }

    /**
     * Operation putInfraByInfraId
     *
     * InfraByInfraId
     *
     * @param string $infra_id  (required)
     * @param \Acronis\Cloud\Client\Model\Infra\InfraPut $body  (required)
     * @throws \Acronis\Cloud\Client\ApiException on non-2xx response
     * @return HttpResponse
     */
    public function putInfraByInfraId($infra_id, $body)
    {
        // verify the required parameter 'infra_id' is set
        if ($infra_id === null) {
            throw new \InvalidArgumentException('Missing the required parameter $infra_id when calling putInfraByInfraId');
        }
        // verify the required parameter 'body' is set
        if ($body === null) {
            throw new \InvalidArgumentException('Missing the required parameter $body when calling putInfraByInfraId');
        }
        // parse inputs
        $resourcePath = "/infra/{infra_id}";
        $httpBody = '';
        $queryParams = [];
        $headerParams = [];
        $formParams = [];
        $_header_accept = $this->apiClient->selectHeaderAccept(['application/json']);
        if (!is_null($_header_accept)) {
            $headerParams['Accept'] = $_header_accept;
        }
        $headerParams['Content-Type'] = $this->apiClient->selectHeaderContentType(['application/json']);

        // path params
        if ($infra_id !== null) {
            $resourcePath = str_replace(
                "{" . "infra_id" . "}",
                $this->apiClient->getSerializer()->toPathValue($infra_id),
                $resourcePath
            );
        }
        // body params
        $_tempBody = null;
        if (isset($body)) {
            $_tempBody = $body;
        }

        // for model (json/xml)
        if (isset($_tempBody)) {
            $httpBody = $_tempBody; // $_tempBody is the method argument, if present
        } elseif (count($formParams) > 0) {
            $httpBody = $formParams; // for HTTP post (form)
        }
        // this endpoint requires OAuth (access token)
        if (strlen($this->apiClient->getConfig()->getAccessToken()) !== 0) {
            $headerParams['Authorization'] = 'Bearer ' . $this->apiClient->getConfig()->getAccessToken();
        }
        // make the API Call
        try {
            list($response, $statusCode, $httpHeader) = $this->apiClient->callApi(
                $resourcePath,
                'PUT',
                $queryParams,
                $httpBody,
                $headerParams,
                '\Acronis\Cloud\Client\Model\Infra\Infra',
                '/infra/{infra_id}'
            );

            return new HttpResponse($statusCode, $httpHeader, $this->apiClient->getSerializer()->deserialize($response, '\Acronis\Cloud\Client\Model\Infra\Infra', $httpHeader));
        } catch (HttpException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Acronis\Cloud\Client\Model\Infra\Infra', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 401:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Acronis\Cloud\Client\Model\Common\Error', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 403:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Acronis\Cloud\Client\Model\Common\Error', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 404:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Acronis\Cloud\Client\Model\Common\Error', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 409:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Acronis\Cloud\Client\Model\Common\Error', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
                case 415:
                    $data = $this->apiClient->getSerializer()->deserialize($e->getResponseBody(), '\Acronis\Cloud\Client\Model\Common\Error', $e->getResponseHeaders());
                    $e->setResponseObject($data);
                    break;
            }

            throw $e;
        }
    }
}
