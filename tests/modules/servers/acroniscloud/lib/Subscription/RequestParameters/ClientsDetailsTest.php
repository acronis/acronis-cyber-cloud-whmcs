<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace WHMCS\Module\Server\AcronisCloud\Subscription\RequestParameters;

class ClientsDetailsTest extends \PHPUnit_Framework_TestCase
{
    public function testGetFunctions()
    {
        $data = [
            ClientsDetails::PARAMETER_ADDRESS_1 => 'Pervomayskaya street, house 35',
            ClientsDetails::PARAMETER_ADDRESS_2 => 'Pavlova street, house 2',
            ClientsDetails::PARAMETER_CITY => 'Moscow',
            ClientsDetails::PARAMETER_COUNTRY => 'Russia',
            ClientsDetails::PARAMETER_EMAIL => 'ivanov123@mail.ru',
            ClientsDetails::PARAMETER_FIRST_NAME => 'Ivan',
            ClientsDetails::PARAMETER_LAST_NAME => 'Ivanov',
            ClientsDetails::PARAMETER_PHONE_NUMBER => '+78005553535',
            ClientsDetails::PARAMETER_STATE => 'Moscow Oblast',
            ClientsDetails::PARAMETER_POSTCODE => '101000',
            ClientsDetails::PARAMETER_COMPANY_NAME => 'Acronis',
            ClientsDetails::PARAMETER_LANGUAGE => 'English',
        ];
        $details = new ClientsDetails($data);
        $this->assertEquals(
            $data[ClientsDetails::PARAMETER_ADDRESS_1], $details->getAddress1(), 'Testing address1 failed'
        );
        $this->assertEquals(
            $data[ClientsDetails::PARAMETER_ADDRESS_2], $details->getAddress2(), 'Testing address2 failed'
        );
        $this->assertEquals(
            $data[ClientsDetails::PARAMETER_CITY], $details->getCity(), 'Testing city failed'
        );
        $this->assertEquals(
            $data[ClientsDetails::PARAMETER_COUNTRY], $details->getCountry(), 'Testing country failed'
        );
        $this->assertEquals(
            $data[ClientsDetails::PARAMETER_EMAIL], $details->getEmail(), 'Testing email failed'
        );
        $this->assertEquals(
            $data[ClientsDetails::PARAMETER_FIRST_NAME], $details->getFirstName(), 'Testing first name failed'
        );
        $this->assertEquals(
            $data[ClientsDetails::PARAMETER_LAST_NAME], $details->getLastName(), 'Testing last name failed'
        );
        $this->assertEquals(
            $data[ClientsDetails::PARAMETER_PHONE_NUMBER],
            $details->getPhoneNumber(),
            'Testing phone number failed'
        );
        $this->assertEquals(
            $data[ClientsDetails::PARAMETER_STATE], $details->getState(), 'Testing state failed'
        );
        $this->assertEquals(
            $data[ClientsDetails::PARAMETER_POSTCODE], $details->getPostcode(), 'Testing postcode failed'
        );
        $this->assertEquals(
            $data[ClientsDetails::PARAMETER_COMPANY_NAME], $details->getCompanyName(), 'Testing company name failed'
        );
        $this->assertEquals(
            $data[ClientsDetails::PARAMETER_LANGUAGE], $details->getLanguage(), 'Testing language failed'
        );
    }

}