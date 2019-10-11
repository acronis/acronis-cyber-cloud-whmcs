<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace WHMCS\Module\Server\AcronisCloud\Subscription\RequestParameters;

class ServerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider serverDataProvider
     */
    public function testGetServerFunctions($data)
    {
        $server = new Server($data);
        $this->assertEquals(
            $data[Server::PARAMETER_SERVER_ID], $server->getId(),
            'Testing id failed'
        );
        $this->assertEquals(
            $data[Server::PARAMETER_SERVER_SECURE], $server->isSecure(),
            'Testing schema failed'
        );
        $this->assertEquals(
            $data[Server::PARAMETER_SERVER_HOSTNAME], $server->getHostname(),
            'Testing ip failed'
        );
        $this->assertEquals(
            $data[Server::PARAMETER_SERVER_PORT], $server->getPort(),
            'Testing port failed'
        );
        $this->assertEquals(
            $data[Server::PARAMETER_SERVER_USERNAME], $server->getUsername(),
            'Testing username failed'
        );
        $this->assertEquals(
            $data[Server::PARAMETER_SERVER_PASSWORD], $server->getPassword(),
            'Testing password failed'
        );
    }

    public function testGetDefaultValues()
    {
        $server = new Server([]);
        $this->assertEmpty($server->getHostname(), 'Test getting hostname failed');
        $this->assertEmpty($server->getPort(), 'Test getting port failed');
        $this->assertEmpty(
            $server->getUsername(), 'Test getting username failed'
        );
        $this->assertEmpty(
            $server->getPassword(), 'Test getting password failed'
        );
        $this->assertTrue(
            $server->isSecure(), 'Test getting schema failed'
        );
        $this->assertNull($server->getId(), 'Default id server is not null');
    }

    public function serverDataProvider()
    {
        return [
            [
                [
                    Server::PARAMETER_SERVER_ID => 100,
                    Server::PARAMETER_SERVER_SECURE => true,
                    Server::PARAMETER_SERVER_HOSTNAME => '127.0.0.1',
                    Server::PARAMETER_SERVER_PORT => '8080',
                    Server::PARAMETER_SERVER_PASSWORD => 'qwerty123',
                    Server::PARAMETER_SERVER_USERNAME => 'admin',
                ],
            ],
        ];
    }
}