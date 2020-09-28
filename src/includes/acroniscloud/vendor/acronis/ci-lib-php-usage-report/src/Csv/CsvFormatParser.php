<?php
/**
 * @Copyright © 2002-2020 Acronis International GmbH. All rights reserved
 */

namespace Acronis\UsageReport\Csv;

use Acronis\UsageReport\Exception\EmptyRequiredColumnInRowException;
use Acronis\UsageReport\Exception\FormatParserException;
use Acronis\UsageReport\Exception\MissingColumnInRowException;
use Acronis\UsageReport\Exception\MissingColumnsException;
use Acronis\UsageReport\FormatParserInterface;
use Acronis\UsageReport\Csv\ReportRowWrapper;

class CsvFormatParser implements FormatParserInterface
{
    private $noEditionColumn = false;

    const AFFINITY_INHERITED = 'inherited';
    const AFFINITY_OWN = 'own';
    const AFFINITY_CHILD = 'child';

    const SYNTETIC_COLUMN_INFRA_AFFINITY = 'infra.affinity';

    const COLUMN_APPLICATION_ID = 'application.id';
    const COLUMN_INFRA_BACKEND_TYPE = 'infra.backend_type';
    const COLUMN_INFRA_ID = 'infra.id';
    const COLUMN_INFRA_OWNER_ID = 'infra.owner_id';
    const COLUMN_OFFERING_ITEM_NAME = 'name';
    const COLUMN_TENANT_ID = 'tenant.id';
    const COLUMN_TENANT_KIND = 'tenant.kind';
    const COLUMN_USAGE_ABSOLUTE_PRODUCTION = 'usage.absolute.production';
    const COLUMN_USAGE_ABSOLUTE_TOTAL = 'usage.absolute.total';
    const COLUMN_USAGE_ABSOLUTE_TRIAL = 'usage.absolute.trial';
    const COLUMN_USAGE_DELTA_PRODUCTION = 'usage.delta.production';
    const COLUMN_USAGE_DELTA_TOTAL = 'usage.delta.total';
    const COLUMN_USAGE_DELTA_TRIAL = 'usage.delta.trial';
    const COLUMN_USAGE_EFFECTIVE_PRODUCTION = 'usage.effective.production';
    const COLUMN_USAGE_EFFECTIVE_TOTAL = 'usage.effective.total';
    const COLUMN_USAGE_EFFECTIVE_TRIAL = 'usage.effective.trial';
    const COLUMN_EDITION = 'edition';

    const COLUMN_TYPE_NUMERIC = 'numeric';
    const COLUMN_TYPE_NULLABLE = 'nullable';
    const COLUMN_TYPE_DEFAULT = 'default';

    const EDITION_STD = 'standard';

    protected static $columnsList = [
        self::COLUMN_APPLICATION_ID => self::COLUMN_TYPE_DEFAULT,
        self::COLUMN_INFRA_BACKEND_TYPE => self::COLUMN_TYPE_NULLABLE,
        self::COLUMN_INFRA_ID => self::COLUMN_TYPE_NULLABLE,
        self::COLUMN_INFRA_OWNER_ID => self::COLUMN_TYPE_NULLABLE,
        self::COLUMN_OFFERING_ITEM_NAME => self::COLUMN_TYPE_DEFAULT,
        self::COLUMN_TENANT_ID => self::COLUMN_TYPE_DEFAULT,
        self::COLUMN_TENANT_KIND => self::COLUMN_TYPE_DEFAULT,
        self::COLUMN_USAGE_ABSOLUTE_PRODUCTION => self::COLUMN_TYPE_NUMERIC,
        self::COLUMN_USAGE_ABSOLUTE_TOTAL => self::COLUMN_TYPE_NUMERIC,
        self::COLUMN_USAGE_ABSOLUTE_TRIAL => self::COLUMN_TYPE_NUMERIC,
        self::COLUMN_USAGE_DELTA_PRODUCTION => self::COLUMN_TYPE_NUMERIC,
        self::COLUMN_USAGE_DELTA_TOTAL => self::COLUMN_TYPE_NUMERIC,
        self::COLUMN_USAGE_DELTA_TRIAL => self::COLUMN_TYPE_NUMERIC,
        self::COLUMN_USAGE_EFFECTIVE_PRODUCTION => self::COLUMN_TYPE_NUMERIC,
        self::COLUMN_USAGE_EFFECTIVE_TOTAL => self::COLUMN_TYPE_NUMERIC,
        self::COLUMN_USAGE_EFFECTIVE_TRIAL => self::COLUMN_TYPE_NUMERIC,
        self::COLUMN_EDITION => self::COLUMN_TYPE_DEFAULT,
    ];

    /**
     * This structure of array is made for better performance
     * to be able to use isset() instead of in_array()
     * @var bool[]
     */
    protected static $notEmptyColumns = [
        self::COLUMN_APPLICATION_ID => true,
        self::COLUMN_TENANT_ID => true,
        self::COLUMN_OFFERING_ITEM_NAME => true
    ];

    /** @var array|null */
    protected $columnsNestedProperties;

