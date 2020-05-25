<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Repository\WHMCS;

use AcronisCloud\Model\WHMCS\ProductConfigGroup;
use AcronisCloud\Model\WHMCS\ProductConfigOption;
use AcronisCloud\Model\WHMCS\ProductConfigSubOption;
use AcronisCloud\Repository\AbstractRepository;
use AcronisCloud\Util\Arr;
use WHMCS\Database\Capsule;

class ProductConfigGroupRepository extends AbstractRepository
{
    /**
     * @param string $name
     * @param string $description
     * @param array $options
     * @return ProductConfigGroup
     */
    public function createGroupWithOptions($name, $description = '', array $options = [])
    {
        return Capsule::transaction(function () use ($name, $description, $options) {
            $group = $this->createGroup($name, $description);
            if ($options) {
                $this->createGroupOptions($group, $options);
            }

            return $group;
        });
    }

    /**
     * @param string $name
     * @param string $description
     * @return ProductConfigGroup
     */
    private function createGroup($name, $description = '')
    {
        $productGroup = ProductConfigGroup::create([
            ProductConfigGroup::COLUMN_NAME => $name,
            ProductConfigGroup::COLUMN_DESCRIPTION => $description,
        ]);
        $this->enforceInsertId($productGroup);

        return $productGroup;
    }

    /**
     * @param ProductConfigGroup $group
     * @param array $options
     * @return ProductConfigOption[]
     */
    private function createGroupOptions(ProductConfigGroup $group, array $options)
    {
        // reset array indexes to not mix up sub-options
        $options = array_values($options);

        $group->options()->createMany(array_map(
            function ($option) {
                return [
                    ProductConfigOption::COLUMN_OPTION_NAME =>
                        Arr::get($option, ProductConfigOption::COLUMN_OPTION_NAME),
                    ProductConfigOption::COLUMN_OPTION_TYPE =>
                        Arr::get($option, ProductConfigOption::COLUMN_OPTION_TYPE),
                    ProductConfigOption::COLUMN_ORDER =>
                        Arr::get($option, ProductConfigOption::COLUMN_ORDER, 0),
                    ProductConfigOption::COLUMN_QTY_MINIMUM =>
                        Arr::get($option, ProductConfigOption::COLUMN_QTY_MINIMUM, 0),
                    ProductConfigOption::COLUMN_QTY_MAXIMUM =>
                        Arr::get($option, ProductConfigOption::COLUMN_QTY_MAXIMUM, 0),
                    ProductConfigOption::COLUMN_HIDDEN =>
                        Arr::get($option, ProductConfigOption::COLUMN_HIDDEN, 0),
                ];
            },
            $options
        ));

        // eager load because of insert index pollution
        $optionsModels = $group->options()->getEager()->all();
        foreach ($optionsModels as $index => $optionModel) {
            $subOptions = Arr::get($options[$index], ProductConfigOption::RELATION_SUB_OPTIONS);
            if (empty($subOptions)) {
                continue;
            }
            $this->createSubOptions($optionModel, $subOptions);
        }

        return $optionsModels;
    }

    /**
     * @param ProductConfigOption $option
     * @param array $subOptions
     * @return ProductConfigSubOption[]
     */
    private function createSubOptions(ProductConfigOption $option, array $subOptions)
    {
        return $option->subOptions()->createMany(array_map(
            function ($subOption) {
                return [
                    ProductConfigSubOption::COLUMN_OPTION_NAME =>
                        Arr::get($subOption, ProductConfigSubOption::COLUMN_OPTION_NAME, ''),
                    ProductConfigSubOption::COLUMN_SORT_ORDER =>
                        Arr::get($subOption, ProductConfigSubOption::COLUMN_SORT_ORDER, 0),
                    ProductConfigSubOption::COLUMN_HIDDEN =>
                        Arr::get($subOption, ProductConfigSubOption::COLUMN_HIDDEN, 0),
                ];
            },
            $subOptions
        ));
    }
}