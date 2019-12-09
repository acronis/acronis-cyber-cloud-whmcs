<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Model\WHMCS;

use AcronisCloud\Model\AbstractModel;

class Admin extends AbstractModel
{
    const TABLE = 'tbladmins';
    const PERMISSIONS_TABLE = 'tbladminperms';
    const ADMIN_PERMISSION_ID = 81;
}