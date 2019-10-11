<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\CloudApi;

use Acronis\Cloud\Client\Api\ApplicationsApi;
use Acronis\Cloud\Client\Api\BillingApi;
use Acronis\Cloud\Client\Api\ClientsApi;
use Acronis\Cloud\Client\Api\EditionsApi;
use Acronis\Cloud\Client\Api\IdpApi;
use Acronis\Cloud\Client\Api\InfraApi;
use Acronis\Cloud\Client\Api\LocationsApi;
use Acronis\Cloud\Client\Api\ReportsApi;
use Acronis\Cloud\Client\Api\TenantsApi;
use Acronis\Cloud\Client\Api\UsersApi;
use Acronis\Cloud\Client\Api\VersionsApi;

abstract class ApiAccessor extends BaseApi
{
    /**
     * @return ApplicationsApi
     */
    protected function getApplicationsApi()
    {
        return $this->memoize(function () {
            return new ApplicationsApi($this->getApiClient());
        });
    }

    /**
     * @return BillingApi
     */
    protected function getBillingApi()
    {
        return $this->memoize(function () {
            return new BillingApi($this->getApiClient());
        });
    }

    /**
     * @return EditionsApi
     */
    protected function getEditionsApi()
    {
        return $this->memoize(function () {
            return new EditionsApi($this->getApiClient());
        });
    }

    /**
     * @return IdpApi
     */
    protected function getIdpApi()
    {
        return $this->memoize(function () {
            return new IdpApi($this->getApiClient());
        });
    }

    /**
     * @return InfraApi
     */
    protected function getInfraApi()
    {
        return $this->memoize(function () {
            return new InfraApi($this->getApiClient());
        });
    }

    /**
     * @return LocationsApi
     */
    protected function getLocationsApi()
    {
        return $this->memoize(function () {
            return new LocationsApi($this->getApiClient());
        });
    }

    /**
     * @return ReportsApi
     */
    protected function getReportsApi()
    {
        return $this->memoize(function () {
            return new ReportsApi($this->getApiClient());
        });
    }

    /**
     * @return TenantsApi
     */
    protected function getTenantsApi()
    {
        return $this->memoize(function () {
            return new TenantsApi($this->getApiClient());
        });
    }

    /**
     * @return UsersApi
     */
    protected function getUsersApi()
    {
        return $this->memoize(function () {
            return new UsersApi($this->getApiClient());
        });
    }

    /**
     * @return VersionsApi
     */
    protected function getVersionsApi()
    {
        return $this->memoize(function () {
            return new VersionsApi($this->getApiClient());
        });
    }

    /**
     * @return ClientsApi
     */
    protected function getClientsApi()
    {
        return $this->memoize(function () {
            return new ClientsApi($this->getApiClient());
        });
    }
}