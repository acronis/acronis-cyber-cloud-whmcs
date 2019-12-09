<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\CloudApi;

class AuthorizedApiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider apiDataProvider
     */
    public function testGetters($url, $login, $password, $grantType)
    {
        $authorizedApi = $this->getMockObjectGenerator()->getMockForAbstractClass(AuthorizedApi::class,
            [$url, $login, $password, $grantType]);
        $this->assertEquals($url, $authorizedApi->getUrl());
        $this->assertEquals($login, $authorizedApi->getLogin());
        $this->assertEquals($password, $authorizedApi->getPassword());
        $this->assertEquals($grantType, $authorizedApi->getGrantType());
    }

    public function apiDataProvider()
    {
        return [
            ['testfactory.com', 'admin', 'qwerty123', 'username'],
            ['testfactory.ru', 'player', '1q2w3e', 'client_credentials'],
            ['testmock.org', 'testname', 'password', 'username'],
        ];
    }
}