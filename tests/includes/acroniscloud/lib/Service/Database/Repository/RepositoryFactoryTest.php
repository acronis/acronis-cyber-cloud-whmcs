<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Database\Repository;

use AcronisCloud\Repository\TemplateRepository;
use AcronisCloud\Repository\WHMCS\AcronisServerRepository;
use AcronisCloud\Repository\WHMCS\ConfigurationRepository;
use AcronisCloud\Repository\WHMCS\CustomFieldsRepository;
use AcronisCloud\Repository\WHMCS\CustomFieldsValuesRepository;
use AcronisCloud\Repository\WHMCS\ProductRepository;

class RepositoryFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetCustomFieldsValuesRepository()
    {
        $factory = new RepositoryFactory();

        $this->assertInstanceOf(CustomFieldsValuesRepository::class, $factory->getCustomFieldsValuesRepository());
    }

    public function testGetCustomFieldsRepository()
    {
        $factory = new RepositoryFactory();

        $this->assertInstanceOf(CustomFieldsRepository::class, $factory->getCustomFieldsRepository());
    }

    public function testGetConfigurationRepository()
    {
        $factory = new RepositoryFactory();

        $this->assertInstanceOf(ConfigurationRepository::class, $factory->getConfigurationRepository());
    }

    public function testGetTemplateRepository()
    {
        $factory = new RepositoryFactory();

        $this->assertInstanceOf(TemplateRepository::class, $factory->getTemplateRepository());
    }

    public function testGetAcronisServerRepository()
    {
        $factory = new RepositoryFactory();

        $this->assertInstanceOf(AcronisServerRepository::class, $factory->getAcronisServerRepository());
    }

    public function testGetProductRepository()
    {
        $factory = new RepositoryFactory();

        $this->assertInstanceOf(ProductRepository::class, $factory->getProductRepository());
    }

    public function testCreateInstance()
    {
        $factory = new RepositoryFactory();

        $this->assertInstanceOf(RepositoryFactory::class, $factory->CreateInstance());
    }
}
