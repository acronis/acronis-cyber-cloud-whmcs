<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Repository\WHMCS;

use AcronisCloud\Model\WHMCS\AddonModule;
use AcronisCloud\Repository\AbstractRepository;
use AcronisCloud\Util\Arr;

class AddonModuleRepository extends AbstractRepository
{
    public function getSettings()
    {
        $settings = AddonModule::where(AddonModule::COLUMN_MODULE, ACRONIS_CLOUD_SERVICE_NAME)
            ->select([AddonModule::COLUMN_SETTING, AddonModule::COLUMN_VALUE])
            ->get()
            ->toArray();

        return Arr::map(
            $settings,
            AddonModule::COLUMN_SETTING,
            AddonModule::COLUMN_VALUE
        );
    }
}