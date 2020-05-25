<?php
/**
 * Error
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

namespace Acronis\Cloud\Client\Model\Common;

use Acronis\Cloud\Client\BaseModel;
/**
 * Error Class Doc Comment
 *
 * @category    Class
 * @package     Acronis\Cloud\Client
 * @author      Swagger Codegen team
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class Error extends BaseModel 
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      * @var string
      */
    protected static $swaggerModelName = 'Common\Error';

    /**
     * Array of property to type mappings. Used for (de)serialization
     * @return array
     */
    public static function swaggerTypes()
    {
        return [
            'error' => '\Acronis\Cloud\Client\Model\Common\ErrorError'
        ];
    }


    /**
     * Array of property to format mappings. Used for (de)serialization
     * @return array
     */
    public static function swaggerFormats()
    {
        return [
            'error' => null
        ];
    }

    /**
     * Array of attributes where the key is the local name, and the value is the original name
     * @return array
     */
    public static function attributeMap()
    {
        return [
            'error' => 'error'
        ];
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     * @return array
     */
    public static function setters()
    {
        return [
            'error' => 'setError'
        ];
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     * @return array
     */
    public static function getters()
    {
        return [
            'error' => 'getError'
        ];
    }

    /**
     * Array of attributes to checkers functions (for deserialization of responses)
     * @return array
     */
    public static function checkers()
    {
        return [
            'error' => 'hasError'
        ];
    }

    /**
     * Array of attributes to validators functions (for deserialization of responses)
     * @return array
     */
    public static function validators() {
        return [
            'error' => 'validateError'
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
        if (!empty($this->validateError())) {
            $invalid_properties[] = $this->validateError();
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
        if (!empty($this->validateError())) {
            return false;
        }
        return true;
    }


    /**
     * Gets error
     * @return \Acronis\Cloud\Client\Model\Common\ErrorError
     */
    public function getError()
    {
        return $this->offsetGet('error');
    }

    /**
     * Checks error
     * @return boolean
     */
    public function hasError()
    {
        return $this->offsetExists('error');
    }

    /**
     * Sets error
     * @param \Acronis\Cloud\Client\Model\Common\ErrorError $error
     * @return $this
     */
    public function setError($error)
    {
        $this->offsetSet('error', $error);

        return $this;
    }

    /**
     * Unset error
     */
    public function unsetError() {
        $this->offsetUnset('error');
    }

    /**
     * Valid error
     * @return array|boolean
     */
    public function validateError()
    {
        if (!$this->offsetExists('error')) {
            return "Property 'error' was not initialized.";
        }
        return false;
    }

}

