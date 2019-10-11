<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\CloudApi;

use AcronisCloud\Service\BuildInfo\BuildInfoParser;
use AcronisCloud\Service\Locator;
use AcronisCloud\Service\BuildInfo\BuildInfoFactory;
use AcronisCloud\Service\Logger\LoggerFactory;
use AcronisCloud\Service\Config\ConfigFactory;
use Psr\Log\LoggerInterface;

abstract class AbstractApiClientTestCase extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->setupApiClient();
    }

    public function tearDown()
    {
        parent::tearDown();
        Locator::getInstance()->reset(ConfigFactory::NAME);
        Locator::getInstance()->reset(BuildInfoFactory::NAME);
        Locator::getInstance()->reset(LoggerFactory::NAME);
    }

    protected function setupApiClient()
    {
        $locator = Locator::getInstance();

        $buildInfoParserMock = $this->getMockBuilder(BuildInfoParser::class)->disableOriginalConstructor()->getMock();
        $locator->set(BuildInfoFactory::NAME, $buildInfoParserMock);

        $loggerMock = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();
        $locator->set(LoggerFactory::NAME, $loggerMock);

        $configFactoryMock = $this->getMockObjectGenerator()->getMock(ConfigFactory::class, ['getConfigData']);

        $configFactoryMock->expects($this->any())
            ->method('getConfigData')
            ->will($this->returnValue([]));

        $locator->addFactory(ConfigFactory::NAME, $configFactoryMock);
    }
}
