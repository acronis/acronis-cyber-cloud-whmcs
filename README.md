# Acronis Cyber Cloud WHMCS provisioning module

The Acronis Cyber Cloud WHMCS provisioning module is a high quality, fully functional WHMCS integration module developed by the Acronis engineering team. The module is the same as the one available at WHMCS Marketplace. On the one hand, it's a valuable example of Acronis Cyber Platform API usage from PHP; on the other hand, it's a great quick start for those companies who want to build their own WHMCS module for Acronis Cyber Cloud. A developer can fully use the code and business logic, and change only what is needed, as we publish it under the MIT license.

## Acronis Cyber Platform Client and Authorization

The implementation of the Acronis Cyber Platform client provides a wide range of functionality. You can find and use advanced workloads for working with Tenants, Users, Reports, and more, in src\includes\acroniscloud\vendor\acronis\ci-lib-php-cloud-client\lib\Api. All of these advanced workloads use the base implementation of low-level API interactions located at src\includes\acroniscloud\lib\CloudApi.

Basic procedures for API calls are implemented in the ApiClient class and authorization routines are implemented in the AuthorizedApi class. The client implementation is quite straightforward – it wraps HTTP calls into the callApi function that is used in the advanced workloads to call specific REST API. Let's pay attention to the way the authorization flow is implemented.

The main way to start is by using the obtainAccessToken function in the AuthorizedApi class. It supports two grant types: password and client credentials. For the password grant type, we directly request the IDP API to receive an authorization token; for the client credentials grant type, we need some additional preparation.

```PHP
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
```

And then we call IDP API as well.

```PHP
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
```

Let's check what happens inside postIdpToken. You can see that it's just an HTTP POST call to the specific endpoint, with all of the required parameters set.

```PHP
public function postIdpToken($grant_type, $client_id = null, $username = null, $password = null, $refresh_token = null, $code = null, $scope = null, $assertion = null, $device_code = null, $totp_code = null)
    {
        // parameters check is removed here for code clearness
        ....

        // parse inputs
        $resourcePath = "/idp/token";
        $httpBody = '';
        $queryParams = [];
        $headerParams = [];
        $formParams = [];
        $_header_accept = $this->apiClient->selectHeaderAccept(['application/json']);
        if (!is_null($_header_accept)) {
            $headerParams['Accept'] = $_header_accept;
        }
        $headerParams['Content-Type'] = $this->apiClient->selectHeaderContentType(['application/x-www-form-urlencoded']);

        // params handling is removed here for code clearness
        .....

        // for model (json/xml)
        if (isset($_tempBody)) {
            $httpBody = $_tempBody; // $_tempBody is the method argument, if present
        } elseif (count($formParams) > 0) {
            $httpBody = $formParams; // for HTTP post (form)
        }
        // make the API Call
        try {
            list($response, $statusCode, $httpHeader) = $this->apiClient->callApi(
                $resourcePath,
                'POST',
                $queryParams,
                $httpBody,
                $headerParams,
                '\Acronis\Cloud\Client\Model\Idp\Token',
                '/idp/token'
            );

            return new HttpResponse($statusCode, $httpHeader, $this->apiClient->getSerializer()->deserialize($response, '\Acronis\Cloud\Client\Model\Idp\Token', $httpHeader));
        } catch (HttpException $e) {
              // exceptopn handling  is removed here for code clearness
              ......
            }

            throw $e;
        }
    }
```

Now, we understand what class is used for API calls and how to review an access token. Let's look into another advanced API usage example.

## Working with API

All API can be found in the Api class located in src\acroniscloud\lib\CloudApi\Api.php. It wraps most of the advanced API uses from src\includes\acroniscloud\vendor\acronis\ci-lib-php-cloud-client\lib\Api.

For example, for a new tenant creation, postTenants functions are used from the TenantsApi class.

```PHP
public function createTenant(TenantPost $body)
    {
        return $this->authorizedCall(function () use ($body) {
            return $this->getTenantsApi()->postTenants($body);
        });
    }
```

And as it is for tenant API calls, it wraps the POST call to the /tenants endpoint and uses the required parameters.

