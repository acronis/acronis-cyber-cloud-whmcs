<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\MetaInfo;

use AcronisCloud\Util\Arr;

class EditionMeta extends AbstractMeta
{
    const PROPERTY_EDITION_NAME = 'edition_name';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_DESCRIPTION = 'description';

    /**
     * @return string
     */
    public function getEditionName()
    {
        return (string)Arr::get($this->data, static::PROPERTY_EDITION_NAME, '');
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
    public function getDescription()
    {
        return Arr::get($this->data, static::PROPERTY_DESCRIPTION, '');
    }
}