    /** @var string[]|null */
    private $columnNamesHashTable;

    /**
     * @param string[] $columnNames
     * @throws FormatParserException
     */
    public function setColumnNames($columnNames)
    {
        if (!in_array(static::COLUMN_EDITION, $columnNames)) {
            $this->noEditionColumn = true;
            $columnNames[] = static::COLUMN_EDITION;
        }

        $this->checkRequiredColumns($columnNames);
        $this->columnNamesHashTable = array_flip($columnNames);
    }

    /**
     * @param $row
     * @return ReportRowWrapper
     * @throws \Exception
     */
    public function parseRow($row)
    {
        if (is_null($this->columnNamesHashTable)) {
            throw new FormatParserException('Column names are not set.');
        }

        $rowObject = $this->buildRowObject($row);

        return $this->createReportRowWrapper($rowObject);
    }

    /**
     * @param $rowObject
     *
     * @return ReportRowWrapper
     */
    protected function createReportRowWrapper($rowObject)
    {
        return new ReportRowWrapper($rowObject);
    }

    /**
     * @param $columnNames
     * @throws MissingColumnsException
     */
    protected function checkRequiredColumns($columnNames)
    {
        $requiredColumns = array_keys(static::$columnsList);
        $missingColumns = array_diff($requiredColumns, $columnNames);

        if (count($missingColumns) > 0) {
            throw new MissingColumnsException($missingColumns);
        }
    }

    /**
     * Builds object from plain array,
     * @see buildNestedPropertiesForColumns
     * @param $row
     * @throws FormatParserException
     * @return object
     */
    protected function buildRowObject($row)
    {
        $result = new \stdClass();
        foreach ($this->getNestedPropertiesForColumns() as $key => $properties) {
            $value = $this->getColumnValue($row, $key);

            $currentObject = $result;
            foreach (array_slice($properties, 0, -1) as $property) {
                if (!property_exists($currentObject, $property)) {
                    $currentObject->{$property} = new \stdClass();
                }
                $currentObject = $currentObject->{$property};
            }
            $lastProperty = end($properties);
            $currentObject->{$lastProperty} = $value;
        }

        return $result;
    }

    /**
     * Builds map of column names looks like this:
     * ['tenant.id' => ['tenant', 'id'], 'infra.owner_id' => ['infra', 'owner_id'] ... ]
     * that is using to generate object from plain array of columns from csv_v2_0 format, e.g.:
     *  {'tenant' => {'id' => ''}, 'infra' => {'id' =>, 'owner_id' => } ... }
     * @return array
     */
    protected function buildNestedPropertiesForColumns()
    {
        $keys = array_keys(static::$columnsList);
        $properties = array_map(function ($column) {
            return explode('.', $column);
        }, $keys);
        return array_combine($keys, $properties);
    }

    protected function getNestedPropertiesForColumns()
    {
        if (is_null($this->columnsNestedProperties)) {
            $this->columnsNestedProperties = $this->buildNestedPropertiesForColumns();
        }

        return $this->columnsNestedProperties;
    }

    /**
     * @param string[] $row
     * @param string $columnName
     * @throws FormatParserException
     * @return string
     */
    private function getColumnValue($row, $columnName)
    {
        // todo Remove this w/a when column "edition" will be present in CSV
        if ($columnName === static::COLUMN_EDITION && $this->noEditionColumn) {
            return static::EDITION_STD;
        }

        $columnIndex = $this->getColumnIndex($columnName);

        if (!isset($row[$columnIndex])) {
            throw new MissingColumnInRowException($columnName, $row);
        }

        $value = trim($row[$columnIndex]);

        if (!$this->canBeEmpty($columnName) && $value === '') {
            throw new EmptyRequiredColumnInRowException($columnName, $row);
        }

        $columnType = $this->getColumnType($columnName);
        if ($columnType === static::COLUMN_TYPE_NULLABLE) {
            return $this->castToNullable($value);
        }

        if ($columnType === static::COLUMN_TYPE_NUMERIC) {
            return $this->castToNumeric($value);
        }

        return $value;
    }

    private function castToNullable($value)
    {
        return $value === '' ? null : $value;
    }

    /**
     * @param $value
     * @return float
     * @throws FormatParserException
     */
    private function castToNumeric($value)
    {
        if (!is_numeric($value)) {
            throw new FormatParserException(\sprintf(
                'Given value "%s" is not numeric as expected.',
                $value
            ));
        }

        return doubleval($value);
    }

    private function getColumnType($columnName)
    {
        return static::$columnsList[$columnName];
    }

    private function canBeEmpty($columnName)
    {
        return isset(static::$notEmptyColumns[$columnName]) ? !static::$notEmptyColumns[$columnName] : true;
    }

    /**
     * @param string $columnName
     * @return string
     * @throws FormatParserException
     */
    private function getColumnIndex($columnName)
    {
        if (is_null($this->columnNamesHashTable)) {
            throw new FormatParserException('Column names are not set.');
        }

        return $this->columnNamesHashTable[$columnName];
    }
}