```PHP
/**
    * Operation postTenants
    *
    * Tenants
    *
    * @param \Acronis\Cloud\Client\Model\Tenants\TenantPost $body  (required)
    * @param string $_issues  (optional)
    * @throws \Acronis\Cloud\Client\ApiException on non-2xx response
    * @return HttpResponse
    */
public function postTenants($body, $_issues = null)
{
    // parameters check is removed here for code clearness
        ....
    // parse inputs
    $resourcePath = "/tenants";
    $httpBody = '';
    $queryParams = [];
    $headerParams = [];
    $formParams = [];
    $_header_accept = $this->apiClient->selectHeaderAccept(['application/json']);
    if (!is_null($_header_accept)) {
        $headerParams['Accept'] = $_header_accept;
    }
    $headerParams['Content-Type'] = $this->apiClient->selectHeaderContentType(['application/json']);

    /// params handling is removed here for code clearness
    .....

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
            '\Acronis\Cloud\Client\Model\Tenants\Tenant',
            '/tenants'
        );

        return new HttpResponse($statusCode, $httpHeader, $this->apiClient->getSerializer()->deserialize($response, '\Acronis\Cloud\Client\Model\Tenants\Tenant', $httpHeader));
    } catch (HttpException $e) {
        // exceptopn handling  is removed here for code clearness
        ......
        }

        throw $e;
    }
}
```

Finally, now we understand Acronis Cyber Platform code base structure, and can find and use valuable code pieces.

If you have any questions regarding the Acronis Cyber Platform API samples or usage, see refer to [Acronis Developer Network portal](https://developer.acronis.com/) or use [Acronis Cyber Platform Forum](https://forum.acronis.com/forum/acronis-cyber-platform-forum-apis-and-sdks/acronis-cyber-platform-forum-apis-and-sdks).

## Acronis Cyber Cloud WHMCS Integration and Hooks

Acronis Cyber Cloud WHMCS provisioning module uses WHMCS hooks model to integrate. You can find registered hooks in src\includes\hooks\acroniscloud.php.

```PHP
$hooks = [
    'ServerAdd',
    'ServerDelete',
    'ServerEdit',
    'ClientEdit',
    'ProductEdit',
    'ServiceDelete',
    'AdminAreaHeaderOutput',
    'ClientAreaHeaderOutput',
    'OrderProductUpgradeOverride',
];

$outputHooks = [
    'AdminAreaHeaderOutput',
    'ClientAreaHeaderOutput',
];

foreach ($hooks as $hook) {
    add_hook($hook, 1, function ($parameters) use ($hook, $outputHooks) {
        /** @var Dispatcher $dispatcher */
        $response = '';
        try {
            $dispatcher = Locator::getInstance()->get(DispatcherFactory::NAME);

            $response = $dispatcher->dispatch(
                __FILE__,
                ACRONIS_CLOUD_SERVICE_NAME . RequestInterface::ACTION_NAME_DELIMITER . $hook,
                $parameters
            );
        } catch (Exception $e) {
            // always suppress exceptions for hooks not to affect other WHMCS modules
        }

        return in_array($hook, $outputHooks) ? $response : $parameters;
    });
}
```

And in includes\acroniscloud\controllers.php you can find hooks mapping.

```PHP
'hooks' => new ModuleActionRouter([
        'ServerAdd' => [Server::class, 'updateServerInfo'],
        'ServerEdit' => [Server::class, 'updateServerInfo'],
        'ServerDelete' => [Server::class, 'deleteInternalTag'],
        'ClientEdit' => [ContactInfo::class, 'updateTenants'],
        'ProductEdit' => [Product::class, 'setupCustomFields'],
        'ServiceDelete' => [Subscription::class, 'terminate'],
        'OrderProductUpgradeOverride' => [Product::class, 'beforeUpgrade'],
        'AdminAreaHeaderOutput' => [Server::class, 'adminOutput'],
        'ClientAreaHeaderOutput' => [CustomHeaderOutput::class, 'clientOutput'],
    ])
```

For more information about WHMCS integrations development, refer to [WHMCS portal for developers](https://www.whmcs.com/developer-friendly/).

## Building the Module for WHMCS

To build the module, you need to have the Make utility and Docker installed on your Linux machine. Just type make in the root repository folder, press Enter, and that it's.

```bash
make
```

Under the hood, a docker image is built based on Dockerfile in the root dir repository. This image contains all of the utilities and components necessary to build the module successfully.

And finally, the image is used to start a container and to build the module as described in the build.sh file in the root folder of the repository.