<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
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