<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
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