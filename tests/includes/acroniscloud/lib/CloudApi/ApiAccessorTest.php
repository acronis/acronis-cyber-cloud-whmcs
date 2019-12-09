<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\CloudApi;

use Acronis\Cloud\Client\Api\ApplicationsApi;
use Acronis\Cloud\Client\Api\BillingApi;
use Acronis\Cloud\Client\Api\EditionsApi;
use Acronis\Cloud\Client\Api\IdpApi;
use Acronis\Cloud\Client\Api\InfraApi;
use Acronis\Cloud\Client\Api\LocationsApi;
use Acronis\Cloud\Client\Api\ReportsApi;
use Acronis\Cloud\Client\Api\TenantsApi;
use Acronis\Cloud\Client\Api\UsersApi;
use Tests\Reflection;

class ApiAccessorTest extends AbstractApiClientTestCase
{
    /**
     * @dataProvider classesDataProvider
     */
    public function testApiClasses($ClassName, $MethodName)
    {
        $mock = $this->getMockForAbstractClass(ApiAccessor::class);
        $reflection = new Reflection();
        $this->assertInstanceOf(
            $ClassName,
            $reflection->invokeInaccessibleMethod($mock, $MethodName)
        );

        $obj1 = $reflection->invokeInaccessibleMethod($mock, $MethodName);
        $obj2 = $reflection->invokeInaccessibleMethod($mock, $MethodName);
        $this->assertSame($obj1, $obj2);
    }

    public function classesDataProvider()
    {
        return [
            [ApplicationsApi::class, 'getApplicationsApi'],
            [BillingApi::class, 'getBillingApi'],
            [EditionsApi::class, 'getEditionsApi'],
            [IdpApi::class, 'getIdpApi'],
            [InfraApi::class, 'getInfraApi'],
            [LocationsApi::class, 'getLocationsApi'],
            [ReportsApi::class, 'getReportsApi'],
            [TenantsApi::class, 'getTenantsApi'],
            [UsersApi::class, 'getUsersApi'],
        ];
    }
}