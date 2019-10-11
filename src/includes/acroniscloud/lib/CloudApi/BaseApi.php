<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\CloudApi;

use AcronisCloud\Service\BuildInfo\BuildInfoAwareTrait;
use AcronisCloud\Service\Config\ConfigAwareTrait;
use AcronisCloud\Service\Logger\LoggerAwareTrait;
use AcronisCloud\Util\MemoizeTrait;
use AcronisCloud\Util\Str;
use Acronis\Cloud\Client\ApiClient;
use Acronis\Cloud\Client\Configuration;

abstract class BaseApi
{
    use LoggerAwareTrait,
        BuildInfoAwareTrait,
        ConfigAwareTrait,
        MemoizeTrait;

    const USER_AGENT_TEMPLATE = 'Acronis-WHMCS-Package-%s';
    const ACRONIS_CUSTOM_HEADER = 'X-Acronis-API';

    /**
     * @return string
     */
    abstract protected function getServerUrl();

    /**
     * @param string $url
     * @return ApiClient
     */
    protected function createApiClient($url)
    {
        $apiClientConfig = new Configuration();
        $apiClientConfig->setHost($url);
        $apiClientConfig->setUserAgent($this->getUserAgent());
        $apiClientConfig->setLogger($this->getLogger());

        $apiClientConfig->setSSLVerification($this->getVerifySsl());
        // We always write to logs Acronis cloud api requests
        $apiClientConfig->setDebug(true);
        $apiClientConfig->addDefaultHeader(static::ACRONIS_CUSTOM_HEADER, 1);

        return new ApiClient($apiClientConfig);
    }

    /**
     * @return ApiClient
     */
    protected function getApiClient()
    {
        return $this->memoize(function () {
            return $this->createApiClient($this->getServerUrl());
        });
    }

    private function getUserAgent()
    {
        return $this->memoize(function () {
            return Str::format(
                static::USER_AGENT_TEMPLATE,
                $this->getBuildInfo()->getPackageVersion()
            );
        });
    }

    private function getVerifySsl()
    {
        return $this->getConfig()->getCloudApiSettings()->getVerifySsl();
    }
}