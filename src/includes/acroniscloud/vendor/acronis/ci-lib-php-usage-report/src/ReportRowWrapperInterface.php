<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. All rights reserved
 */

namespace Acronis\UsageReport;

interface ReportRowWrapperInterface
{
    /**
     * @return string
     */
    public function getTenantId();

    /**
     * @return string
     */
    public function getTenantKind();

    /**
     * @return string
     */
    public function getApplicationId();

    /**
     * @return string
     */
    public function getInfraBackendType();

    /**
     * @return string
     */
    public function getInfraId();

    /**
     * @return string
     */
    public function getInfraOwnerId();

    /**
     * @return string
     */
    public function getOfferingItemName();

    /**
     * @return string
     */
    public function getEdition();

    /**
     * This method returns one of existing usage values from report row
     * Usage counting kind can be absolute|production|trial
     * Usage calculation strategy cab be absolute|effective|delta
     * @param $usageCalculationStrategy
     * @param $usageCountingKind
     * @return int
     */
    public function getUsage($usageCalculationStrategy, $usageCountingKind);

    /**
     * @return mixed
     */
    public function getRawData();
}