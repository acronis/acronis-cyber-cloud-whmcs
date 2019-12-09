<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Model\WHMCS;

use AcronisCloud\Model\AbstractModel;
use AcronisCloud\Model\Template;
use Illuminate\Database\Eloquent\Relations\HasOne;
use WHMCS\Module\Server\AcronisCloud\Subscription\RequestParameters\ProductOptions;

class Product extends AbstractModel
{
    const TABLE = 'tblproducts';

    const COLUMN_ID = 'id';
    const COLUMN_NAME = 'name';
    const COLUMN_DESCRIPTION = 'description';
    const COLUMN_SERVER_TYPE = 'servertype';

    const PAY_TYPE_FREE = 'free';

    /**
     * @return HasOne
     */
    public function template()
    {
        if ($this->getAttribute(static::COLUMN_SERVER_TYPE) !== ACRONIS_CLOUD_SERVICE_NAME) {
            return null;
        }
        $templateIdCol = ProductOptions::getConfigOptionName(ProductOptions::INDEX_TEMPLATE_ID);

        return $this->hasOne(Template::class, Template::COLUMN_ID, $templateIdCol);
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->getAttributeValue(static::COLUMN_ID);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getAttributeValue(static::COLUMN_NAME);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->getAttributeValue(static::COLUMN_DESCRIPTION);
    }
}