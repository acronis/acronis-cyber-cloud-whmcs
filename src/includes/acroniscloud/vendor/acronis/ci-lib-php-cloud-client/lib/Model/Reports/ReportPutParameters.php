<?php
/**
 * ReportPutParameters
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

namespace Acronis\Cloud\Client\Model\Reports;

use Acronis\Cloud\Client\BaseModel;
/**
 * ReportPutParameters Class Doc Comment
 *
 * @category    Class
 * @package     Acronis\Cloud\Client
 * @author      Swagger Codegen team
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class ReportPutParameters extends BaseModel 
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      * @var string
      */
    protected static $swaggerModelName = 'Reports\ReportPutParameters';

    /**
     * Array of property to type mappings. Used for (de)serialization
     * @return array
     */
    public static function swaggerTypes()
    {
        return [
            'level' => '\Acronis\Cloud\Client\Model\Reports\ReportPutLevel',
            'kind' => '\Acronis\Cloud\Client\Model\Reports\ReportPutKind'
        ];
    }


    /**
     * Array of property to format mappings. Used for (de)serialization
     * @return array
     */
    public static function swaggerFormats()
    {
        return [
            'level' => null,
            'kind' => null
        ];
    }

    /**
     * Array of attributes where the key is the local name, and the value is the original name
     * @return array
     */
    public static function attributeMap()
    {
        return [
            'level' => 'level',
            'kind' => 'kind'
        ];
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     * @return array
     */
    public static function setters()
    {
        return [
            'level' => 'setLevel',
            'kind' => 'setKind'
        ];
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     * @return array
     */
    public static function getters()
    {
        return [
            'level' => 'getLevel',
            'kind' => 'getKind'
        ];
    }

    /**
     * Array of attributes to checkers functions (for deserialization of responses)
     * @return array
     */
    public static function checkers()
    {
        return [
            'level' => 'hasLevel',
            'kind' => 'hasKind'
        ];
    }

    /**
     * Array of attributes to validators functions (for deserialization of responses)
     * @return array
     */
    public static function validators() {
        return [
            'level' => 'validateLevel',
            'kind' => 'validateKind'
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
        if (!empty($this->validateLevel())) {
            $invalid_properties[] = $this->validateLevel();
        }
        if (!empty($this->validateKind())) {
            $invalid_properties[] = $this->validateKind();
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
        if (!empty($this->validateLevel())) {
            return false;
        }
        if (!empty($this->validateKind())) {
            return false;
        }
        return true;
    }


    /**
     * Gets level
     * @return \Acronis\Cloud\Client\Model\Reports\ReportPutLevel
     */
    public function getLevel()
    {
        return $this->offsetGet('level');
    }

    /**
     * Checks level
     * @return boolean
     */
    public function hasLevel()
    {
        return $this->offsetExists('level');
    }

    /**
     * Sets level
     * @param \Acronis\Cloud\Client\Model\Reports\ReportPutLevel $level
     * @return $this
     */
    public function setLevel($level)
    {
        $this->offsetSet('level', $level);

        return $this;
    }

    /**
     * Unset level
     */
    public function unsetLevel() {
        $this->offsetUnset('level');
    }

    /**
     * Valid level
     * @return array|boolean
     */
    public function validateLevel()
    {
        if (!$this->offsetExists('level')) {
            return false;
        }
        return false;
    }


    /**
     * Gets kind
     * @return \Acronis\Cloud\Client\Model\Reports\ReportPutKind
     */
    public function getKind()
    {
        return $this->offsetGet('kind');
    }

    /**
     * Checks kind
     * @return boolean
     */
    public function hasKind()
    {
        return $this->offsetExists('kind');
    }

    /**
     * Sets kind
     * @param \Acronis\Cloud\Client\Model\Reports\ReportPutKind $kind
     * @return $this
     */
    public function setKind($kind)
    {
        $this->offsetSet('kind', $kind);

        return $this;
    }

    /**
     * Unset kind
     */
    public function unsetKind() {
        $this->offsetUnset('kind');
    }

    /**
     * Valid kind
     * @return array|boolean
     */
    public function validateKind()
    {
        if (!$this->offsetExists('kind')) {
            return false;
        }
        return false;
    }

}


