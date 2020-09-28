<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Repository\Validation\Template;

use Acronis\Cloud\Client\Model\Applications\Application;
use Acronis\Cloud\Client\Model\Infra\Infra;
use Acronis\Cloud\Client\Model\OfferingItems\OfferingItemOutput;
use AcronisCloud\CloudApi\ApiInterface;
use AcronisCloud\Service\Localization\Translator;
use AcronisCloud\Service\Locator;
use AcronisCloud\Service\MetaInfo\MetaInfoFactory;
use AcronisCloud\Service\Localization\TranslatorFactory;

class TemplateValidatorTest extends \PHPUnit_Framework_TestCase
{
    private $templateData = null;

    public static function setUpBeforeClass()
    {
        if (!Locator::getInstance()->has(TranslatorFactory::NAME)) {
            Locator::getInstance()->set(TranslatorFactory::NAME, new Translator('en_US'));
        }
        if (!Locator::getInstance()->has(MetaInfoFactory::NAME)) {
            $factory = new MetaInfoFactory();
            Locator::getInstance()->set(MetaInfoFactory::NAME, $factory->createInstance());
        }
    }

    public static function tearDownAfterClass()
    {
        Locator::getInstance()->reset(MetaInfoFactory::NAME);
        Locator::getInstance()->reset(TranslatorFactory::NAME);
    }

    /**
     * @dataProvider templateDataProvider
     * @param $replacements
     * @param $isPassing
     * @param $expectedErrors
     */
    public function testValidation($replacements, $isPassing, $expectedErrors)
    {
        $templateData = $this->getTemplateData($replacements);
        $applications = $this->getValidApplications();
        $offeringItems = $this->getValidOfferingItems();
        $infras = $this->getValidInfras();

        $validator = new TemplateValidator($templateData, $applications, $offeringItems, $infras);

        $errors = $validator->errors()->messages();
        $errMsg = print_r($errors, true);
        $this->assertEquals($validator->passes(), $isPassing, $errMsg);
        $this->assertEquals($errors, $expectedErrors, $errMsg);
        if ($isPassing) {
            $this->assertEquals($validator->getData(), $templateData);
        }
    }

