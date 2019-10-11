<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Logger;

use AcronisCloud\Service\Config\AddonConfigAwareTrait;
use AcronisCloud\Service\Config\ConfigAwareTrait;
use AcronisCloud\Service\Database\Repository\RepositoryAwareTrait;
use AcronisCloud\Service\FactoryInterface;
use AcronisCloud\Service\Logger\Processor\LimitsProcessor;
use AcronisCloud\Service\Logger\Processor\MessageProcessor;
use AcronisCloud\Service\Logger\Processor\PasswordProtectProcessor;
use AcronisCloud\Service\Logger\Processor\TokenProtectProcessor;
use AcronisCloud\Service\Logger\Whmcs\ModuleLogFormatter;
use AcronisCloud\Service\Logger\Whmcs\ModuleLogHandler;
use AcronisCloud\Service\Logger\Whmcs\ProtectSensitiveDataProcessor;
use AcronisCloud\Service\Logger\Whmcs\ScopeCloudApiFormatter;
use AcronisCloud\Service\Logger\Whmcs\ScopeCloudApiProcessor;
use AcronisCloud\Service\Logger\Whmcs\ScopeDbQueryFormatter;
use AcronisCloud\Service\Logger\Whmcs\ScopeWhmcsApiFormatter;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\UidProcessor;

class LoggerFactory implements FactoryInterface
{
    use AddonConfigAwareTrait,
        ConfigAwareTrait,
        RepositoryAwareTrait;

    const NAME = 'logger';

    const LOGGER_RECORD_FORMAT = '%datetime% %extra.uid% %level_name% %message% File: %extra.file%:%extra.line%';

    /**
     * @return Logger
     * @throws \Exception
     */
    public function createInstance()
    {
        $logger = new Logger(ACRONIS_CLOUD_SERVICE_NAME);

        $handlers = [];
        $stream = $this->createStreamHandler();
        if ($stream) {
            $handlers[] = $stream;
        }

        $moduleLog = $this->createModuleLogHandler();
        if ($moduleLog) {
            $handlers[] = $moduleLog;
        }

        // processors are a huge overhead, so we avoid them if not needed
        if ($handlers) {
            $logger->pushProcessor(new UidProcessor());
        } else {
            $handlers[] = new NullHandler();
        }

        foreach ($handlers as $handler) {
            $logger->pushHandler($handler);
        }

        return $logger;
    }

    /**
     * @return StreamHandler|null
     * @throws \Exception
     */
    private function createStreamHandler()
    {
        $settings = $this->getConfig()->getLoggerSettings();

        $enabled = $settings->getEnabled();
        $filename = $settings->getFilename();

        if (!$enabled || !$filename) {
            return null;
        }

        $level = $settings->getLevel();

        $stream = new StreamHandler($filename, $level);
        $formatter = new LineFormatter(static::LOGGER_RECORD_FORMAT . PHP_EOL);
        $stream->setFormatter($formatter);

        $stream->pushProcessor(new TokenProtectProcessor());
        $stream->pushProcessor(new PasswordProtectProcessor());

        $stream->pushProcessor(new IntrospectionProcessor());

        // limits processor needs to be pushed before message processor
        $maxLength = $settings->getMaxMessageLength();
        $stream->pushProcessor(new LimitsProcessor($maxLength));
        $stream->pushProcessor(new MessageProcessor());

        return $stream;
    }

    /**
     * @return ModuleLogHandler|null
     */
    private function createModuleLogHandler()
    {
        $isModuleDebugModeEnabled = $this->getRepository()
            ->getConfigurationRepository()
            ->isModuleDebugModeEnabled();
        if (!$isModuleDebugModeEnabled) {
            return null;
        }

        $addonConfig = $this->getAddonConfig();

        $moduleLogEnabled = $addonConfig->isLoggingCloudApi()
            || $addonConfig->isLoggingDbQuery()
            || $addonConfig->isLoggingWhmcsApi();

        if (!$moduleLogEnabled) {
            return null;
        }

        $moduleLog = new ModuleLogHandler();
        $moduleLog->pushProcessor(new ProtectSensitiveDataProcessor());
        $scopes = [];
        if ($addonConfig->isLoggingDbQuery()) {
            $scopes[ScopeDbQueryFormatter::NAME] = new ScopeDbQueryFormatter();
        }
        if ($addonConfig->isLoggingWhmcsApi()) {
            $scopes[ScopeWhmcsApiFormatter::NAME] = new ScopeWhmcsApiFormatter();
        }
        if ($addonConfig->isLoggingCloudApi()) {
            $scopes[ScopeCloudApiFormatter::NAME] = new ScopeCloudApiFormatter();
            $moduleLog->pushProcessor(new ScopeCloudApiProcessor());
        }
        $moduleLog->setFormatter(new ModuleLogFormatter($scopes));

        return $moduleLog;
    }
}