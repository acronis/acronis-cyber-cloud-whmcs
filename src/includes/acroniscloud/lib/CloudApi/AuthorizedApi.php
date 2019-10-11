<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\CloudApi;

use Acronis\Cloud\Client\ApiClient;
use Acronis\Cloud\Client\ApiException;
use Acronis\Cloud\Client\HttpException;
use Acronis\Cloud\Client\HttpResponseInterface;
use Acronis\Cloud\Client\IOException;
use Acronis\Cloud\Client\Model\Idp\Token;
use AcronisCloud\Service\Cache\CacheableTrait;
use AcronisCloud\Util\WHMCS\LocalApi;

abstract class AuthorizedApi extends ApiAccessor implements ApiInterface
{
    use CacheableTrait;

    const URL_PATH_API_V1 = '/api/1';
    const URL_PATH_API_V2 = '/api/2';

    const GRANT_TYPE_CLIENT_CREDENTIALS = 'client_credentials';

    /** @var string */
    private $url;

    /** @var string */
    private $login;

    /** @var string */
    private $password;

    /** @var bool */
    private $grantType;

    public function __construct($url, $login, $password, $grantType)
    {
        $this->url = $url;
        $this->login = $login;
        $this->password = $password;
        $this->grantType = $grantType;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param $clientId
     * @param $clientSecret
     * @throws HttpException
     * @throws IOException
     */
    public function setClientCredentials($clientId, $clientSecret)
    {
        $hostname = $this->resolveServerUrlForLogin();
        $this->setLogin($clientId);
        $this->setPassword($clientSecret);
        $this->setUrl($hostname);
        $this->setGrantType(static::GRANT_TYPE_CLIENT_CREDENTIALS);
        $this->resetAccessToken();
    }

    /**
     * @param $login
     */
    protected function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param $password
     */
    protected function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getGrantType()
    {
        return $this->grantType;
    }

    /**
     * @param $grantType
     */
    protected function setGrantType($grantType)
    {
        $this->grantType = $grantType;
    }

    /**
     * @return string
     * @throws HttpException
     * @throws IOException
     */
    protected function getServerUrl()
    {
        return $this->memoize(function () {
            return $this->resolveServerUrlForLogin() . static::URL_PATH_API_V2;
        });
    }

    /**
     * @return string
     * @throws HttpException
     * @throws IOException
     */
    protected function getServerUrlV1()
    {
        return $this->memoize(function () {
            return $this->resolveServerUrlForLogin() . static::URL_PATH_API_V1;
        });
    }

    /**
     * @param callable $fn
     * @return mixed
     */
    protected function call(callable $fn)
    {
        /** @var HttpResponseInterface $response */
        $response = $fn();

        if ($response instanceof HttpResponseInterface) {
            return $response->getResponseBody();
        }

        return $response;
    }

    /**
     * @param callable $fn
     * @return mixed
     */
    protected function unauthorizedCall(callable $fn)
    {
        $clientConfig = $this->getApiClient()->getConfig();

        $accessToken = $clientConfig->getAccessToken();
        $clientConfig->setAccessToken('');

        $response = $this->call($fn);

        $clientConfig->setAccessToken($accessToken);

        return $response;
    }

    /**
     * @param callable $fn
     * @return mixed
     * @throws HttpException
     * @throws ApiException
     */
    protected function authorizedCall(callable $fn)
    {
        if (!$this->isAuthorized()) {
            $this->authorize();
        }
        try {
            return $this->call($fn);
        } catch (HttpException $e) {
            if ($e->getCode() != 401) {
                throw $e;
            }
            $this->authorize(true);
        }

        return $this->call($fn);
    }

    /**
     * @param bool $force
     * @return string
     */
    protected function obtainAccessToken($force = false)
    {
        return LocalApi::decryptPassword($this->fromCache(function () {
            if ($this->getGrantType() === static::GRANT_TYPE_CLIENT_CREDENTIALS) {
                $idpToken = $this->fetchClientAccessToken();
            } else {
                /** @var Token $idpToken */
                $idpToken = $this->unauthorizedCall(function () {
                    return $this->getIdpApi()->postIdpToken(
                        'password', null, $this->getLogin(), $this->getPassword()
                    );
                });
            }

            return LocalApi::encryptPassword($idpToken->getAccessToken());
        }, $this->getAccessTokenTtl(), true, $this->getCredentialsHash(), $force));
    }

    protected function resetAccessToken()
    {
        $this->authorize(true);
    }

    protected function getAccessTokenTtl()
    {
        return $this->getConfig()->getCloudApiSettings()->getAccessTokenTtl();
    }

    protected function getCredentialsHash()
    {
        return md5($this->getUrl() . $this->getLogin() . $this->getPassword());
    }

    /**
     * @return ApiClient
     */
    protected function getApiV1Client()
    {
        return $this->memoize(function () {
            return $this->createApiClient($this->getServerUrlV1());
        });
    }

    /**
     * @param string $path
     * @param string $method
     * @param array|null $queryParameters
     * @param null $body
     * @return mixed
     * @throws HttpException
     * @throws IOException
     */
    protected function requestApiV1Method($path, $method = 'GET', array $queryParameters = null, $body = null)
    {
        $apiClient = $this->getApiV1Client();

        $headers = [
            'Content-Type' => 'application/json',
        ];

        $token = $this->getAccessToken();
        if ($token) {
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        list($response, $statusCode, $responseHeaders) = $apiClient->callApi(
            $path, $method, $queryParameters, $body, $headers
        );

        return $response;
    }

    /**
     * @return string | null
     */
    protected function getAccessToken()
    {
        $token = $this->getApiClient()
            ->getConfig()
            ->getAccessToken();

        return $token;
    }

    private function isAuthorized()
    {
        $token = $this->getAccessToken();

        return !empty($token);
    }

    private function authorize($force = false)
    {
        $this->getApiClient()
            ->getConfig()
            ->setAccessToken($this->obtainAccessToken($force));
    }

    private function fetchClientAccessToken()
    {
        $clientId = $this->getLogin();
        $clientSecret = $this->getPassword();
        $authCredentials = base64_encode("{$clientId}:$clientSecret");
        $this->getApiClient()->getConfig()->addDefaultHeader(
            'Authorization',
            "Basic {$authCredentials}"
        );
        $idpToken = $this->unauthorizedCall(function () {
            return $this->getIdpApi()->postIdpToken('client_credentials');
        });
        $this->getApiClient()->getConfig()->deleteDefaultHeader('Authorization');

        return $idpToken;
    }

    /**
     * @return string
     * @throws HttpException
     * @throws IOException
     */
    protected function resolveServerUrlForLogin()
    {
        return $this->fromCache(function () {
            if ($this->getGrantType() === static::GRANT_TYPE_CLIENT_CREDENTIALS) {
                return $this->getUrl();
            }
            $accountInfo = $this->fetchAccountInfo($this->login);

            return rtrim($accountInfo->server_url, '/');
        }, $this->getAccessTokenTtl(), false, $this->getCredentialsHash());
    }

    /**
     * @param string $login
     * @return object
     * @throws HttpException
     * @throws IOException
     */
    private function fetchAccountInfo($login)
    {
        $url = rtrim($this->getUrl(), '/') . static::URL_PATH_API_V1;
        $apiClient = $this->createApiClient($url);

        $path = '/accounts';
        $method = 'GET';
        $queryParameters = ['login' => $login];
        $body = null;
        $headers = [];

        list($response, $statusCode, $httpHeader) = $apiClient->callApi(
            $path, $method, $queryParameters, $body, $headers
        );

        return $response;
    }
}