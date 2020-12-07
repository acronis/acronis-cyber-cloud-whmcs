<?php
/**
 * @Copyright © 2003-2020 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Model\WHMCS;

use AcronisCloud\Model\AbstractModel;

class BillingMetrics extends AbstractModel
{
    const TABLE = 'tblusage_items';

    const COLUMN_TYPE = 'rel_type';
    const COLUMN_MODULE = 'module';
    const COLUMN_IS_HIDDEN = 'is_hidden';

    const TYPE_PRODUCT = 'Product';
    const NOT_HIDDEN = 0;
}