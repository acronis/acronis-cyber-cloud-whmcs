<?php
/**
 * @Copyright © 2003-2020 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Model\WHMCS;

use AcronisCloud\Model\AbstractModel;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FileAssetSetting extends AbstractModel
{
    const TABLE = 'tblfileassetsettings';

    const COLUMN_TYPE = 'asset_type';
    const COLUMN_STORAGE_ID = 'storageconfiguration_id';

    const TYPE_DOWNLOAD = 'downloads';

    /**
     * @return HasOne
     */
    public function storage()
    {
        return $this->hasOne(StorageConfiguration::class, StorageConfiguration::COLUMN_ID, static::COLUMN_STORAGE_ID);
    }
}