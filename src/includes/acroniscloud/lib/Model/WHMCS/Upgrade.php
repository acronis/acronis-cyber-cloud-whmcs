<?php
/**
 * @Copyright © 2003-2020 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Model\WHMCS;

use AcronisCloud\Model\AbstractModel;
use AcronisCloud\Util\Arr;

class Upgrade extends AbstractModel
{
    const TABLE = 'tblupgrades';

    const COLUMN_RELID = 'relid';
    const COLUMN_ORIGINALVALUE = 'originalvalue';
    const COLUMN_NEWVALUE = 'newvalue';
    const COLUMN_STATUS = 'status';

    const STATUS_PENDING = 'Pending';
    const STATUS_COMPLETE = 'Completed';

    public $timestamps = false;

    /**
     * @return string
     */
    public function getRelatedId()
    {
        return $this->getAttributeValue(static::COLUMN_RELID);
    }

    /**
     * @return string
     */
    public function getOriginalValue()
    {
        return $this->getAttributeValue(static::COLUMN_ORIGINALVALUE);
    }

    /**
     * @return string
     */
    public function getNewValue()
    {
        return $this->getAttributeValue(static::COLUMN_NEWVALUE);
    }

    /**
     * @return string
     */
    public function getNewValueWithoutPrice()
    {
        $newValue = $this->getNewValue();
        $values = explode(',', $newValue);

        return Arr::get($values, 0, $newValue);
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->getAttributeValue(static::COLUMN_STATUS);
    }

    /**
     * @param $hostname
     * @return Upgrade
     */
    public function setStatus($status)
    {
        if (!in_array($status, [static::STATUS_PENDING, static::STATUS_COMPLETE])) {
            return $this;
        }
        $this->setAttribute(static::COLUMN_STATUS, $status);

        return $this;
    }
}