<?php
/**
 * @Copyright © 2003-2020 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Model\WHMCS;

use AcronisCloud\Model\AbstractModel;
use AcronisCloud\Util\Arr;

class StorageConfiguration extends AbstractModel
{
    const TABLE = 'tblstorageconfigurations';

    const COLUMN_SETTING = 'settings';

    const SETTING_LOCAL_PATH = 'local_path';

    public function localPath()
    {
        $setting = json_decode($this->getAttributeValue(static::COLUMN_SETTING));

        return Arr::get($setting, static::SETTING_LOCAL_PATH);
    }
}