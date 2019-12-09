<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Config;

use AcronisCloud\Service\Locator;

abstract class ConfigSectionTest extends \PHPUnit_Framework_TestCase
{
    use ConfigAwareTrait;

    protected $configData = null;

    public function setUp()
    {
        parent::setUp();
        $this->setupConfigData($this->configData);
    }

    public function tearDown()
    {
        parent::tearDown();
        Locator::getInstance()->reset(ConfigFactory::NAME);
    }

    public function setupConfigData($configData)
    {
        $configFactoryMock = $this->getMockObjectGenerator()->getMock(ConfigFactory::class, ['getConfigData']);
        $configFactoryMock->expects($this->any())
            ->method('getConfigData')
            ->will($this->returnValue($configData));

        Locator::getInstance()->addFactory(ConfigFactory::NAME, $configFactoryMock);
    }

}