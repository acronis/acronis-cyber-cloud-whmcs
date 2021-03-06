<?php
/**
 * UserLoginInfoLastLogin
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

namespace Acronis\Cloud\Client\Model\Users;

use Acronis\Cloud\Client\BaseModel;
/**
 * UserLoginInfoLastLogin Class Doc Comment
 *
 * @category    Class
 * @description Information about last login
 * @package     Acronis\Cloud\Client
 * @author      Swagger Codegen team
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class UserLoginInfoLastLogin extends BaseModel 
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      * @var string
      */
    protected static $swaggerModelName = 'Users\UserLoginInfoLastLogin';

    /**
     * Array of property to type mappings. Used for (de)serialization
     * @return array
     */
    public static function swaggerTypes()
    {
        return [
            'time' => 'string',
            'remote_address' => 'string',
            'failed_attempts' => 'int',
            'user_agent' => 'string',
            'idp_type' => 'string'
        ];
    }


    /**
     * Array of property to format mappings. Used for (de)serialization
     * @return array
     */
    public static function swaggerFormats()
    {
        return [
            'time' => null,
            'remote_address' => null,
            'failed_attempts' => 'int32',
            'user_agent' => null,
            'idp_type' => null
        ];
    }

    /**
     * Array of attributes where the key is the local name, and the value is the original name
     * @return array
     */
    public static function attributeMap()
    {
        return [
            'time' => 'time',
            'remote_address' => 'remote_address',
            'failed_attempts' => 'failed_attempts',
            'user_agent' => 'user_agent',
            'idp_type' => 'idp_type'
        ];
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     * @return array
     */
    public static function setters()
    {
        return [
            'time' => 'setTime',
            'remote_address' => 'setRemoteAddress',
            'failed_attempts' => 'setFailedAttempts',
            'user_agent' => 'setUserAgent',
            'idp_type' => 'setIdpType'
        ];
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     * @return array
     */
    public static function getters()
    {
        return [
            'time' => 'getTime',
            'remote_address' => 'getRemoteAddress',
            'failed_attempts' => 'getFailedAttempts',
            'user_agent' => 'getUserAgent',
            'idp_type' => 'getIdpType'
        ];
    }

    /**
     * Array of attributes to checkers functions (for deserialization of responses)
     * @return array
     */
    public static function checkers()
    {
        return [
            'time' => 'hasTime',
            'remote_address' => 'hasRemoteAddress',
            'failed_attempts' => 'hasFailedAttempts',
            'user_agent' => 'hasUserAgent',
            'idp_type' => 'hasIdpType'
        ];
    }

    /**
     * Array of attributes to validators functions (for deserialization of responses)
     * @return array
     */
    public static function validators() {
        return [
            'time' => 'validateTime',
            'remote_address' => 'validateRemoteAddress',
            'failed_attempts' => 'validateFailedAttempts',
            'user_agent' => 'validateUserAgent',
            'idp_type' => 'validateIdpType'
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
        if (!empty($this->validateTime())) {
            $invalid_properties[] = $this->validateTime();
        }
        if (!empty($this->validateRemoteAddress())) {
            $invalid_properties[] = $this->validateRemoteAddress();
        }
        if (!empty($this->validateFailedAttempts())) {
            $invalid_properties[] = $this->validateFailedAttempts();
        }
        if (!empty($this->validateUserAgent())) {
            $invalid_properties[] = $this->validateUserAgent();
        }
        if (!empty($this->validateIdpType())) {
            $invalid_properties[] = $this->validateIdpType();
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
        if (!empty($this->validateTime())) {
            return false;
        }
        if (!empty($this->validateRemoteAddress())) {
            return false;
        }
        if (!empty($this->validateFailedAttempts())) {
            return false;
        }
        if (!empty($this->validateUserAgent())) {
            return false;
        }
        if (!empty($this->validateIdpType())) {
            return false;
        }
        return true;
    }


    /**
     * Gets time
     * @return string
     */
    public function getTime()
    {
        return $this->offsetGet('time');
    }

    /**
     * Checks time
     * @return boolean
     */
    public function hasTime()
    {
        return $this->offsetExists('time');
    }

    /**
     * Sets time
     * @param string $time RFC3339 Formatted date
     * @return $this
     */
    public function setTime($time)
    {
        if (!is_null($time) && (strlen($time) > 32)) {
            throw new \InvalidArgumentException('invalid length for $time when calling UserLoginInfoLastLogin., must be smaller than or equal to 32.');
        }
        if (!is_null($time) && (strlen($time) < 19)) {
            throw new \InvalidArgumentException('invalid length for $time when calling UserLoginInfoLastLogin., must be bigger than or equal to 19.');
        }
        if (!is_null($time) && (!preg_match("/\\d{4}-\\d{2}-\\d{2}T\\d{2}:\\d{2}:\\d{2}(\\.\\d+)?([\\+\\-]\\d{2}\\:\\d{2})?/", $time))) {
            throw new \InvalidArgumentException("invalid value for $time when calling UserLoginInfoLastLogin., must conform to the pattern /\\d{4}-\\d{2}-\\d{2}T\\d{2}:\\d{2}:\\d{2}(\\.\\d+)?([\\+\\-]\\d{2}\\:\\d{2})?/.");
        }

        $this->offsetSet('time', $time);

        return $this;
    }

    /**
     * Unset time
     */
    public function unsetTime() {
        $this->offsetUnset('time');
    }

    /**
     * Valid time
     * @return array|boolean
     */
    public function validateTime()
    {
        if (!$this->offsetExists('time')) {
            return false;
        }
            if ($this->offsetExists('time') && strlen($this->offsetGet('time')) > 32) {
                return "invalid value for 'time', the character length must be smaller than or equal to 32.";
            }
            if ($this->offsetExists('time') && strlen($this->offsetGet('time')) < 19) {
                return "invalid value for 'time', the character length must be bigger than or equal to 19.";
            }
            if ($this->offsetExists('time') && !preg_match("/\\d{4}-\\d{2}-\\d{2}T\\d{2}:\\d{2}:\\d{2}(\\.\\d+)?([\\+\\-]\\d{2}\\:\\d{2})?/", $this->offsetGet('time'))) {
                return "invalid value for 'time', must be conform to the pattern /\\d{4}-\\d{2}-\\d{2}T\\d{2}:\\d{2}:\\d{2}(\\.\\d+)?([\\+\\-]\\d{2}\\:\\d{2})?/.";
            }
        return false;
    }


    /**
     * Gets remote_address
     * @return string
     */
    public function getRemoteAddress()
    {
        return $this->offsetGet('remote_address');
    }

    /**
     * Checks remote_address
     * @return boolean
     */
    public function hasRemoteAddress()
    {
        return $this->offsetExists('remote_address');
    }

    /**
     * Sets remote_address
     * @param string $remote_address
     * @return $this
     */
    public function setRemoteAddress($remote_address)
    {
        $this->offsetSet('remote_address', $remote_address);

        return $this;
    }

    /**
     * Unset remote_address
     */
    public function unsetRemoteAddress() {
        $this->offsetUnset('remote_address');
    }

    /**
     * Valid remote_address
     * @return array|boolean
     */
    public function validateRemoteAddress()
    {
        if (!$this->offsetExists('remote_address')) {
            return false;
        }
        return false;
    }


    /**
     * Gets failed_attempts
     * @return int
     */
    public function getFailedAttempts()
    {
        return $this->offsetGet('failed_attempts');
    }

    /**
     * Checks failed_attempts
     * @return boolean
     */
    public function hasFailedAttempts()
    {
        return $this->offsetExists('failed_attempts');
    }

    /**
     * Sets failed_attempts
     * @param int $failed_attempts
     * @return $this
     */
    public function setFailedAttempts($failed_attempts)
    {
        $this->offsetSet('failed_attempts', $failed_attempts);

        return $this;
    }

    /**
     * Unset failed_attempts
     */
    public function unsetFailedAttempts() {
        $this->offsetUnset('failed_attempts');
    }

    /**
     * Valid failed_attempts
     * @return array|boolean
     */
    public function validateFailedAttempts()
    {
        if (!$this->offsetExists('failed_attempts')) {
            return "Property 'failed_attempts' was not initialized.";
        }
        return false;
    }


    /**
     * Gets user_agent
     * @return string
     */
    public function getUserAgent()
    {
        return $this->offsetGet('user_agent');
    }

    /**
     * Checks user_agent
     * @return boolean
     */
    public function hasUserAgent()
    {
        return $this->offsetExists('user_agent');
    }

    /**
     * Sets user_agent
     * @param string $user_agent
     * @return $this
     */
    public function setUserAgent($user_agent)
    {
        $this->offsetSet('user_agent', $user_agent);

        return $this;
    }

    /**
     * Unset user_agent
     */
    public function unsetUserAgent() {
        $this->offsetUnset('user_agent');
    }

    /**
     * Valid user_agent
     * @return array|boolean
     */
    public function validateUserAgent()
    {
        if (!$this->offsetExists('user_agent')) {
            return false;
        }
        return false;
    }


    /**
     * Gets idp_type
     * @return string
     */
    public function getIdpType()
    {
        return $this->offsetGet('idp_type');
    }

    /**
     * Checks idp_type
     * @return boolean
     */
    public function hasIdpType()
    {
        return $this->offsetExists('idp_type');
    }

    /**
     * Sets idp_type
     * @param string $idp_type
     * @return $this
     */
    public function setIdpType($idp_type)
    {
        $this->offsetSet('idp_type', $idp_type);

        return $this;
    }

    /**
     * Unset idp_type
     */
    public function unsetIdpType() {
        $this->offsetUnset('idp_type');
    }

    /**
     * Valid idp_type
     * @return array|boolean
     */
    public function validateIdpType()
    {
        if (!$this->offsetExists('idp_type')) {
            return false;
        }
        return false;
    }

}


