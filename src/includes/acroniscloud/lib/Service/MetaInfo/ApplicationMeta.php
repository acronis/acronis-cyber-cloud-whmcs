<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\MetaInfo;

use AcronisCloud\Util\Arr;

class ApplicationMeta extends AbstractMeta
{
    const PROPERTY_TYPE = 'type';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_EDITIONS = 'editions';
    const PROPERTY_TENANT_KINDS = 'tenant_kinds';

    /**
     * @return string
     */
    public function getApplicationType()
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
    public function getDescription()
    {
        return Arr::get($this->data, static::PROPERTY_DESCRIPTION, '');
    }

    /**
     * @return array
     */
    public function getTenantKinds()
    {
        return Arr::get($this->data, static::PROPERTY_TENANT_KINDS, []);
    }
}