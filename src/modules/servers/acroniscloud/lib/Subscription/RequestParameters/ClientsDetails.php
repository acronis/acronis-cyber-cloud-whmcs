<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace WHMCS\Module\Server\AcronisCloud\Subscription\RequestParameters;

use AcronisCloud\Util\Arr;

class ClientsDetails
{
    const PARAMETER_ADDRESS_1 = 'address1';
    const PARAMETER_ADDRESS_2 = 'address2';
    const PARAMETER_CITY = 'city';
    const PARAMETER_COUNTRY = 'country';
    const PARAMETER_EMAIL = 'email';
    const PARAMETER_FIRST_NAME = 'firstname';
    const PARAMETER_LAST_NAME = 'lastname';
    const PARAMETER_PHONE_NUMBER = 'phonenumber';
    const PARAMETER_STATE = 'state';
    const PARAMETER_POSTCODE = 'postcode';
    const PARAMETER_COMPANY_NAME = 'companyname';
    const PARAMETER_LANGUAGE = 'language';

    /** @var array */
    private $clientsDetails;

    /**
     * ClientsDetails constructor.
     *
     * @param array $clientsDetails
     */
    public function __construct(array $clientsDetails)
    {
        $this->clientsDetails = $clientsDetails;
    }

    /**
     * @return string
     */
    public function getAddress1()
    {
        return Arr::get($this->clientsDetails, static::PARAMETER_ADDRESS_1, '');
    }

    /**
     * @return string
     */
    public function getAddress2()
    {
        return Arr::get($this->clientsDetails, static::PARAMETER_ADDRESS_2, '');
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return Arr::get($this->clientsDetails, static::PARAMETER_CITY, '');
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return Arr::get($this->clientsDetails, static::PARAMETER_COUNTRY, '');
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return Arr::get($this->clientsDetails, static::PARAMETER_EMAIL, '');
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return Arr::get($this->clientsDetails, static::PARAMETER_FIRST_NAME, '');
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return Arr::get($this->clientsDetails, static::PARAMETER_LAST_NAME, '');
    }

    /**
     * @return string
     */
    public function getPhoneNumber()
    {
        return Arr::get($this->clientsDetails, static::PARAMETER_PHONE_NUMBER, '');
    }

    /**
     * @return string
     */
    public function getState()
    {
        return Arr::get($this->clientsDetails, static::PARAMETER_STATE, '');
    }

    /**
     * @return string
     */
    public function getPostcode()
    {
        return Arr::get($this->clientsDetails, static::PARAMETER_POSTCODE, '');
    }

    /**
     * @return string
     */
    public function getCompanyName()
    {
        return Arr::get($this->clientsDetails, self::PARAMETER_COMPANY_NAME, '');
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return Arr::get($this->clientsDetails, self::PARAMETER_LANGUAGE, '');
    }
}