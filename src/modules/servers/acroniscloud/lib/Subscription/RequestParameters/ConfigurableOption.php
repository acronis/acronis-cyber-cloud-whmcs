<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace WHMCS\Module\Server\AcronisCloud\Subscription\RequestParameters;

use AcronisCloud\Util\WHMCS\ConfigurableOptionHelper as Option;

class ConfigurableOption
{
    /** @var string */
    private $name;

    /** @var int */
    private $value;

    /** @var string */
    private $offeringItemName;

    /** @var string */
    private $measurementUnit;

    /** @var string */
    private $infraId;

    /**
     * ConfigurableOption constructor.
     * @param string $name
     * @param int $value
     */
    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;

        $nameParts = Option::parseName($name);
        $this->offeringItemName = $nameParts[Option::OFFERING_ITEM_NAME];
        $this->measurementUnit = $nameParts[Option::MEASUREMENT_UNIT];
        $this->infraId = $nameParts[Option::INFRA_ID];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getOfferingItemName()
    {
        return $this->offeringItemName;
    }

    /**
     * @return string
     */
    public function getMeasurementUnit()
    {
        return $this->measurementUnit;
    }

    /**
     * @return string
     */
    public function getInfraId()
    {
        return $this->infraId;
    }
}