<?php
/**
 * DeviceCodeApproval
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

namespace Acronis\Cloud\Client\Model\Idp;

use Acronis\Cloud\Client\BaseModel;
/**
 * DeviceCodeApproval Class Doc Comment
 *
 * @category    Class
 * @package     Acronis\Cloud\Client
 * @author      Swagger Codegen team
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class DeviceCodeApproval extends BaseModel 
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      * @var string
      */
    protected static $swaggerModelName = 'Idp\DeviceCodeApproval';

    /**
     * Array of property to type mappings. Used for (de)serialization
     * @return array
     */
    public static function swaggerTypes()
    {
        return [
            'client_id' => 'string',
            'display_name' => 'string',
            'scopes' => 'string[]',
            'tenant_name' => 'string',
            'is_current_tenant' => 'bool'
        ];
    }


    /**
     * Array of property to format mappings. Used for (de)serialization
     * @return array
     */
    public static function swaggerFormats()
    {
        return [
            'client_id' => null,
            'display_name' => null,
            'scopes' => null,
            'tenant_name' => null,
            'is_current_tenant' => null
        ];
    }

    /**
     * Array of attributes where the key is the local name, and the value is the original name
     * @return array
     */
    public static function attributeMap()
    {
        return [
            'client_id' => 'client_id',
            'display_name' => 'display_name',
            'scopes' => 'scopes',
            'tenant_name' => 'tenant_name',
            'is_current_tenant' => 'is_current_tenant'
        ];
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     * @return array
     */
    public static function setters()
    {
        return [
            'client_id' => 'setClientId',
            'display_name' => 'setDisplayName',
            'scopes' => 'setScopes',
            'tenant_name' => 'setTenantName',
            'is_current_tenant' => 'setIsCurrentTenant'
        ];
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     * @return array
     */
    public static function getters()
    {
        return [
            'client_id' => 'getClientId',
            'display_name' => 'getDisplayName',
            'scopes' => 'getScopes',
            'tenant_name' => 'getTenantName',
            'is_current_tenant' => 'getIsCurrentTenant'
        ];
    }

    /**
     * Array of attributes to checkers functions (for deserialization of responses)
     * @return array
     */
    public static function checkers()
    {
        return [
            'client_id' => 'hasClientId',
            'display_name' => 'hasDisplayName',
            'scopes' => 'hasScopes',
            'tenant_name' => 'hasTenantName',
            'is_current_tenant' => 'hasIsCurrentTenant'
        ];
    }

    /**
     * Array of attributes to validators functions (for deserialization of responses)
     * @return array
     */
    public static function validators() {
        return [
            'client_id' => 'validateClientId',
            'display_name' => 'validateDisplayName',
            'scopes' => 'validateScopes',
            'tenant_name' => 'validateTenantName',
            'is_current_tenant' => 'validateIsCurrentTenant'
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
        if (!empty($this->validateClientId())) {
            $invalid_properties[] = $this->validateClientId();
        }
        if (!empty($this->validateDisplayName())) {
            $invalid_properties[] = $this->validateDisplayName();
        }
        if (!empty($this->validateScopes())) {
            $invalid_properties[] = $this->validateScopes();
        }
        if (!empty($this->validateTenantName())) {
            $invalid_properties[] = $this->validateTenantName();
        }
        if (!empty($this->validateIsCurrentTenant())) {
            $invalid_properties[] = $this->validateIsCurrentTenant();
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
        if (!empty($this->validateClientId())) {
            return false;
        }
        if (!empty($this->validateDisplayName())) {
            return false;
        }
        if (!empty($this->validateScopes())) {
            return false;
        }
        if (!empty($this->validateTenantName())) {
            return false;
        }
        if (!empty($this->validateIsCurrentTenant())) {
            return false;
        }
        return true;
    }


    /**
     * Gets client_id
     * @return string
     */
    public function getClientId()
    {
        return $this->offsetGet('client_id');
    }

    /**
     * Checks client_id
     * @return boolean
     */
    public function hasClientId()
    {
        return $this->offsetExists('client_id');
    }

    /**
     * Sets client_id
     * @param string $client_id
     * @return $this
     */
    public function setClientId($client_id)
    {
        $this->offsetSet('client_id', $client_id);

        return $this;
    }

    /**
     * Unset client_id
     */
    public function unsetClientId() {
        $this->offsetUnset('client_id');
    }

    /**
     * Valid client_id
     * @return array|boolean
     */
    public function validateClientId()
    {
        if (!$this->offsetExists('client_id')) {
            return "Property 'client_id' was not initialized.";
        }
        return false;
    }


    /**
     * Gets display_name
     * @return string
     */
    public function getDisplayName()
    {
        return $this->offsetGet('display_name');
    }

    /**
     * Checks display_name
     * @return boolean
     */
    public function hasDisplayName()
    {
        return $this->offsetExists('display_name');
    }

    /**
     * Sets display_name
     * @param string $display_name
     * @return $this
     */
    public function setDisplayName($display_name)
    {
        $this->offsetSet('display_name', $display_name);

        return $this;
    }

    /**
     * Unset display_name
     */
    public function unsetDisplayName() {
        $this->offsetUnset('display_name');
    }

    /**
     * Valid display_name
     * @return array|boolean
     */
    public function validateDisplayName()
    {
        if (!$this->offsetExists('display_name')) {
            return false;
        }
        return false;
    }


    /**
     * Gets scopes
     * @return string[]
     */
    public function getScopes()
    {
        return $this->offsetGet('scopes');
    }

    /**
     * Checks scopes
     * @return boolean
     */
    public function hasScopes()
    {
        return $this->offsetExists('scopes');
    }

    /**
     * Sets scopes
     * @param string[] $scopes
     * @return $this
     */
    public function setScopes($scopes)
    {
        $this->offsetSet('scopes', $scopes);

        return $this;
    }

    /**
     * Unset scopes
     */
    public function unsetScopes() {
        $this->offsetUnset('scopes');
    }

    /**
     * Valid scopes
     * @return array|boolean
     */
    public function validateScopes()
    {
        if (!$this->offsetExists('scopes')) {
            return false;
        }
        return false;
    }


    /**
     * Gets tenant_name
     * @return string
     */
    public function getTenantName()
    {
        return $this->offsetGet('tenant_name');
    }

    /**
     * Checks tenant_name
     * @return boolean
     */
    public function hasTenantName()
    {
        return $this->offsetExists('tenant_name');
    }

    /**
     * Sets tenant_name
     * @param string $tenant_name
     * @return $this
     */
    public function setTenantName($tenant_name)
    {
        $this->offsetSet('tenant_name', $tenant_name);

        return $this;
    }

    /**
     * Unset tenant_name
     */
    public function unsetTenantName() {
        $this->offsetUnset('tenant_name');
    }

    /**
     * Valid tenant_name
     * @return array|boolean
     */
    public function validateTenantName()
    {
        if (!$this->offsetExists('tenant_name')) {
            return false;
        }
        return false;
    }


    /**
     * Gets is_current_tenant
     * @return bool
     */
    public function getIsCurrentTenant()
    {
        return $this->offsetGet('is_current_tenant');
    }

    /**
     * Checks is_current_tenant
     * @return boolean
     */
    public function hasIsCurrentTenant()
    {
        return $this->offsetExists('is_current_tenant');
    }

    /**
     * Sets is_current_tenant
     * @param bool $is_current_tenant
     * @return $this
     */
    public function setIsCurrentTenant($is_current_tenant)
    {
        $this->offsetSet('is_current_tenant', $is_current_tenant);

        return $this;
    }

    /**
     * Unset is_current_tenant
     */
    public function unsetIsCurrentTenant() {
        $this->offsetUnset('is_current_tenant');
    }

    /**
     * Valid is_current_tenant
     * @return array|boolean
     */
    public function validateIsCurrentTenant()
    {
        if (!$this->offsetExists('is_current_tenant')) {
            return false;
        }
        return false;
    }

}


