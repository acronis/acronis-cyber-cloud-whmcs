<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace WHMCS\Module\Server\AcronisCloud\Controller;

use AcronisCloud\Util\Str;

class Notification
{
    // property in post request data
    const PROPERTY_STATUS = 'status';

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    const ALLOWED_STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_INACTIVE,
    ];

    /** @var string */
    private $type;

    /** @var string */
    private $applicationType;

    /** @var string */
    private $status;

    public function __construct($type, $applicationType, $status = self::STATUS_INACTIVE)
    {
        $this->type = $type;
        $this->applicationType = $applicationType;
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getApplicationType()
    {
        return $this->applicationType;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        if (!in_array($status, static::ALLOWED_STATUSES)) {
            throw new \InvalidArgumentException(Str::format(
                'Cannot set status %s for notification %s. Allowed statuses: %s',
                $status, $this->getType(), implode(',', static::ALLOWED_STATUSES)
            ));
        }

        $this->status = $status;
    }
}