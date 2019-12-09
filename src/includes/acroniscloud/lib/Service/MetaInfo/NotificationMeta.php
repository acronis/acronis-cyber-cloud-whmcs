<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\MetaInfo;

use AcronisCloud\Util\Arr;

class NotificationMeta extends AbstractMeta
{
    const PROPERTY_TYPE = 'type';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_APPLICATION_TYPE = 'application_type';

    /**
     * @return string
     */
    public function getType()
    {
        return (string)Arr::get($this->data, static::PROPERTY_TYPE, '');
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return Arr::get($this->data, static::PROPERTY_TITLE, '');
    }

    /**
     * @return string
     */
    public function getApplicationType()
    {
        return (string)Arr::get($this->data, static::PROPERTY_APPLICATION_TYPE, '');
    }
}