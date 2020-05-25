<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Model\WHMCS;

use AcronisCloud\Model\AbstractModel;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property Product product
 */
class Service extends AbstractModel
{
    const TABLE = 'tblhosting';

    const COLUMN_USER_ID = 'userid';
    const COLUMN_PRODUCT_ID = 'packageid';
    const COLUMN_SERVER_ID = 'server';
    const COLUMN_PAYMENT_METHOD = 'paymentmethod';
    const COLUMN_AMOUNT = 'amount';
    const COLUMN_BILLING_CYCLE = 'billingcycle';
    const COLUMN_STATUS = 'domainstatus';
    const COLUMN_NEXT_DUE_DATE = 'nextduedate';
    const COLUMN_REGDATE = 'regdate';

    const RELATION_CLOUD_SERVER = 'cloudServer';
    const RELATION_PRODUCT = 'product';

    const DATE_FORMAT = 'Y-m-d\TH:i:s\Z';
    const DATE_MISSING = '0000-00-00';

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->getAttributeValue(static::COLUMN_USER_ID);
    }

    /**
     * @return int
     */
    public function getProductId()
    {
        return $this->getAttributeValue(static::COLUMN_PRODUCT_ID);
    }

    /**
     * @return int
     */
    public function getServerId()
    {
        return $this->getAttributeValue(static::COLUMN_SERVER_ID);
    }

    /**
     * @param $serverId
     */
    public function setServerId($serverId)
    {
        $this->setAttribute(static::COLUMN_SERVER_ID, $serverId);
    }

    /**
     * @return string
     */
    public function getAmount()
    {
        return $this->getAttributeValue(static::COLUMN_AMOUNT);
    }

    /**
     * @return string
     */
    public function getBillingCycle()
    {
        return $this->getAttributeValue(static::COLUMN_BILLING_CYCLE);
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->getAttributeValue(static::COLUMN_STATUS);
    }

    /**
     * @param string $format
     * @return string
     */
    public function getRegistrationDate($format = self::DATE_FORMAT)
    {
        $rawDate = $this->getAttributeValue(static::COLUMN_REGDATE);

        return $this->formatDate($rawDate, $format);
    }

    /**
     * @param string $format
     * @return string
     */
    public function getNextDueDate($format = self::DATE_FORMAT)
    {
        $rawDate = $this->getAttributeValue(static::COLUMN_NEXT_DUE_DATE);

        return $this->formatDate($rawDate, $format);
    }

    /**
     * @return string
     */
    public function getPaymentMethodName()
    {
        return $this->paymentMethod()
            ->where(PaymentMethod::COLUMN_SETTING, PaymentMethod::SETTING_NAME)
            ->value(PaymentMethod::COLUMN_VALUE);
    }

    /**
     * @return HasOne
     */
    public function cloudServer()
    {
        return $this->hasOne(Server::class, Server::COLUMN_ID, static::COLUMN_SERVER_ID);
    }

    /**
     * @return HasOne
     */
    public function product()
    {
        return $this->hasOne(Product::class, Product::COLUMN_ID, static::COLUMN_PRODUCT_ID);
    }

    /**
     * Payment method settings are split in multiple rows
     *
     * @return HasMany
     */
    public function paymentMethod()
    {
        return $this->hasMany(PaymentMethod::class, PaymentMethod::COLUMN_GATEWAY, static::COLUMN_PAYMENT_METHOD);
    }

    /**
     * @param \DateTime|string $date
     * @param string $format
     * @return string
     */
    protected function formatDate($date, $format)
    {
        if ($date instanceof \DateTime) {
            return $date->format($format);
        } elseif ($date === static::DATE_MISSING) {
            return '';
        } else {
            return date($format, strtotime($date));
        }
    }
}