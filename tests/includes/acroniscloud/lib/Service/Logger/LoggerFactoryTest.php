<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Logger;

use AcronisCloud\Repository\WHMCS\ConfigurationRepository;
use AcronisCloud\Service\Config\ConfigAccessor;
use AcronisCloud\Service\Config\ConfigFactory;
use AcronisCloud\Service\Config\Settings\LoggerSettings;
use AcronisCloud\Service\Database\Repository\RepositoryFactory;
use AcronisCloud\Service\Locator;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;

class LoggerFactoryTest extends \PHPUnit_Framework_TestCase
{
    protected $processorClasses = [
        UidProcessor::class,
    ];

    public function loggerSettingsProvider()
    {
        $configData = [
            ConfigAccessor::SECTION_LOGGER => [
                LoggerSettings::PROPERTY_ENABLED => true,
                LoggerSettings::PROPERTY_FILENAME => '/var/log/acroniscloud/acronis-cloud.log',
                LoggerSettings::PROPERTY_LEVEL => 'NOTICE',
            ],
        ];

        return [
            'No data in Logger Settings' => [[], NullHandler::class, Logger::DEBUG],
            'Some data in Logger Settings' => [$configData, StreamHandler::class, Logger::NOTICE],
        ];
    }

    /**
     * @dataProvider loggerSettingsProvider
     */
    public function testCreateInstance($loggerSettings, $handlerClass, $propertyLevel)
    {
        $this->setupConfigData($loggerSettings);
        $logger = (new LoggerFactory())->createInstance();

        $processors = $logger->getProcessors();
        if ($loggerSettings) {
            $this->assertEquals(array_map(function ($processor) {
                return get_class($processor);
            }, $processors), $this->processorClasses);
        } else {
            $this->assertEmpty($processors);
        }

        $output = $loggerSettings ? LoggerFactory::LOGGER_RECORD_FORMAT . PHP_EOL : null;
        $formatter = new LineFormatter($output);

        $handlers = $logger->getHandlers();
        foreach ($handlers as $index => $handler) {
            $this->assertInstanceOf($handlerClass, $handler);
            $this->assertEquals($formatter, $handler->getFormatter());
        }

        $handler = $logger->popHandler();
        $this->assertEquals($propertyLevel, $handler->getLevel());
    }

    public function tearDown()
    {
        Locator::getInstance()->reset(ConfigFactory::NAME);
        Locator::getInstance()->reset(RepositoryFactory::NAME);
    }

    protected function setupConfigData($configData, $addonConfigSettings = [])
    {
        $configAccessorMock = $this->getMockObjectGenerator()->getMock(
            ConfigAccessor::class,
            ['getConfigData'],
            [$configData]
        );
        $configAccessorMock->expects($this->any())
            ->method('getConfigData')
            ->will($this->returnValue($configData));

        Locator::getInstance()->set(ConfigFactory::NAME, $configAccessorMock);

        $configurationRepository = $this->getMockObjectGenerator()->getMock(
            ConfigurationRepository::class,
            ['isModuleDebugModeEnabled']
        );

        $configurationRepository->expects($this->any())
            ->method('isModuleDebugModeEnabled')
            ->will($this->returnValue(false));

        $repositoryMock = $this->getMockObjectGenerator()->getMock(
            RepositoryFactory::class,
            ['getConfigurationRepository']
        );

        $repositoryMock->expects($this->any())
            ->method('getConfigurationRepository')
            ->will($this->returnValue($configurationRepository));

        Locator::getInstance()->set(RepositoryFactory::NAME, $repositoryMock);
    }
}
