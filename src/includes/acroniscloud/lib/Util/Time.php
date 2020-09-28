<?php
/**
 * @Copyright © 2002-2017 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Util;

class Time
{
    const DEFAULT_TIMEZONE = 'UTC';
    const DEFAULT_DATE_FORMAT = 'Y-m-d';

    /**
     * @param string $timeZone
     * @param string $dateFormat
     * @param int $dayOffset
     * @return string
     */
    public static function getCurrentDate($timeZone = self::DEFAULT_TIMEZONE, $dateFormat = self::DEFAULT_DATE_FORMAT, $dayOffset = 0)
    {
        $timezone = new \DateTimeZone($timeZone);
        $currentDate = static::offsetDate(new \DateTime('now', $timezone), $dayOffset);

        return $currentDate->format($dateFormat);
    }

    /**
     * @param \DateTime $date
     * @param int $dayOffset
     * @param string $dateFormat
     * @return \DateTime
     */
    public static function offsetDate($date, $dayOffset = 0)
    {
        if ($dayOffset !== 0) {
            $dayOffset = intval($dayOffset);
            $modifyExpr = ($dayOffset < 0 ? $dayOffset : '+' . $dayOffset) . ' day';
            $date->modify($modifyExpr);
        }

        return $date;
    }

} 