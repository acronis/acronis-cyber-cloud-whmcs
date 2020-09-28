<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. All rights reserved
 */

namespace Acronis\UsageReport\Csv;

use Acronis\UsageReport\ReportRowWrapperInterface;

class ReportRowWrapper implements ReportRowWrapperInterface
{
    private $data;

    /**
     * @param object $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getRawData()
    {
        return $this->data;
    }

    public function getTenantId()
    {
        return $this->data->tenant->id;
    }

    public function getTenantKind()
    {
        return $this->data->tenant->kind;
    }

    public function getApplicationId()
    {
        return $this->data->application->id;
    }

    public function getInfraBackendType()
    {
        return $this->data->infra->backend_type;
    }

    public function getInfraId()
    {
        return $this->data->infra->id;
    }

    public function getInfraOwnerId()
    {
        return $this->data->infra->owner_id;
    }

    public function getOfferingItemName()
    {
        return $this->data->name;
    }

    public function getEdition()
    {
        return $this->data->edition;
    }

    /**
     * @inheritdoc
     * @param $usageCalculationStrategy
     * @param $usageCountingKind
     * @return int|string
     */
    public function getUsage($usageCalculationStrategy, $usageCountingKind)
    {
        return $this->data->usage->{$usageCalculationStrategy}->{$usageCountingKind};
    }
}