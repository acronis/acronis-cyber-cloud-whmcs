<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Repository\WHMCS;

use AcronisCloud\Model\WHMCS\CustomField;
use AcronisCloud\Repository\AbstractRepository;
use AcronisCloud\Service\MetaInfo\CustomFieldMeta;
use Illuminate\Database\Eloquent\Model;

class CustomFieldsRepository extends AbstractRepository
{
    const NAME_SEPARATOR = '|';

    /**
     * @param array $data
     * @return CustomField
     */
    public function create(array $data)
    {
        return CustomField::create($data);
    }

    /**
     * @param int $productId
     * @param CustomFieldMeta $fieldMeta
     * @return CustomField
     */
    public function createProductField($productId, CustomFieldMeta $fieldMeta)
    {
        $customField = $this->create([
            CustomField::COLUMN_TYPE => CustomField::TYPE_PRODUCT,
            CustomField::COLUMN_RELID => $productId,
            CustomField::COLUMN_FIELDNAME => $this->formatName($fieldMeta),
            CustomField::COLUMN_FIELDTYPE => $fieldMeta->getType(),
            CustomField::COLUMN_DESCRIPTION => $fieldMeta->getDescription(),
            CustomField::COLUMN_REGEXPR => $fieldMeta->getValidation(),
            CustomField::COLUMN_ADMINONLY => $fieldMeta->isAdminOnly() ? CustomField::SETTING_ON : '',
            CustomField::COLUMN_REQUIRED => $fieldMeta->isRequired() ? CustomField::SETTING_ON : '',
            CustomField::COLUMN_SHOWORDER => $fieldMeta->isShowOnOrder() ? CustomField::SETTING_ON : '',
            CustomField::COLUMN_SHOWINVOICE => $fieldMeta->isShowOnInvoice() ? CustomField::SETTING_ON : '',
            CustomField::COLUMN_SORTORDER => $fieldMeta->getSortPriority(),
        ]);

        return $customField;
    }

    /**
     * @param int $productId
     * @param string $fieldName
     * @return Model
     */
    public function getProductField($productId, $fieldName)
    {
        return CustomField::where(CustomField::COLUMN_TYPE, CustomField::TYPE_PRODUCT)
            ->where(CustomField::COLUMN_RELID, $productId)
            ->where(CustomField::COLUMN_FIELDNAME, 'REGEXP', '^' . preg_quote($fieldName) . '(\s*' . static::NAME_SEPARATOR . '.*)?$')
            ->first();
    }

    /**
     * @param CustomFieldMeta $fieldMeta
     * @return string
     */
    protected function formatName(CustomFieldMeta $fieldMeta)
    {
        return implode(static::NAME_SEPARATOR, [$fieldMeta->getName(), $fieldMeta->getFriendlyName()]);
    }

    public function delete($id)
    {
        return CustomField::where(CustomField::COLUMN_ID, $id)
            ->delete();
    }
}