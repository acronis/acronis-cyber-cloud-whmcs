<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Repository;

use Acronis\UsageReport\ReportStorageInterface;
use AcronisCloud\Model\ReportStorage;
use WHMCS\Database\Capsule;

class ReportStorageRepository extends AbstractRepository implements ReportStorageInterface
{
    /**
    * {@inheritdoc}
    */
    public function set($key, $value)
    {
        return ReportStorage::create([
            ReportStorage::COLUMN_KEY => $key,
            ReportStorage::COLUMN_VALUE => $value,
        ]);
    }

    /**
    * {@inheritdoc}
    */
    public function get($key)
    {
        $reportStorage = ReportStorage::where(ReportStorage::COLUMN_KEY, $key)->first();

        return $reportStorage ? $reportStorage->getValue() : $reportStorage;
    }

    /**
    * {@inheritdoc}
    */
    public function deleteAll($keyPrefix = '')
    {
        ReportStorage::truncate();
    }
}