    public function templateDataProvider()
    {
        return [
            'valid data' => [[], true, []],
            'empty template name' => [
                ['name' => ''],
                false,
                ['name' => ['The name field is required.']],
            ],
            'invalid tenant kind' => [
                ['tenant_kind' => 'invalid'],
                false,
                ['tenant_kind' => ['The selected tenant kind is invalid. Valid values: partner, customer']],
            ],
            'no active application' => [
                [
                    'applications.0.status' => 'inactive',
                    'applications.0.editions.0.status' => 'inactive',
                    'applications.0.editions.1.status' => 'inactive',
                    'applications.0.editions.2.status' => 'inactive',
                    'applications.1.status' => 'inactive',
                ],
                false,
                ['applications' => [
                    'At least one application must be active. Details: {"applications":[{"type":"backup","status":"inactive","editions":[{"name":"standard","status":"inactive"},{"name":"advanced","status":"inactive"},{"name":"disaster_recovery","status":"inactive"}],"offering_items":[{"name":"universal_devices","measurement_unit":"quantity","quota_value":10,"status":"active"},{"name":"workstations","measurement_unit":"quantity","quota_value":20,"status":"inactive"},{"name":"servers","measurement_unit":"quantity","status":"inactive"},{"name":"win_server_essentials","measurement_unit":"quantity","status":"inactive"},{"name":"virtualhosts","measurement_unit":"quantity","status":"inactive"},{"name":"vms","measurement_unit":"quantity","status":"inactive"},{"name":"mobiles","measurement_unit":"quantity","status":"inactive"},{"name":"mailboxes","measurement_unit":"quantity","status":"active"},{"name":"o365_mailboxes","measurement_unit":"feature","status":"active"},{"name":"o365_onedrive","measurement_unit":"feature","status":"inactive"},{"name":"o365_sharepoint_sites","measurement_unit":"quantity","status":"inactive"},{"name":"gsuite_seats","measurement_unit":"quantity","status":"inactive"},{"name":"google_mail","measurement_unit":"feature","status":"inactive"},{"name":"google_drive","measurement_unit":"feature","status":"inactive"},{"name":"google_team_drive","measurement_unit":"feature","status":"inactive"},{"name":"web_hosting_servers","measurement_unit":"quantity","status":"inactive"},{"name":"websites","measurement_unit":"quantity","status":"inactive"},{"name":"local_storage","measurement_unit":"bytes","quota_value":6,"status":"inactive"},{"name":"child_storages","measurement_unit":"feature","status":"inactive"},{"name":"dr_child_storages","measurement_unit":"feature","status":"inactive"},{"name":"storage","measurement_unit":"bytes","infra_id":"7064f5d1-1f4d-4ace-80bd-5f7c57768389","status":"inactive"},{"name":"storage","measurement_unit":"bytes","infra_id":"7064f5d1-1f4d-4ace-80bd-5f7c57768389","status":"inactive"},{"name":"storage","measurement_unit":"bytes","infra_id":"7064f5d1-1f4d-4ace-80bd-5f7c57768389","status":"inactive"},{"name":"storage","measurement_unit":"bytes","infra_id":"ad2ce2a7-5930-49c2-a942-8bdb47b6e6c4","status":"inactive"},{"name":"dr_storage","measurement_unit":"bytes","infra_id":"ad2ce2a7-5930-49c2-a942-8bdb47b6e6c4","status":"inactive"},{"name":"compute_points","measurement_unit":"seconds","infra_id":"ad2ce2a7-5930-49c2-a942-8bdb47b6e6c4","status":"inactive"},{"name":"public_ips","measurement_unit":"quantity","infra_id":"ad2ce2a7-5930-49c2-a942-8bdb47b6e6c4","status":"inactive"},{"name":"dr_cloud_servers","measurement_unit":"quantity","infra_id":"ad2ce2a7-5930-49c2-a942-8bdb47b6e6c4","status":"inactive"},{"name":"dr_internet_access","measurement_unit":"feature","infra_id":"2ee77cf9-24b5-4802-8995-d92d6eecd0db","status":"inactive"},{"name":"adv_workstations","measurement_unit":"quantity","status":"inactive"},{"name":"adv_servers","measurement_unit":"quantity","status":"inactive"},{"name":"adv_vms","measurement_unit":"quantity","status":"inactive"},{"name":"adv_mobiles","measurement_unit":"quantity","status":"inactive"},{"name":"adv_o365_seats","measurement_unit":"quantity","status":"inactive"},{"name":"adv_o365_mailboxes","measurement_unit":"feature","status":"inactive"},{"name":"adv_o365_onedrive","measurement_unit":"feature","status":"inactive"},{"name":"adv_o365_sharepoint_sites","measurement_unit":"quantity","status":"inactive"},{"name":"adv_gsuite_seats","measurement_unit":"quantity","status":"inactive"},{"name":"adv_google_mail","measurement_unit":"feature","status":"inactive"},{"name":"adv_google_drive","measurement_unit":"feature","status":"inactive"},{"name":"adv_google_team_drive","measurement_unit":"feature","status":"inactive"},{"name":"adv_web_hosting_servers","measurement_unit":"quantity","status":"inactive"},{"name":"adv_websites","measurement_unit":"quantity","status":"inactive"},{"name":"adv_storage","measurement_unit":"bytes","infra_id":"","status":"inactive"},{"name":"adv_storage","measurement_unit":"bytes","infra_id":"6b041407-f280-41bf-bcae-6153acd95c2d","status":"inactive"},{"name":"adv_storage","measurement_unit":"bytes","infra_id":"","status":"inactive"},{"name":"adv_storage","measurement_unit":"bytes","infra_id":"6b041407-f280-41bf-bcae-6153acd95c2d","status":"inactive"},{"name":"adv_child_storages","measurement_unit":"feature","status":"inactive"},{"name":"dre_workstations","measurement_unit":"quantity","status":"inactive"},{"name":"dre_servers","measurement_unit":"quantity","status":"inactive"},{"name":"dre_vms","measurement_unit":"quantity","status":"inactive"},{"name":"dre_mobiles","measurement_unit":"quantity","status":"inactive"},{"name":"dre_o365_seats","measurement_unit":"quantity","status":"inactive"},{"name":"dre_o365_mailboxes","measurement_unit":"feature","status":"inactive"},{"name":"dre_o365_onedrive","measurement_unit":"feature","status":"inactive"},{"name":"dre_o365_sharepoint_sites","measurement_unit":"quantity","status":"inactive"},{"name":"dre_gsuite_seats","measurement_unit":"quantity","status":"inactive"},{"name":"dre_google_mail","measurement_unit":"feature","status":"inactive"},{"name":"dre_google_drive","measurement_unit":"feature","status":"inactive"},{"name":"dre_google_team_drive","measurement_unit":"feature","status":"inactive"},{"name":"dre_web_hosting_servers","measurement_unit":"quantity","status":"inactive"},{"name":"dre_websites","measurement_unit":"quantity","status":"inactive"},{"name":"dre_storage","measurement_unit":"bytes","infra_id":"","status":"inactive"},{"name":"dre_storage","measurement_unit":"bytes","infra_id":"","status":"inactive"},{"name":"dre_storage","measurement_unit":"bytes","infra_id":"","status":"inactive"},{"name":"dre_storage","measurement_unit":"bytes","infra_id":"6b041407-f280-41bf-bcae-6153acd95c2d","status":"inactive"},{"name":"dre_child_storages","measurement_unit":"feature","status":"inactive"},{"name":"dre_dr_child_storages","measurement_unit":"feature","status":"inactive"},{"name":"dre_dr_storage","measurement_unit":"bytes","infra_id":"2ee77cf9-24b5-4802-8995-d92d6eecd0db","status":"inactive"},{"name":"dre_compute_points","measurement_unit":"seconds","infra_id":"2ee77cf9-24b5-4802-8995-d92d6eecd0db","status":"inactive"},{"name":"dre_public_ips","measurement_unit":"quantity","infra_id":"2ee77cf9-24b5-4802-8995-d92d6eecd0db","status":"inactive"},{"name":"dre_dr_cloud_servers","measurement_unit":"quantity","infra_id":"2ee77cf9-24b5-4802-8995-d92d6eecd0db","status":"inactive"},{"name":"dre_dr_internet_access","measurement_unit":"feature","infra_id":"2ee77cf9-24b5-4802-8995-d92d6eecd0db","status":"inactive"}]},{"type":"files_cloud","status":"inactive","editions":[],"offering_items":[{"name":"fc_child_storages","measurement_unit":"feature","status":"inactive"},{"name":"fc_seats","measurement_unit":"quantity","status":"inactive"},{"name":"fc_storage","measurement_unit":"bytes","infra_id":"","status":"inactive"}]},{"type":"hci","status":"inactive","editions":[],"offering_items":[]},{"type":"physical_data_shipping","status":"inactive","editions":[],"offering_items":[{"name":"drives_shipped_to_cloud","measurement_unit":"quantity","status":"inactive"},{"name":"drives_shipped_from_cloud","measurement_unit":"quantity","status":"inactive"}]},{"type":"notary","status":"inactive","editions":[],"offering_items":[{"name":"notary_storage","measurement_unit":"bytes","status":"inactive"},{"name":"notarizations","measurement_unit":"quantity","status":"inactive"},{"name":"esignatures","measurement_unit":"quantity","status":"inactive"}]}]}',
                ]],
            ],
            'editions property missing' => [
                ['applications.2.editions' => null],
                false,
                ['applications' => [
                    'Application is missing properties. Required: type, status, editions and offering_items Details: {"application":{"type":"hci","status":"inactive","editions":null,"offering_items":[]}}',
                ]],
            ],
            'more than one edition for customer' => [
                ['tenant_kind' => 'customer'],
                false,
                ['applications' => [
                    'Applications can have only one edition when the tenant kind is customer. Details: {"application":"backup","edition(s)":[{"name":"standard","status":"active"},{"name":"advanced","status":"active"},{"name":"disaster_recovery","status":"active"}]}',
                ]],
            ],
            'invalid edition name' => [
                ['applications.0.editions.0.name' => 'standard2'],
                false,
                ['applications' => [
                    'Edition is not present in cloud. Details: {"application":"backup","edition(s)":{"name":"standard2","status":"active"}}',
                ]],
            ],
            'invalid offering item format' => [
                ['applications.0.offering_items.0' => ['offeringItemName' => 'test']],
                false,
                ['applications' => [
                    'Invalid offering item, required fields: name, measurement_unit, status. Details: {"application":"backup","offering_item":{"offeringItemName":"test"}}',
                ]],
            ],
            'not allowed offering item' => [
                ['applications.0.offering_items.0.name' => 'newOfferingItem'],
                false,
                ['applications' => [
                    'Offering item does not exist in {0}. Check available offering items in the parent tenant. Details: {"application":"backup","offering_item":{"name":"newOfferingItem","measurement_unit":"quantity","quota_value":10,"status":"active"}}',
                ]],
            ],
            'no meta for offering item' => [
                ['applications.0.offering_items.0.name' => 'noMetaInfo'],
                false,
                ['applications' => [
                    'Offering item has no meta information. Details: {"application":"backup","offering_item":{"name":"noMetaInfo","measurement_unit":"quantity","quota_value":10,"status":"active"}}',
                ]],
            ],
            'offering item has mismatching app type in meta' => [
                [
                    'applications.0.type' => 'files_cloud',
                    'applications.1.type' => 'backup',
                ],
                false,
                ['applications' => [
                    'Offering item application type is different than application type in meta info. Details: {"application":"backup","offering_item":{"name":"fc_child_storages","measurement_unit":"feature","status":"inactive"},"metaInfo":{"application_type":"files_cloud","offering_item_name":"fc_child_storages","offering_item_friendly_name":"Partner-owned cloud storage","measurement_unit":"feature","configurable_option":{"friendly_name":"Partner-owned files cloud storage","measurement_unit":"feature","measurement_unit_name":"Enable"},"sort_priority":206}}',
                ]],
            ],
            'offering item has mismatching measurement unit in meta' => [
                ['applications.0.offering_items.0.measurement_unit' => 'bytes'],
                false,
                ['applications' => [
                    'Offering item measurement unit is not the same as in meta info. Details: {"application":"backup","offering_item":{"name":"universal_devices","measurement_unit":"bytes","quota_value":10,"status":"active"},"metaInfo":{"application_type":"backup","edition_name":"standard","offering_item_name":"universal_devices","offering_item_friendly_name":"Universal","measurement_unit":"quantity","resource_type":"data","configurable_option":{"friendly_name":"Universal","measurement_unit":"quantity","measurement_unit_name":"Devices"},"sort_priority":1}}',
                ]],
            ],
            'active offering item belongs to inactive edition' => [
                ['applications.0.editions.0.status' => 'inactive'],
                false,
                ['applications' => [
                    'Active offering item cannot belong to missing or inactive edition. Details: {"application":"backup","offering_item":{"name":"universal_devices","measurement_unit":"quantity","quota_value":10,"status":"active"},"metaInfo":{"application_type":"backup","edition_name":"standard","offering_item_name":"universal_devices","offering_item_friendly_name":"Universal","measurement_unit":"quantity","resource_type":"data","configurable_option":{"friendly_name":"Universal","measurement_unit":"quantity","measurement_unit_name":"Devices"},"sort_priority":1}}',
                ]],
            ],
            'active offering item has inactive parent' => [
                ['applications.0.offering_items.7.status' => 'inactive'],
                false,
                ['applications' => [
                    'Offering item is a child of an offering item that is inactive or missing. Details: {"application":"backup","offering_item":{"name":"o365_mailboxes","measurement_unit":"feature","status":"active"}}',
                ]],
            ],
            'offering item has invalid infra id' => [
                ['applications.0.offering_items.0.infra_id' => '1234'],
                false,
                ['applications' => [
                    'Offering item has infra id that doesn\'t exist in Cloud. Details: {"application":"backup","offering_item":{"name":"universal_devices","measurement_unit":"quantity","quota_value":10,"status":"active","infra_id":"1234"}}',
                ]],
            ],
            'offering item has invalid infra id and measurement unit' => [
                [
                    'applications.0.offering_items.0.measurement_unit' => 'bytes',
                    'applications.0.offering_items.0.infra_id' => '1234',
                ],
                false,
                ['applications' => [
                    'Offering item measurement unit is not the same as in meta info. Details: {"application":"backup","offering_item":{"name":"universal_devices","measurement_unit":"bytes","quota_value":10,"status":"active","infra_id":"1234"},"metaInfo":{"application_type":"backup","edition_name":"standard","offering_item_name":"universal_devices","offering_item_friendly_name":"Universal","measurement_unit":"quantity","resource_type":"data","configurable_option":{"friendly_name":"Universal","measurement_unit":"quantity","measurement_unit_name":"Devices"},"sort_priority":1}}',
                ]],
            ],
            'customer has offering items with the same name' => [
                [
                    'tenant_kind' => 'customer',
                    'applications.0.editions.0.status' => 'active',
                    'applications.0.editions.1.status' => 'inactive',
                    'applications.0.editions.2.status' => 'inactive',
                    'applications.0.offering_items.0.name' => 'storage',
                    'applications.0.offering_items.0.status' => 'active',
                    'applications.0.offering_items.1.name' => 'storage',
                    'applications.0.offering_items.1.status' => 'active',
                ],
                false,
                ['applications' => [
                    'Only one infrastructure component can be enabled for a tenant of the "Customer" type. Details: {"application":"backup","offering_item":{"name":"storage","measurement_unit":"quantity","quota_value":20,"status":"active"}}',
                ]],
            ],
        ];
    }

