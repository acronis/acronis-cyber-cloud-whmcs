<?php
/**
 * LocationPost
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

namespace Acronis\Cloud\Client\Model\Locations;

use Acronis\Cloud\Client\BaseModel;
/**
 * LocationPost Class Doc Comment
 *
 * @category    Class
 * @package     Acronis\Cloud\Client
 * @author      Swagger Codegen team
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class LocationPost extends BaseModel 
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      * @var string
      */
    protected static $swaggerModelName = 'Locations\LocationPost';

    /**
     * Array of property to type mappings. Used for (de)serialization
     * @return array
     */
    public static function swaggerTypes()
    {
        return [
            'owner_tenant_id' => 'string',
            'name' => 'string'
        ];
    }


    /**
     * Array of property to format mappings. Used for (de)serialization
     * @return array
     */
    public static function swaggerFormats()
    {
        return [
            'owner_tenant_id' => null,
            'name' => null
        ];
    }

    /**
     * Array of attributes where the key is the local name, and the value is the original name
     * @return array
     */
    public static function attributeMap()
    {
        return [
            'owner_tenant_id' => 'owner_tenant_id',
            'name' => 'name'
        ];
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     * @return array
     */
    public static function setters()
    {
        return [
            'owner_tenant_id' => 'setOwnerTenantId',
            'name' => 'setName'
        ];
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     * @return array
     */
    public static function getters()
    {
        return [
            'owner_tenant_id' => 'getOwnerTenantId',
            'name' => 'getName'
        ];
    }

    /**
     * Array of attributes to checkers functions (for deserialization of responses)
     * @return array
     */
    public static function checkers()
    {
        return [
            'owner_tenant_id' => 'hasOwnerTenantId',
            'name' => 'hasName'
        ];
    }

    /**
     * Array of attributes to validators functions (for deserialization of responses)
     * @return array
     */
    public static function validators() {
        return [
            'owner_tenant_id' => 'validateOwnerTenantId',
            'name' => 'validateName'
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
        if (!empty($this->validateOwnerTenantId())) {
            $invalid_properties[] = $this->validateOwnerTenantId();
        }
        if (!empty($this->validateName())) {
            $invalid_properties[] = $this->validateName();
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
        if (!empty($this->validateOwnerTenantId())) {
            return false;
        }
        if (!empty($this->validateName())) {
            return false;
        }
        return true;
    }


    /**
     * Gets owner_tenant_id
     * @return string
     */
    public function getOwnerTenantId()
    {
        return $this->offsetGet('owner_tenant_id');
    }

    /**
     * Checks owner_tenant_id
     * @return boolean
     */
    public function hasOwnerTenantId()
    {
        return $this->offsetExists('owner_tenant_id');
    }

    /**
     * Sets owner_tenant_id
     * @param string $owner_tenant_id
     * @return $this
     */
    public function setOwnerTenantId($owner_tenant_id)
    {
        if ((strlen($owner_tenant_id) > 36)) {
            throw new \InvalidArgumentException('invalid length for $owner_tenant_id when calling LocationPost., must be smaller than or equal to 36.');
        }
        if ((strlen($owner_tenant_id) < 36)) {
            throw new \InvalidArgumentException('invalid length for $owner_tenant_id when calling LocationPost., must be bigger than or equal to 36.');
        }
        if ((!preg_match("/[0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12}/", $owner_tenant_id))) {
            throw new \InvalidArgumentException("invalid value for $owner_tenant_id when calling LocationPost., must conform to the pattern /[0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12}/.");
        }

        $this->offsetSet('owner_tenant_id', $owner_tenant_id);

        return $this;
    }

    /**
     * Unset owner_tenant_id
     */
    public function unsetOwnerTenantId() {
        $this->offsetUnset('owner_tenant_id');
    }

    /**
     * Valid owner_tenant_id
     * @return array|boolean
     */
    public function validateOwnerTenantId()
    {
        if (!$this->offsetExists('owner_tenant_id')) {
            return "Property 'owner_tenant_id' was not initialized.";
        }
            if ($this->offsetExists('owner_tenant_id') && strlen($this->offsetGet('owner_tenant_id')) > 36) {
                return "invalid value for 'owner_tenant_id', the character length must be smaller than or equal to 36.";
            }
            if ($this->offsetExists('owner_tenant_id') && strlen($this->offsetGet('owner_tenant_id')) < 36) {
                return "invalid value for 'owner_tenant_id', the character length must be bigger than or equal to 36.";
            }
            if ($this->offsetExists('owner_tenant_id') && !preg_match("/[0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12}/", $this->offsetGet('owner_tenant_id'))) {
                return "invalid value for 'owner_tenant_id', must be conform to the pattern /[0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12}/.";
            }
        return false;
    }


    /**
     * Gets name
     * @return string
     */
    public function getName()
    {
        return $this->offsetGet('name');
    }

    /**
     * Checks name
     * @return boolean
     */
    public function hasName()
    {
        return $this->offsetExists('name');
    }

    /**
     * Sets name
     * @param string $name Human-readable name of infrastructure location that will be displayed to the users
     * @return $this
     */
    public function setName($name)
    {
        $this->offsetSet('name', $name);

        return $this;
    }

    /**
     * Unset name
     */
    public function unsetName() {
        $this->offsetUnset('name');
    }

    /**
     * Valid name
     * @return array|boolean
     */
    public function validateName()
    {
        if (!$this->offsetExists('name')) {
            return "Property 'name' was not initialized.";
        }
        return false;
    }

}


