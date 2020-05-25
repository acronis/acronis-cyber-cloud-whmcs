<?php
/**
 * MfaStatus
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

namespace Acronis\Cloud\Client\Model\Tenants;

use Acronis\Cloud\Client\BaseModel;
/**
 * MfaStatus Class Doc Comment
 *
 * @category    Class
 * @package     Acronis\Cloud\Client
 * @author      Swagger Codegen team
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class MfaStatus extends BaseModel 
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      * @var string
      */
    protected static $swaggerModelName = 'Tenants\MfaStatus';

    /**
     * Array of property to type mappings. Used for (de)serialization
     * @return array
     */
    public static function swaggerTypes()
    {
        return [
            'mfa_status' => 'string',
            'users' => 'int',
            'users_with_totp_enabled' => 'int',
            'update_allowed' => 'bool'
        ];
    }


    /**
     * Array of property to format mappings. Used for (de)serialization
     * @return array
     */
    public static function swaggerFormats()
    {
        return [
            'mfa_status' => null,
            'users' => 'int32',
            'users_with_totp_enabled' => 'int32',
            'update_allowed' => null
        ];
    }

    /**
     * Array of attributes where the key is the local name, and the value is the original name
     * @return array
     */
    public static function attributeMap()
    {
        return [
            'mfa_status' => 'mfa_status',
            'users' => 'users',
            'users_with_totp_enabled' => 'users_with_totp_enabled',
            'update_allowed' => 'update_allowed'
        ];
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     * @return array
     */
    public static function setters()
    {
        return [
            'mfa_status' => 'setMfaStatus',
            'users' => 'setUsers',
            'users_with_totp_enabled' => 'setUsersWithTotpEnabled',
            'update_allowed' => 'setUpdateAllowed'
        ];
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     * @return array
     */
    public static function getters()
    {
        return [
            'mfa_status' => 'getMfaStatus',
            'users' => 'getUsers',
            'users_with_totp_enabled' => 'getUsersWithTotpEnabled',
            'update_allowed' => 'getUpdateAllowed'
        ];
    }

    /**
     * Array of attributes to checkers functions (for deserialization of responses)
     * @return array
     */
    public static function checkers()
    {
        return [
            'mfa_status' => 'hasMfaStatus',
            'users' => 'hasUsers',
            'users_with_totp_enabled' => 'hasUsersWithTotpEnabled',
            'update_allowed' => 'hasUpdateAllowed'
        ];
    }

    /**
     * Array of attributes to validators functions (for deserialization of responses)
     * @return array
     */
    public static function validators() {
        return [
            'mfa_status' => 'validateMfaStatus',
            'users' => 'validateUsers',
            'users_with_totp_enabled' => 'validateUsersWithTotpEnabled',
            'update_allowed' => 'validateUpdateAllowed'
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
        if (!empty($this->validateMfaStatus())) {
            $invalid_properties[] = $this->validateMfaStatus();
        }
        if (!empty($this->validateUsers())) {
            $invalid_properties[] = $this->validateUsers();
        }
        if (!empty($this->validateUsersWithTotpEnabled())) {
            $invalid_properties[] = $this->validateUsersWithTotpEnabled();
        }
        if (!empty($this->validateUpdateAllowed())) {
            $invalid_properties[] = $this->validateUpdateAllowed();
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
        if (!empty($this->validateMfaStatus())) {
            return false;
        }
        if (!empty($this->validateUsers())) {
            return false;
        }
        if (!empty($this->validateUsersWithTotpEnabled())) {
            return false;
        }
        if (!empty($this->validateUpdateAllowed())) {
            return false;
        }
        return true;
    }


    /**
     * Gets mfa_status
     * @return string
     */
    public function getMfaStatus()
    {
        return $this->offsetGet('mfa_status');
    }

    /**
     * Checks mfa_status
     * @return boolean
     */
    public function hasMfaStatus()
    {
        return $this->offsetExists('mfa_status');
    }

    /**
     * Sets mfa_status
     * @param string $mfa_status Status of MFA for tenant: 'disabled', 'disabled-in-progress', 'enabled'
     * @return $this
     */
    public function setMfaStatus($mfa_status)
    {
        $this->offsetSet('mfa_status', $mfa_status);

        return $this;
    }

    /**
     * Unset mfa_status
     */
    public function unsetMfaStatus() {
        $this->offsetUnset('mfa_status');
    }

    /**
     * Valid mfa_status
     * @return array|boolean
     */
    public function validateMfaStatus()
    {
        if (!$this->offsetExists('mfa_status')) {
            return "Property 'mfa_status' was not initialized.";
        }
        return false;
    }


    /**
     * Gets users
     * @return int
     */
    public function getUsers()
    {
        return $this->offsetGet('users');
    }

    /**
     * Checks users
     * @return boolean
     */
    public function hasUsers()
    {
        return $this->offsetExists('users');
    }

    /**
     * Sets users
     * @param int $users Total number of active users in this tenant (including child folders or units of the same organization)
     * @return $this
     */
    public function setUsers($users)
    {
        $this->offsetSet('users', $users);

        return $this;
    }

    /**
     * Unset users
     */
    public function unsetUsers() {
        $this->offsetUnset('users');
    }

    /**
     * Valid users
     * @return array|boolean
     */
    public function validateUsers()
    {
        if (!$this->offsetExists('users')) {
            return false;
        }
        return false;
    }


    /**
     * Gets users_with_totp_enabled
     * @return int
     */
    public function getUsersWithTotpEnabled()
    {
        return $this->offsetGet('users_with_totp_enabled');
    }

    /**
     * Checks users_with_totp_enabled
     * @return boolean
     */
    public function hasUsersWithTotpEnabled()
    {
        return $this->offsetExists('users_with_totp_enabled');
    }

    /**
     * Sets users_with_totp_enabled
     * @param int $users_with_totp_enabled Number of users in this tenant (including child folders or units of the same organization) with TOTP configured
     * @return $this
     */
    public function setUsersWithTotpEnabled($users_with_totp_enabled)
    {
        $this->offsetSet('users_with_totp_enabled', $users_with_totp_enabled);

        return $this;
    }

    /**
     * Unset users_with_totp_enabled
     */
    public function unsetUsersWithTotpEnabled() {
        $this->offsetUnset('users_with_totp_enabled');
    }

    /**
     * Valid users_with_totp_enabled
     * @return array|boolean
     */
    public function validateUsersWithTotpEnabled()
    {
        if (!$this->offsetExists('users_with_totp_enabled')) {
            return false;
        }
        return false;
    }


    /**
     * Gets update_allowed
     * @return bool
     */
    public function getUpdateAllowed()
    {
        return $this->offsetGet('update_allowed');
    }

    /**
     * Checks update_allowed
     * @return boolean
     */
    public function hasUpdateAllowed()
    {
        return $this->offsetExists('update_allowed');
    }

    /**
     * Sets update_allowed
     * @param bool $update_allowed true if logged in user can change MFA status for this tenant
     * @return $this
     */
    public function setUpdateAllowed($update_allowed)
    {
        $this->offsetSet('update_allowed', $update_allowed);

        return $this;
    }

    /**
     * Unset update_allowed
     */
    public function unsetUpdateAllowed() {
        $this->offsetUnset('update_allowed');
    }

    /**
     * Valid update_allowed
     * @return array|boolean
     */
    public function validateUpdateAllowed()
    {
        if (!$this->offsetExists('update_allowed')) {
            return "Property 'update_allowed' was not initialized.";
        }
        return false;
    }

}

