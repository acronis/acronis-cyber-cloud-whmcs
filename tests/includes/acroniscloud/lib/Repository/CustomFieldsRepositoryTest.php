<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Repository;

use AcronisCloud\Model\WHMCS\CustomField;
use AcronisCloud\Repository\WHMCS\CustomFieldsRepository;
use AcronisCloud\Service\Localization\TranslatorFactory;
use AcronisCloud\Service\Locator;
use AcronisCloud\Service\MetaInfo\CustomFieldMeta;
use Symfony\Component\Translation\Translator;

class CustomFieldsRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public static function setUpBeforeClass()
    {
        if (!Locator::getInstance()->has(TranslatorFactory::NAME)) {
            Locator::getInstance()->set(TranslatorFactory::NAME, new Translator('en_US'));
        }
    }

    public static function tearDownAfterClass()
    {
        Locator::getInstance()->reset(TranslatorFactory::NAME);
    }

    public function testCreateProductFieldWithRegexp()
    {
        $productId = 3;
        $regex = '[a-z]';
        $fieldMeta = new CustomFieldMeta([
            CustomFieldMeta::PROPERTY_NAME => 'name',
            CustomFieldMeta::PROPERTY_FRIENDLY_NAME => 'Test Field',
            CustomFieldMeta::PROPERTY_TYPE => CustomField::FIELD_TYPE_TEXT,
            CustomFieldMeta::PROPERTY_DESCRIPTION => 'description',
            CustomFieldMeta::PROPERTY_VALIDATION => $regex,
            CustomFieldMeta::PROPERTY_ADMIN_ONLY => CustomField::SETTING_ON,
            CustomFieldMeta::PROPERTY_REQUIRED => false,
            CustomFieldMeta::PROPERTY_SHOW_ON_ORDER => false,
            CustomFieldMeta::PROPERTY_SHOW_ON_INVOICE => true,
            CustomFieldMeta::PROPERTY_SORT_PRIORITY => 2,
        ]);
        $data = [
            CustomField::COLUMN_TYPE => CustomField::TYPE_PRODUCT,
            CustomField::COLUMN_RELID => $productId,
            CustomField::COLUMN_ADMINONLY => CustomField::SETTING_ON,
            CustomField::COLUMN_FIELDTYPE => CustomField::FIELD_TYPE_TEXT,
            CustomField::COLUMN_DESCRIPTION => 'description',
            CustomField::COLUMN_FIELDNAME => $fieldMeta->getName() . '|' . $fieldMeta->getFriendlyName(),
            CustomField::COLUMN_REGEXPR => $regex,
            CustomField::COLUMN_REQUIRED => '',
            CustomField::COLUMN_SHOWORDER=> '',
            CustomField::COLUMN_SHOWINVOICE=> 'on',
            CustomField::COLUMN_SORTORDER => 2,
        ];

        $createCustomFieldsMock = $this->getMockBuilder(CustomFieldsRepository::class)
            ->setMethods(['create'])
            ->getMock();;
        $createCustomFieldsMock->expects($this->once())
            ->method('create')
            ->with($data)
            ->will($this->returnValue(new CustomField()));

        $createCustomFieldsMock->createProductField($productId, $fieldMeta);
    }

    public function testCreateProductFieldNoRegexp()
    {
        $data = [
            CustomField::COLUMN_TYPE => CustomField::TYPE_PRODUCT,
            CustomField::COLUMN_ADMINONLY => CustomField::SETTING_ON,
            CustomField::COLUMN_FIELDTYPE => CustomField::FIELD_TYPE_TEXT,
            CustomField::COLUMN_RELID => 1,
            CustomField::COLUMN_FIELDNAME => 'name|Test Field',
            CustomField::COLUMN_DESCRIPTION => 'description',
            CustomField::COLUMN_REGEXPR => '',
            CustomField::COLUMN_REQUIRED => '',
            CustomField::COLUMN_SHOWORDER=> '',
            CustomField::COLUMN_SHOWINVOICE=> '',
            CustomField::COLUMN_SORTORDER => 2,
        ];
        $fieldMeta = new CustomFieldMeta([
            CustomFieldMeta::PROPERTY_NAME => 'name',
            CustomFieldMeta::PROPERTY_FRIENDLY_NAME => 'Test Field',
            CustomFieldMeta::PROPERTY_TYPE => CustomField::FIELD_TYPE_TEXT,
            CustomFieldMeta::PROPERTY_DESCRIPTION => 'description',
            CustomFieldMeta::PROPERTY_VALIDATION => '',
            CustomFieldMeta::PROPERTY_ADMIN_ONLY => CustomField::SETTING_ON,
            CustomFieldMeta::PROPERTY_REQUIRED => false,
            CustomFieldMeta::PROPERTY_SORT_PRIORITY => 2,
        ]);

        $createCustomFieldsMock = $this->getMockBuilder(CustomFieldsRepository::class)
            ->setMethods(['create'])
            ->getMock();;
        $createCustomFieldsMock->expects($this->once())
            ->method('create')
            ->with($data)
            ->will($this->returnValue(new CustomField()));

        $createCustomFieldsMock->createProductField(1, $fieldMeta);
    }
}
