<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Repository\WHMCS;

use AcronisCloud\Model\WHMCS\CustomFieldsValues;
use AcronisCloud\Repository\AbstractRepository;
use Illuminate\Database\Eloquent\Model;

class CustomFieldsValuesRepository extends AbstractRepository
{
    const COLUMN_FIELDID = 'fieldid';
    const COLUMN_RELID = 'relid';
    const COLUMN_VALUE = 'value';

    /**
     * @param int $fieldId
     * @param int $serviceId
     * @param mixed $value
     * @return bool
     */
    public function saveCustomFieldServiceValue($fieldId, $serviceId, $value)
    {
        /** @var Model $record */
        $record = CustomFieldsValues::firstOrNew([
            CustomFieldsValues::COLUMN_FIELDID => $fieldId,
            CustomFieldsValues::COLUMN_RELID => $serviceId,
        ]);
        $record->value = $value;

        return $record->save();
    }

    /**
     * @param int $fieldId
     * @param int $serviceId
     * @return CustomFieldsValues
     */
    public function getCustomFieldServiceValue($fieldId, $serviceId)
    {
        return CustomFieldsValues::where(CustomFieldsValues::COLUMN_FIELDID, $fieldId)
            ->where(CustomFieldsValues::COLUMN_RELID, $serviceId)
            ->first();
    }

    /**
     * @param int $fieldId
     * @param int $serviceId
     * @return bool
     */
    public function deleteCustomFieldServiceValue($fieldId, $serviceId)
    {
        return CustomFieldsValues::where(CustomFieldsValues::COLUMN_FIELDID, $fieldId)
            ->where(CustomFieldsValues::COLUMN_RELID, $serviceId)
            ->delete();
    }
}