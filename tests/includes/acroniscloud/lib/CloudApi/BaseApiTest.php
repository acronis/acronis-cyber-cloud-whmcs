<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\CloudApi;

use Acronis\Cloud\Client\ApiClient;
use Tests\Reflection;

class BaseApiTest extends AbstractApiClientTestCase
{
    public function testGetApiClient()
    {
        $baseApiMock = $this->getMockObjectGenerator()->getMockForAbstractClass(BaseApi::class);

        $reflection = new Reflection();
        $this->assertInstanceOf(ApiClient::class, $reflection->invokeInaccessibleMethod($baseApiMock, 'getApiClient'));

        $api1 = $reflection->invokeInaccessibleMethod($baseApiMock, 'getApiClient');
        $api2 = $reflection->invokeInaccessibleMethod($baseApiMock, 'getApiClient');
        $this->assertSame($api1, $api2);
    }
}