<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\MetaInfo;

use AcronisCloud\Util\Arr;

class CustomFieldMeta extends AbstractMeta
{
    const PROPERTY_NAME = 'name';
    const PROPERTY_FRIENDLY_NAME = 'friendly_name';
    const PROPERTY_TYPE = 'type';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_VALIDATION = 'validation';
    const PROPERTY_ADMIN_ONLY = 'admin_only';
    const PROPERTY_REQUIRED = 'required';
    const PROPERTY_SHOW_ON_ORDER = 'show_on_order';
    const PROPERTY_SHOW_ON_INVOICE = 'show_on_invoice';

    /**
     * @return string
     */
    public function getName()
    {
        return (string)Arr::get($this->data, static::PROPERTY_NAME, '');
    }

    /**
     * @return string
     */
    public function getFriendlyName()
    {
        return Arr::get($this->data, static::PROPERTY_FRIENDLY_NAME, '');
    }

    /**
     * @return string
     */
    public function getType()
    {
        return (string)Arr::get($this->data, static::PROPERTY_TYPE, '');
    }

    /**
     * Description is not translated because the original text is needed to create all available translations
     *
     * @return string
     */
    public function getDescription()
    {
        return Arr::get($this->data, static::PROPERTY_DESCRIPTION, '');
    }

    /**
     * @return string
     */
    public function getValidation()
    {
        return (string)Arr::get($this->data, static::PROPERTY_VALIDATION, '');
    }

    /**
     * @return bool
     */
    public function isAdminOnly()
    {
        return Arr::get($this->data, static::PROPERTY_ADMIN_ONLY, false);
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return Arr::get($this->data, static::PROPERTY_REQUIRED, false);
    }

    /**
     * @return bool
     */
    public function isShowOnOrder()
    {
        return Arr::get($this->data, static::PROPERTY_SHOW_ON_ORDER, false);
    }

    /**
     * @return bool
     */
    public function isShowOnInvoice()
    {
        return Arr::get($this->data, static::PROPERTY_SHOW_ON_INVOICE, false);
    }
}