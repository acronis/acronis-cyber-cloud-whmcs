<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Config\Settings;

use AcronisCloud\Util\Arr;
use Monolog\Logger;

class LoggerSettings extends AbstractSettings
{
    const PROPERTY_ENABLED = 'enabled';
    const PROPERTY_LEVEL = 'level';
    const PROPERTY_FILENAME = 'filename';
    const PROPERTY_MAX_MESSAGE_LENGTH = 'max_message_length';

    /**
     * @return bool
     */
    public function getEnabled()
    {
        return (bool)Arr::get($this->settings, static::PROPERTY_ENABLED, false);
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        $levelName = Arr::get($this->settings, static::PROPERTY_LEVEL, '');
        $levelName = is_string($levelName)
            ? mb_strtoupper(trim($levelName))
            : '';

        return Arr::get(Logger::getLevels(), $levelName, Logger::NOTICE);
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        $filename = Arr::get($this->settings, static::PROPERTY_FILENAME);

        return is_string($filename) ? $filename : '';
    }

    /**
     * @return int
     */
    public function getMaxMessageLength()
    {
        return (int) Arr::get($this->settings, static::PROPERTY_MAX_MESSAGE_LENGTH);
    }
}