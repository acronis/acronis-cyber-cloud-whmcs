<?php
/**
 * TenantPricingSettingsPut
 *
 * PHP version 5
 *
 * @category Class
 * @package  Acronis\Cloud\Client
 * @author   Swaagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * REST API v2 description for Multi-service Portal
 *
 * No description provided (generated by Swagger Codegen https://github.com/swagger-api/swagger-codegen)
 *
 * OpenAPI spec version: 2
 * 
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 *
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace Acronis\Cloud\Client\Model\Pricing;

use Acronis\Cloud\Client\BaseModel;
/**
 * TenantPricingSettingsPut Class Doc Comment
 *
 * @category    Class
 * @package     Acronis\Cloud\Client
 * @author      Swagger Codegen team
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class TenantPricingSettingsPut extends BaseModel 
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      * @var string
      */
    protected static $swaggerModelName = 'Pricing\TenantPricingSettingsPut';

    /**
     * Array of property to type mappings. Used for (de)serialization
     * @return array
     */
    public static function swaggerTypes()
    {
        return [
            'mode' => '\Acronis\Cloud\Client\Model\Pricing\TenantPricingSettingsPutMode',
            'currency' => 'object',
            'version' => 'int'
        ];
    }


    /**
     * Array of property to format mappings. Used for (de)serialization
     * @return array
     */
    public static function swaggerFormats()
    {
        return [
            'mode' => null,
            'currency' => null,
            'version' => 'int32'
        ];
    }

    /**
     * Array of attributes where the key is the local name, and the value is the original name
     * @return array
     */
    public static function attributeMap()
    {
        return [
            'mode' => 'mode',
            'currency' => 'currency',
            'version' => 'version'
        ];
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     * @return array
     */
    public static function setters()
    {
        return [
            'mode' => 'setMode',
            'currency' => 'setCurrency',
            'version' => 'setVersion'
        ];
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     * @return array
     */
    public static function getters()
    {
        return [
            'mode' => 'getMode',
            'currency' => 'getCurrency',
            'version' => 'getVersion'
        ];
    }

    /**
     * Array of attributes to checkers functions (for deserialization of responses)
     * @return array
     */
    public static function checkers()
    {
        return [
            'mode' => 'hasMode',
            'currency' => 'hasCurrency',
            'version' => 'hasVersion'
        ];
    }

    /**
     * Array of attributes to validators functions (for deserialization of responses)
     * @return array
     */
    public static function validators() {
        return [
            'mode' => 'validateMode',
            'currency' => 'validateCurrency',
            'version' => 'validateVersion'
        ];
    }

    

    

    /**
     * show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalid_properties = [];
        if (!empty($this->validateMode())) {
            $invalid_properties[] = $this->validateMode();
        }
        if (!empty($this->validateCurrency())) {
            $invalid_properties[] = $this->validateCurrency();
        }
        if (!empty($this->validateVersion())) {
            $invalid_properties[] = $this->validateVersion();
        }
        return $invalid_properties;
    }

    /**
     * validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {
        if (!empty($this->validateMode())) {
            return false;
        }
        if (!empty($this->validateCurrency())) {
            return false;
        }
        if (!empty($this->validateVersion())) {
            return false;
        }
        return true;
    }


    /**
     * Gets mode
     * @return \Acronis\Cloud\Client\Model\Pricing\TenantPricingSettingsPutMode
     */
    public function getMode()
    {
        return $this->offsetGet('mode');
    }

    /**
     * Checks mode
     * @return boolean
     */
    public function hasMode()
    {
        return $this->offsetExists('mode');
    }

    /**
     * Sets mode
     * @param \Acronis\Cloud\Client\Model\Pricing\TenantPricingSettingsPutMode $mode
     * @return $this
     */
    public function setMode($mode)
    {
        $this->offsetSet('mode', $mode);

        return $this;
    }

    /**
     * Unset mode
     */
    public function unsetMode() {
        $this->offsetUnset('mode');
    }

    /**
     * Valid mode
     * @return array|boolean
     */
    public function validateMode()
    {
        if (!$this->offsetExists('mode')) {
            return false;
        }
        return false;
    }


    /**
     * Gets currency
     * @return object
     */
    public function getCurrency()
    {
        return $this->offsetGet('currency');
    }

    /**
     * Checks currency
     * @return boolean
     */
    public function hasCurrency()
    {
        return $this->offsetExists('currency');
    }

    /**
     * Sets currency
     * @param object $currency Currency of all offering item prices for this tenant
     * @return $this
     */
    public function setCurrency($currency)
    {
        $this->offsetSet('currency', $currency);

        return $this;
    }

    /**
     * Unset currency
     */
    public function unsetCurrency() {
        $this->offsetUnset('currency');
    }

    /**
     * Valid currency
     * @return array|boolean
     */
    public function validateCurrency()
    {
        if (!$this->offsetExists('currency')) {
            return false;
        }
        return false;
    }


    /**
     * Gets version
     * @return int
     */
    public function getVersion()
    {
        return $this->offsetGet('version');
    }

    /**
     * Checks version
     * @return boolean
     */
    public function hasVersion()
    {
        return $this->offsetExists('version');
    }

    /**
     * Sets version
     * @param int $version Auto-incremented entity version
     * @return $this
     */
    public function setVersion($version)
    {
        $this->offsetSet('version', $version);

        return $this;
    }

    /**
     * Unset version
     */
    public function unsetVersion() {
        $this->offsetUnset('version');
    }

    /**
     * Valid version
     * @return array|boolean
     */
    public function validateVersion()
    {
        if (!$this->offsetExists('version')) {
            return "Property 'version' was not initialized.";
        }
        return false;
    }

}


