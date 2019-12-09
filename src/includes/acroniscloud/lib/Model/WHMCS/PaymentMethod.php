<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Model\WHMCS;


use AcronisCloud\Model\AbstractModel;

class PaymentMethod extends AbstractModel
{
    const TABLE = 'tblpaymentgateways';

    const COLUMN_GATEWAY = 'gateway';
    const COLUMN_SETTING = 'setting';
    const COLUMN_VALUE = 'value';

    const SETTING_NAME = 'name';
}