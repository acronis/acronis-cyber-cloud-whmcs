<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace WHMCS\Module\Server\AcronisCloud\Subscription\RequestParameters;

class AccessorTest extends \PHPUnit_Framework_TestCase
{
    public function testAccessorGetFunctions()
    {
        $data = [
            Accessor::PARAMETER_USER_ID => 100,
            Accessor::PARAMETER_PID => 25,
            Accessor::PARAMETER_SERVICE_ID => 5,
            Accessor::PARAMETER_USERNAME => 'Mr.Smith',
            Accessor::PARAMETER_PASSWORD => 'QWERTY',
            Accessor::PARAMETER_SERVER => true,
            Accessor::PARAMETER_WHMCS_VERSION => 'whmcs2',
            Accessor::PARAMETER_CLIENTS_DETAILS => [
                ClientsDetails::PARAMETER_CITY => 'Moscow',
                ClientsDetails::PARAMETER_COUNTRY => 'Russia',
            ],
        ];
        $accessor = new Accessor($data);
        $this->assertEquals(
            $data[Accessor::PARAMETER_PID], $accessor->getProductId(), 'Testing product id failed'
        );
        $this->assertEquals(
            $data[Accessor::PARAMETER_SERVICE_ID], $accessor->getServiceId(), 'Testing service id failed'
        );
        $this->assertEquals(
            $data[Accessor::PARAMETER_USER_ID], $accessor->getUserId(), 'Testing user id failed'
        );
        $this->assertEquals(
            $data[Accessor::PARAMETER_USERNAME], $accessor->getUsername(), 'Testing username failed'
        );
        $this->assertEquals(
            $data[Accessor::PARAMETER_PASSWORD], $accessor->getPassword(), 'Testing password failed'
        );
        $this->assertEquals(
            $data[Accessor::PARAMETER_SERVER], $accessor->hasServer(), 'Testing server failed'
        );
        $this->assertEquals(
            true, $accessor->hasClientsDetails(), 'Testing clients details failed'
        );
        $this->assertEquals(
            $data[Accessor::PARAMETER_WHMCS_VERSION], $accessor->getWhmcsVersion(), 'Testing version failed'
        );
    }

    /**
     * @dataProvider classDataProvider
     */
    public function testGetClassFunctions($class, $method)
    {
        $data = [
            Accessor::PARAMETER_USER_ID => 100,
            Accessor::PARAMETER_PID => 25,
            Accessor::PARAMETER_SERVICE_ID => 5,
            Accessor::PARAMETER_USERNAME => 'Mr.Smith',
            Accessor::PARAMETER_PASSWORD => 'QWERTY',
            Accessor::PARAMETER_SERVER => true,
            Accessor::PARAMETER_WHMCS_VERSION => 'whmcs2',
            Accessor::PARAMETER_CLIENTS_DETAILS => [
                ClientsDetails::PARAMETER_CITY => 'Moscow',
                ClientsDetails::PARAMETER_COUNTRY => 'Russia',
            ],
        ];
        $accessor = new Accessor($data);
        $this->assertInstanceOf($class, call_user_func([$accessor, $method]));
        $obj1 = call_user_func([$accessor, $method]);
        $obj2 = call_user_func([$accessor, $method]);
        $this->assertSame($obj1, $obj2);
    }

    public function testDefaultValues()
    {
        $accessor = new Accessor([]);
        $this->assertEmpty($accessor->getUsername(), 'Default username value is not empty');
        $this->assertEmpty($accessor->getPassword(), 'Default password value is not empty');
        $this->assertFalse($accessor->hasServer(), 'Default server value is not false');
        $this->assertEmpty($accessor->getWhmcsVersion(), 'Default whmcs version value is not empty');
        $this->assertNull($accessor->getProductId(), 'Default product id is not null');
        $this->assertNull($accessor->getServiceId(), 'Default service id is not null');
        $this->assertNull($accessor->getUserId(), 'Default user id is not null');
        $this->assertFalse($accessor->hasClientsDetails(), 'Default clients details bool value is not false');
    }

    public function classDataProvider()
    {
        return [
            'Product Options class test' => [ProductOptions::class, 'getProductOptions',],
            'Server class test' => [Server::class, 'getServer',],
            'Clients details class test' => [ClientsDetails::class, 'getClientsDetails',],
        ];
    }
}