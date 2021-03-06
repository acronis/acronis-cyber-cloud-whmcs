<?php
/**
 * OfferingItemOutput
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

namespace Acronis\Cloud\Client\Model\OfferingItems;

use Acronis\Cloud\Client\BaseModel;
/**
 * OfferingItemOutput Class Doc Comment
 *
 * @category    Class
 * @package     Acronis\Cloud\Client
 * @author      Swagger Codegen team
 * @link        https://github.com/swagger-api/swagger-codegen
 */
class OfferingItemOutput extends BaseModel 
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      * @var string
      */
    protected static $swaggerModelName = 'OfferingItems\OfferingItemOutput';

    /**
     * Array of property to type mappings. Used for (de)serialization
     * @return array
     */
    public static function swaggerTypes()
    {
        return [
            'application_id' => 'string',
            'name' => 'string',
            'edition' => 'string',
            'usage_name' => 'string',
            'status' => 'int',
            'locked' => 'bool',
            'quota' => '\Acronis\Cloud\Client\Model\OfferingItems\Quota',
            'type' => 'string',
            'infra_id' => 'string',
            'measurement_unit' => 'string'
        ];
    }


    /**
     * Array of property to format mappings. Used for (de)serialization
     * @return array
     */
    public static function swaggerFormats()
    {
        return [
            'application_id' => null,
            'name' => null,
            'edition' => null,
            'usage_name' => null,
            'status' => 'int32',
            'locked' => null,
            'quota' => null,
            'type' => null,
            'infra_id' => null,
            'measurement_unit' => null
        ];
    }

    /**
     * Array of attributes where the key is the local name, and the value is the original name
     * @return array
     */
    public static function attributeMap()
    {
        return [
            'application_id' => 'application_id',
            'name' => 'name',
            'edition' => 'edition',
            'usage_name' => 'usage_name',
            'status' => 'status',
            'locked' => 'locked',
            'quota' => 'quota',
            'type' => 'type',
            'infra_id' => 'infra_id',
            'measurement_unit' => 'measurement_unit'
        ];
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     * @return array
     */
    public static function setters()
    {
        return [
            'application_id' => 'setApplicationId',
            'name' => 'setName',
            'edition' => 'setEdition',
            'usage_name' => 'setUsageName',
            'status' => 'setStatus',
            'locked' => 'setLocked',
            'quota' => 'setQuota',
            'type' => 'setType',
            'infra_id' => 'setInfraId',
            'measurement_unit' => 'setMeasurementUnit'
        ];
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     * @return array
     */
    public static function getters()
    {
        return [
            'application_id' => 'getApplicationId',
            'name' => 'getName',
            'edition' => 'getEdition',
            'usage_name' => 'getUsageName',
            'status' => 'getStatus',
            'locked' => 'getLocked',
            'quota' => 'getQuota',
            'type' => 'getType',
            'infra_id' => 'getInfraId',
            'measurement_unit' => 'getMeasurementUnit'
        ];
    }

    /**
     * Array of attributes to checkers functions (for deserialization of responses)
     * @return array
     */
    public static function checkers()
    {
        return [
            'application_id' => 'hasApplicationId',
            'name' => 'hasName',
            'edition' => 'hasEdition',
            'usage_name' => 'hasUsageName',
            'status' => 'hasStatus',
            'locked' => 'hasLocked',
            'quota' => 'hasQuota',
            'type' => 'hasType',
            'infra_id' => 'hasInfraId',
            'measurement_unit' => 'hasMeasurementUnit'
        ];
    }

    /**
     * Array of attributes to validators functions (for deserialization of responses)
     * @return array
     */
    public static function validators() {
        return [
            'application_id' => 'validateApplicationId',
            'name' => 'validateName',
            'edition' => 'validateEdition',
            'usage_name' => 'validateUsageName',
            'status' => 'validateStatus',
            'locked' => 'validateLocked',
            'quota' => 'validateQuota',
            'type' => 'validateType',
            'infra_id' => 'validateInfraId',
            'measurement_unit' => 'validateMeasurementUnit'
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
        if (!empty($this->validateApplicationId())) {
            $invalid_properties[] = $this->validateApplicationId();
        }
        if (!empty($this->validateName())) {
            $invalid_properties[] = $this->validateName();
        }
        if (!empty($this->validateEdition())) {
            $invalid_properties[] = $this->validateEdition();
        }
        if (!empty($this->validateUsageName())) {
            $invalid_properties[] = $this->validateUsageName();
        }
        if (!empty($this->validateStatus())) {
            $invalid_properties[] = $this->validateStatus();
        }
        if (!empty($this->validateLocked())) {
            $invalid_properties[] = $this->validateLocked();
        }
        if (!empty($this->validateQuota())) {
            $invalid_properties[] = $this->validateQuota();
        }
        if (!empty($this->validateType())) {
            $invalid_properties[] = $this->validateType();
        }
        if (!empty($this->validateInfraId())) {
            $invalid_properties[] = $this->validateInfraId();
        }
        if (!empty($this->validateMeasurementUnit())) {
            $invalid_properties[] = $this->validateMeasurementUnit();
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
        if (!empty($this->validateApplicationId())) {
            return false;
        }
        if (!empty($this->validateName())) {
            return false;
        }
        if (!empty($this->validateEdition())) {
            return false;
        }
        if (!empty($this->validateUsageName())) {
            return false;
        }
        if (!empty($this->validateStatus())) {
            return false;
        }
        if (!empty($this->validateLocked())) {
            return false;
        }
        if (!empty($this->validateQuota())) {
            return false;
        }
        if (!empty($this->validateType())) {
            return false;
        }
        if (!empty($this->validateInfraId())) {
            return false;
        }
        if (!empty($this->validateMeasurementUnit())) {
            return false;
        }
        return true;
    }


    /**
     * Gets application_id
     * @return string
     */
    public function getApplicationId()
    {
        return $this->offsetGet('application_id');
    }

    /**
     * Checks application_id
     * @return boolean
     */
    public function hasApplicationId()
    {
        return $this->offsetExists('application_id');
    }

    /**
     * Sets application_id
     * @param string $application_id
     * @return $this
     */
    public function setApplicationId($application_id)
    {
        if ((strlen($application_id) > 36)) {
            throw new \InvalidArgumentException('invalid length for $application_id when calling OfferingItemOutput., must be smaller than or equal to 36.');
        }
        if ((strlen($application_id) < 36)) {
            throw new \InvalidArgumentException('invalid length for $application_id when calling OfferingItemOutput., must be bigger than or equal to 36.');
        }
        if ((!preg_match("/[0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12}/", $application_id))) {
            throw new \InvalidArgumentException("invalid value for $application_id when calling OfferingItemOutput., must conform to the pattern /[0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12}/.");
        }

        $this->offsetSet('application_id', $application_id);

        return $this;
    }

    /**
     * Unset application_id
     */
    public function unsetApplicationId() {
        $this->offsetUnset('application_id');
    }

    /**
     * Valid application_id
     * @return array|boolean
     */
    public function validateApplicationId()
    {
        if (!$this->offsetExists('application_id')) {
            return "Property 'application_id' was not initialized.";
        }
            if ($this->offsetExists('application_id') && strlen($this->offsetGet('application_id')) > 36) {
                return "invalid value for 'application_id', the character length must be smaller than or equal to 36.";
            }
            if ($this->offsetExists('application_id') && strlen($this->offsetGet('application_id')) < 36) {
                return "invalid value for 'application_id', the character length must be bigger than or equal to 36.";
            }
            if ($this->offsetExists('application_id') && !preg_match("/[0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12}/", $this->offsetGet('application_id'))) {
                return "invalid value for 'application_id', must be conform to the pattern /[0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12}/.";
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
     * @param string $name
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


    /**
     * Gets edition
     * @return string
     */
    public function getEdition()
    {
        return $this->offsetGet('edition');
    }

    /**
     * Checks edition
     * @return boolean
     */
    public function hasEdition()
    {
        return $this->offsetExists('edition');
    }

    /**
     * Sets edition
     * @param string $edition
     * @return $this
     */
    public function setEdition($edition)
    {
        $this->offsetSet('edition', $edition);

        return $this;
    }

    /**
     * Unset edition
     */
    public function unsetEdition() {
        $this->offsetUnset('edition');
    }

    /**
     * Valid edition
     * @return array|boolean
     */
    public function validateEdition()
    {
        if (!$this->offsetExists('edition')) {
            return "Property 'edition' was not initialized.";
        }
        return false;
    }


    /**
     * Gets usage_name
     * @return string
     */
    public function getUsageName()
    {
        return $this->offsetGet('usage_name');
    }

    /**
     * Checks usage_name
     * @return boolean
     */
    public function hasUsageName()
    {
        return $this->offsetExists('usage_name');
    }

    /**
     * Sets usage_name
     * @param string $usage_name
     * @return $this
     */
    public function setUsageName($usage_name)
    {
        $this->offsetSet('usage_name', $usage_name);

        return $this;
    }

    /**
     * Unset usage_name
     */
    public function unsetUsageName() {
        $this->offsetUnset('usage_name');
    }

    /**
     * Valid usage_name
     * @return array|boolean
     */
    public function validateUsageName()
    {
        if (!$this->offsetExists('usage_name')) {
            return "Property 'usage_name' was not initialized.";
        }
        return false;
    }


    /**
     * Gets status
     * @return int
     */
    public function getStatus()
    {
        return $this->offsetGet('status');
    }

    /**
     * Checks status
     * @return boolean
     */
    public function hasStatus()
    {
        return $this->offsetExists('status');
    }

    /**
     * Sets status
     * @param int $status Status of offering item: 1 - item turned on, 0 - off
     * @return $this
     */
    public function setStatus($status)
    {
        $this->offsetSet('status', $status);

        return $this;
    }

    /**
     * Unset status
     */
    public function unsetStatus() {
        $this->offsetUnset('status');
    }

    /**
     * Valid status
     * @return array|boolean
     */
    public function validateStatus()
    {
        if (!$this->offsetExists('status')) {
            return "Property 'status' was not initialized.";
        }
        return false;
    }


    /**
     * Gets locked
     * @return bool
     */
    public function getLocked()
    {
        return $this->offsetGet('locked');
    }

    /**
     * Checks locked
     * @return boolean
     */
    public function hasLocked()
    {
        return $this->offsetExists('locked');
    }

    /**
     * Sets locked
     * @param bool $locked Flag, if 'true' this item status can not be changed
     * @return $this
     */
    public function setLocked($locked)
    {
        $this->offsetSet('locked', $locked);

        return $this;
    }

    /**
     * Unset locked
     */
    public function unsetLocked() {
        $this->offsetUnset('locked');
    }

    /**
     * Valid locked
     * @return array|boolean
     */
    public function validateLocked()
    {
        if (!$this->offsetExists('locked')) {
            return false;
        }
        return false;
    }


    /**
     * Gets quota
     * @return \Acronis\Cloud\Client\Model\OfferingItems\Quota
     */
    public function getQuota()
    {
        return $this->offsetGet('quota');
    }

    /**
     * Checks quota
     * @return boolean
     */
    public function hasQuota()
    {
        return $this->offsetExists('quota');
    }

    /**
     * Sets quota
     * @param \Acronis\Cloud\Client\Model\OfferingItems\Quota $quota
     * @return $this
     */
    public function setQuota($quota)
    {
        $this->offsetSet('quota', $quota);

        return $this;
    }

    /**
     * Unset quota
     */
    public function unsetQuota() {
        $this->offsetUnset('quota');
    }

    /**
     * Valid quota
     * @return array|boolean
     */
    public function validateQuota()
    {
        if (!$this->offsetExists('quota')) {
            return false;
        }
        return false;
    }


    /**
     * Gets type
     * @return string
     */
    public function getType()
    {
        return $this->offsetGet('type');
    }

    /**
     * Checks type
     * @return boolean
     */
    public function hasType()
    {
        return $this->offsetExists('type');
    }

    /**
     * Sets type
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->offsetSet('type', $type);

        return $this;
    }

    /**
     * Unset type
     */
    public function unsetType() {
        $this->offsetUnset('type');
    }

    /**
     * Valid type
     * @return array|boolean
     */
    public function validateType()
    {
        if (!$this->offsetExists('type')) {
            return "Property 'type' was not initialized.";
        }
        return false;
    }


    /**
     * Gets infra_id
     * @return string
     */
    public function getInfraId()
    {
        return $this->offsetGet('infra_id');
    }

    /**
     * Checks infra_id
     * @return boolean
     */
    public function hasInfraId()
    {
        return $this->offsetExists('infra_id');
    }

    /**
     * Sets infra_id
     * @param string $infra_id
     * @return $this
     */
    public function setInfraId($infra_id)
    {
        if (!is_null($infra_id) && (strlen($infra_id) > 36)) {
            throw new \InvalidArgumentException('invalid length for $infra_id when calling OfferingItemOutput., must be smaller than or equal to 36.');
        }
        if (!is_null($infra_id) && (strlen($infra_id) < 36)) {
            throw new \InvalidArgumentException('invalid length for $infra_id when calling OfferingItemOutput., must be bigger than or equal to 36.');
        }
        if (!is_null($infra_id) && (!preg_match("/[0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12}/", $infra_id))) {
            throw new \InvalidArgumentException("invalid value for $infra_id when calling OfferingItemOutput., must conform to the pattern /[0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12}/.");
        }

        $this->offsetSet('infra_id', $infra_id);

        return $this;
    }

    /**
     * Unset infra_id
     */
    public function unsetInfraId() {
        $this->offsetUnset('infra_id');
    }

    /**
     * Valid infra_id
     * @return array|boolean
     */
    public function validateInfraId()
    {
        if (!$this->offsetExists('infra_id')) {
            return false;
        }
            if ($this->offsetExists('infra_id') && strlen($this->offsetGet('infra_id')) > 36) {
                return "invalid value for 'infra_id', the character length must be smaller than or equal to 36.";
            }
            if ($this->offsetExists('infra_id') && strlen($this->offsetGet('infra_id')) < 36) {
                return "invalid value for 'infra_id', the character length must be bigger than or equal to 36.";
            }
            if ($this->offsetExists('infra_id') && !preg_match("/[0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12}/", $this->offsetGet('infra_id'))) {
                return "invalid value for 'infra_id', must be conform to the pattern /[0-9a-f]{8}-([0-9a-f]{4}-){3}[0-9a-f]{12}/.";
            }
        return false;
    }


    /**
     * Gets measurement_unit
     * @return string
     */
    public function getMeasurementUnit()
    {
        return $this->offsetGet('measurement_unit');
    }

    /**
     * Checks measurement_unit
     * @return boolean
     */
    public function hasMeasurementUnit()
    {
        return $this->offsetExists('measurement_unit');
    }

    /**
     * Sets measurement_unit
     * @param string $measurement_unit Measurement unit in which offering item's usages are kept (e.g.: 'bytes', 'quantity', 'seconds', 'n/a')
     * @return $this
     */
    public function setMeasurementUnit($measurement_unit)
    {
        $this->offsetSet('measurement_unit', $measurement_unit);

        return $this;
    }

    /**
     * Unset measurement_unit
     */
    public function unsetMeasurementUnit() {
        $this->offsetUnset('measurement_unit');
    }

    /**
     * Valid measurement_unit
     * @return array|boolean
     */
    public function validateMeasurementUnit()
    {
        if (!$this->offsetExists('measurement_unit')) {
            return "Property 'measurement_unit' was not initialized.";
        }
        return false;
    }

}