    // Test Helpers

    private function getTemplateData($replacements = [])
    {
        if (!$this->templateData) {
            $this->templateData = json_decode(file_get_contents(ACRONIS_CLOUD_TEMPLATE_DATA_DIR . '/createTemplateData.json'), true);
        }
        $templateData = $this->templateData;
        foreach ($replacements as $path => $data) {
            $node =& $templateData;
            $explodedPath = explode('.', $path);
            foreach ($explodedPath as $key) {
                if (!isset($node[$key])) {
                    $node[$key] = [];
                }
                $node = &$node[$key];
            }

            $node = $data;
        }

        return $templateData;
    }

    private function getValidApplications()
    {
        $applicationTypes = [
            'backup',
            'files_cloud',
            'hci',
            'physical_data_shipping',
            'notary',
        ];

        return array_map(
            function ($appType) {
                return new Application(['type' => $appType]);
            },
            $applicationTypes
        );
    }

    private function getValidOfferingItems()
    {
        // name, status, edition
        $offeringItems = [
            ['universal_devices', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'standard'],
            ['workstations', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'standard'],
            ['servers', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'standard'],
            ['win_server_essentials', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'standard'],
            ['virtualhosts', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'standard'],
            ['vms', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'standard'],
            ['mobiles', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'standard'],
            ['mailboxes', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'standard'],
            ['o365_mailboxes', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'standard'],
            ['o365_onedrive', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'standard'],
            ['o365_sharepoint_sites', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'standard'],
            ['gsuite_seats', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'standard'],
            ['google_mail', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'standard'],
            ['google_drive', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'standard'],
            ['google_team_drive', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'standard'],
            ['web_hosting_servers', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'standard'],
            ['websites', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'standard'],

            // Backup - Local resources
            ['local_storage', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE],
            ['child_storages', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'standard'],
            ['dr_child_storages', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'standard'],
            ['storage', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'standard'],
            ['dr_storage', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'standard'],
            ['compute_points', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'standard'],
            ['public_ips', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'standard'],
            ['dr_cloud_servers', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'standard'],
            ['dr_internet_access', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'standard'],

            // Backup - Advanced Edition
            ['adv_universal_devices', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'advanced'],
            ['adv_workstations', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'advanced'],
            ['adv_servers', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'advanced'],
            ['adv_win_server_essentials', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'advanced'],
            ['adv_virtualhosts', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'advanced'],
            ['adv_vms', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'advanced'],
            ['adv_mobiles', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'advanced'],
            ['adv_o365_seats', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'advanced'],
            ['adv_o365_mailboxes', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'advanced'],
            ['adv_o365_onedrive', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'advanced'],
            ['adv_o365_sharepoint_sites', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'advanced'],
            ['adv_gsuite_seats', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'advanced'],
            ['adv_google_mail', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'advanced'],
            ['adv_google_drive', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'advanced'],
            ['adv_google_team_drive', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'advanced'],
            ['adv_web_hosting_servers', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'advanced'],
            ['adv_websites', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'advanced'],
            ['adv_storage', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'advanced'],
            ['adv_child_storages', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'advanced'],

            // Backup - Disaster Recovery Edition
            ['dre_universal_devices', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'disaster_recovery'],
            ['dre_workstations', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'disaster_recovery'],
            ['dre_servers', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'disaster_recovery'],
            ['dre_win_server_essentials', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'disaster_recovery'],
            ['dre_virtualhosts', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'disaster_recovery'],
            ['dre_vms', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'disaster_recovery'],
            ['dre_mobiles', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'disaster_recovery'],
            ['dre_o365_seats', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'disaster_recovery'],
            ['dre_o365_mailboxes', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'disaster_recovery'],
            ['dre_o365_onedrive', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'disaster_recovery'],
            ['dre_o365_sharepoint_sites', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'disaster_recovery'],
            ['dre_gsuite_seats', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'disaster_recovery'],
            ['dre_google_mail', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'disaster_recovery'],
            ['dre_google_drive', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'disaster_recovery'],
            ['dre_google_team_drive', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'disaster_recovery'],
            ['dre_web_hosting_servers', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'disaster_recovery'],
            ['dre_websites', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'disaster_recovery'],
            ['dre_storage', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'disaster_recovery'],
            ['dre_child_storages', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'disaster_recovery'],
            ['dre_dr_child_storages', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'disaster_recovery'],
            ['dre_dr_storage', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'disaster_recovery'],
            ['dre_compute_points', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'disaster_recovery'],
            ['dre_public_ips', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'disaster_recovery'],
            ['dre_dr_cloud_servers', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'disaster_recovery'],
            ['dre_dr_internet_access', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE, 'disaster_recovery'],

            // File Sync & Share
            ['fc_child_storages', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE],
            ['fc_seats', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE],
            ['fc_storage', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE],

            // Physical Data Shipping
            ['drives_shipped_to_cloud', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE],
            ['drives_shipped_from_cloud', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE],

            // Notary
            ['notary_storage', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE],
            ['notarizations', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE],
            ['esignatures', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE],

            // For testing of Offering items that are allowed but we don't have any meta info for them
            ['noMetaInfo', ApiInterface::OFFERING_ITEM_STATUS_ACTIVE],
        ];

        return array_map(
            function ($offeringItem) {
                $data = [
                    'name' => $offeringItem[0],
                    'status' => $offeringItem[1],
                ];
                if (isset($offeringItem[2])) {
                    $data['edition'] = $offeringItem[2];
                }

                return new OfferingItemOutput($data);
            },
            $offeringItems
        );
    }

    private function getValidInfras()
    {
        $infraIds = [
            '7064f5d1-1f4d-4ace-80bd-5f7c57768389',
            'ad2ce2a7-5930-49c2-a942-8bdb47b6e6c4',
            '2ee77cf9-24b5-4802-8995-d92d6eecd0db',
            '6b041407-f280-41bf-bcae-6153acd95c2d',
        ];

        return array_map(
            function ($infraId) {
                return new Infra(['id' => $infraId]);
            },
            $infraIds
        );
    }
}
