<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\MetaInfo;

use AcronisCloud\Util\Arr;

abstract class AbstractMeta
{
    const PROPERTY_SORT_PRIORITY = 'sort_priority';

    /** @var array */
    protected $data;

    /**
     * ConfigurableOption constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return int
     */
    public function getSortPriority()
    {
        return (int)Arr::get($this->data, static::PROPERTY_SORT_PRIORITY, 0);
    }
}