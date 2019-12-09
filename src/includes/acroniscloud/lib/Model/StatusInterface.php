<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Model;

interface StatusInterface
{
    /** @var string used in request payloads */
    const PROPERTY_STATUS = 'status';

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    const ALLOWED_STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
    ];
}