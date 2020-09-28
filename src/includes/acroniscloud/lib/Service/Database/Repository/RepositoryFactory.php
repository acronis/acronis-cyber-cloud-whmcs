<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Database\Repository;

use AcronisCloud\Repository\ReportRepository;
use AcronisCloud\Repository\ReportStorageRepository;
use AcronisCloud\Repository\TemplateRepository;
use AcronisCloud\Repository\WHMCS\AcronisServerRepository;
use AcronisCloud\Repository\WHMCS\AddonModuleRepository;
use AcronisCloud\Repository\WHMCS\ConfigurationRepository;
use AcronisCloud\Repository\WHMCS\CurrencyRepository;
use AcronisCloud\Repository\WHMCS\CustomFieldsRepository;
use AcronisCloud\Repository\WHMCS\CustomFieldsValuesRepository;
use AcronisCloud\Repository\WHMCS\ProductConfigGroupRepository;
use AcronisCloud\Repository\WHMCS\ProductRepository;
use AcronisCloud\Repository\WHMCS\ServiceRepository;
use AcronisCloud\Repository\WHMCS\UpgradeRepository;
use AcronisCloud\Service\FactoryInterface;

class RepositoryFactory implements FactoryInterface
{
    const NAME = 'repository';

    /**
     * @return RepositoryFactory
     */
    public function createInstance()
    {
        return new static();
    }

    /**
     * @return TemplateRepository
     */
    public function getTemplateRepository()
    {
        return new TemplateRepository();
    }

    /**
     * @return AcronisServerRepository
     */
    public function getAcronisServerRepository()
    {
        return new AcronisServerRepository();
    }

    /**
     * @return AddonModuleRepository
     */
    public function getAddonModuleRepository()
    {
        return new AddonModuleRepository();
    }

    /**
     * @return ProductRepository
     */
    public function getProductRepository()
    {
        return new ProductRepository();
    }

    /**
     * @return ProductConfigGroupRepository
     */
    public function getProductConfigGroupRepository()
    {
        return new ProductConfigGroupRepository();
    }

    /**
     * @return ConfigurationRepository
     */
    public function getConfigurationRepository()
    {
        return new ConfigurationRepository();
    }

    /**
     * @return CurrencyRepository
     */
    public function getCurrencyRepository()
    {
        return new CurrencyRepository();
    }

    /**
     * @return CustomFieldsRepository
     */
    public function getCustomFieldsRepository()
    {
        return new CustomFieldsRepository();
    }

    /**
     * @return CustomFieldsValuesRepository
     */
    public function getCustomFieldsValuesRepository()
    {
        return new CustomFieldsValuesRepository();
    }

    /**
     * @return ServiceRepository
     */
    public function getServiceRepository()
    {
        return new ServiceRepository();
    }

    /**
     * @return UpgradeRepository
     */
    public function getUpgradeRepository()
    {
        return new UpgradeRepository();
    }

    /**
     * @return ReportRepository
     */
    public function getUsageReportRepository()
    {
        return new ReportRepository();
    }

    /**
     * @return ReportStorageRepository
     */
    public function getReportStorageRepository()
    {
        return new ReportStorageRepository();
    }
}