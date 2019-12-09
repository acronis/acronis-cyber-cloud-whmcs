<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Model\WHMCS;

use AcronisCloud\Model\AbstractModel;

class AddonModule extends AbstractModel
{
    const TABLE = 'tbladdonmodules';

    const COLUMN_MODULE = 'module';
    const COLUMN_SETTING = 'setting';
    const COLUMN_VALUE = 'value';
}