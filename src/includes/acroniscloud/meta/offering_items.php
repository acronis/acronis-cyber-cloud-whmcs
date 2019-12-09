<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

use AcronisCloud\Util\UomConverter;
use AcronisCloud\Localization\L10n;

return [
    // Backup - Standard Edition
    [
        'application_type' => 'backup',
        'edition_name' => 'standard',
        'offering_item_name' => 'universal_devices',
        'offering_item_friendly_name' => L10n::gettext('Universal'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Universal'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Devices'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'standard',
        'offering_item_name' => 'workstations',
        'offering_item_friendly_name' => L10n::gettext('Workstations'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Workstations'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Workstations'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'standard',
        'offering_item_name' => 'servers',
        'offering_item_friendly_name' => L10n::gettext('Servers'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Servers'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Servers'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'standard',
        'offering_item_name' => 'win_server_essentials',
        'offering_item_friendly_name' => L10n::gettext('Windows Server Essentials'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Windows Server Essentials'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Servers'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'standard',
        'offering_item_name' => 'virtualhosts',
        'offering_item_friendly_name' => L10n::gettext('Virtual hosts'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Virtual hosts'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Virtual hosts'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'standard',
        'offering_item_name' => 'vms',
        'offering_item_friendly_name' => L10n::gettext('Virtual machines'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Virtual machines'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Machines'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'standard',
        'offering_item_name' => 'mobiles',
        'offering_item_friendly_name' => L10n::gettext('Mobile devices'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Mobile devices'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Devices'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'standard',
        'offering_item_name' => 'mailboxes',
        'offering_item_friendly_name' => L10n::gettext('Office 365 seats'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Office 365 seats'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Seats'),
        ],
        'child_offering_items' => [
            'o365_mailboxes',
            'o365_onedrive',
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'standard',
        'offering_item_name' => 'o365_mailboxes',
        'offering_item_friendly_name' => L10n::gettext('Mailboxes'),
        'measurement_unit' => UomConverter::FEATURE,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Office 365 Mailboxes'),
            'measurement_unit' => UomConverter::FEATURE,
            'measurement_unit_name' => L10n::gettext('Enable'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'standard',
        'offering_item_name' => 'o365_onedrive',
        'offering_item_friendly_name' => L10n::gettext('OneDrive'),
        'measurement_unit' => UomConverter::FEATURE,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Office 365 OneDrive'),
            'measurement_unit' => UomConverter::FEATURE,
            'measurement_unit_name' => L10n::gettext('Enable'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'standard',
        'offering_item_name' => 'o365_sharepoint_sites',
        'offering_item_friendly_name' => L10n::gettext('Office 365 SharePoint Online'),
        'measurement_unit' => UomConverter::FEATURE,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Office 365 SharePoint Online'),
            'measurement_unit' => UomConverter::FEATURE,
            'measurement_unit_name' => L10n::gettext('Enable'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'standard',
        'offering_item_name' => 'gsuite_seats',
        'offering_item_friendly_name' => L10n::gettext('G Suite seats'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('G Suite seats'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Seats'),
        ],
        'child_offering_items' => [
            'google_mail',
            'google_drive',
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'standard',
        'offering_item_name' => 'google_mail',
        'offering_item_friendly_name' => L10n::gettext('Gmail (incl. Calendar, Contacts)'),
        'measurement_unit' => UomConverter::FEATURE,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Gmail (incl. Calendar, Contacts)'),
            'measurement_unit' => UomConverter::FEATURE,
            'measurement_unit_name' => L10n::gettext('Enable'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'standard',
        'offering_item_name' => 'google_drive',
        'offering_item_friendly_name' => L10n::gettext('Google Drive'),
        'measurement_unit' => UomConverter::FEATURE,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Google Drive'),
            'measurement_unit' => UomConverter::FEATURE,
            'measurement_unit_name' => L10n::gettext('Enable'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'standard',
        'offering_item_name' => 'google_team_drive',
        'offering_item_friendly_name' => L10n::gettext('G Suite Shared Drive'),
        'measurement_unit' => UomConverter::FEATURE,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('G Suite Shared Drive'),
            'measurement_unit' => UomConverter::FEATURE,
            'measurement_unit_name' => L10n::gettext('Enable'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'standard',
        'offering_item_name' => 'web_hosting_servers',
        'offering_item_friendly_name' => L10n::gettext('Web hosting servers'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Web hosting servers'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Servers'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'standard',
        'offering_item_name' => 'websites',
        'offering_item_friendly_name' => L10n::gettext('Websites'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Websites'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Websites'),
        ],
    ],

    // Backup - Local resources

    [
        'application_type' => 'backup',
        'offering_item_name' => 'local_storage',
        'offering_item_friendly_name' => L10n::gettext('Local backup'),
        'measurement_unit' => UomConverter::BYTES,
        'resource_type' => 'local',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Local Storage'),
            'measurement_unit' => UomConverter::GIGABYTES,
            'measurement_unit_name' => L10n::gettext('Gb'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'standard',
        'offering_item_name' => 'child_storages',
        'offering_item_friendly_name' => L10n::gettext('Partner-owned backup storage'),
        'measurement_unit' => UomConverter::FEATURE,
        'resource_type' => 'cloud',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Partner-owned backup storage'),
            'measurement_unit' => UomConverter::FEATURE,
            'measurement_unit_name' => L10n::gettext('Enable'),
        ],
        'child_offering_items' => [
            'dr_child_storages',
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'standard',
        'offering_item_name' => 'dr_child_storages',
        'offering_item_friendly_name' => L10n::gettext('Partner-owned disaster recovery infrastructure'),
        'measurement_unit' => UomConverter::FEATURE,
        'resource_type' => 'cloud',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Partner-owned disaster recovery infrastructure'),
            'measurement_unit' => UomConverter::FEATURE,
            'measurement_unit_name' => L10n::gettext('Enable'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'standard',
        'offering_item_name' => 'storage',
        'offering_item_friendly_name' => L10n::gettext('Backup storage'),
        'measurement_unit' => UomConverter::BYTES,
        'resource_type' => 'cloud',
        'capability' => 'backup',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Cloud Storage'),
            'measurement_unit' => UomConverter::GIGABYTES,
            'measurement_unit_name' => L10n::gettext('Gb'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'standard',
        'offering_item_name' => 'dr_storage',
        'offering_item_friendly_name' => L10n::gettext('Disaster recovery storage'),
        'measurement_unit' => UomConverter::BYTES,
        'resource_type' => 'cloud',
        'capability' => 'disaster_recovery',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Disaster recovery storage'),
            'measurement_unit' => UomConverter::GIGABYTES,
            'measurement_unit_name' => L10n::gettext('Gb'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'standard',
        'offering_item_name' => 'compute_points',
        'offering_item_friendly_name' => L10n::gettext('Compute points'),
        'measurement_unit' => UomConverter::SECONDS,
        'resource_type' => 'cloud',
        'capability' => 'disaster_recovery',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Compute points'),
            'measurement_unit' => UomConverter::HOURS,
            'measurement_unit_name' => L10n::gettext('Compute points'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'standard',
        'offering_item_name' => 'public_ips',
        'offering_item_friendly_name' => L10n::gettext('Public IP addresses'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'cloud',
        'capability' => 'disaster_recovery',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Public IP addresses'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('IP addresses'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'standard',
        'offering_item_name' => 'dr_cloud_servers',
        'offering_item_friendly_name' => L10n::gettext('Cloud servers'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'cloud',
        'capability' => 'disaster_recovery',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Cloud servers'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Servers'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'standard',
        'offering_item_name' => 'dr_internet_access',
        'offering_item_friendly_name' => L10n::gettext('Internet access'),
        'measurement_unit' => UomConverter::FEATURE,
        'resource_type' => 'cloud',
        'capability' => 'disaster_recovery',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Internet access'),
            'measurement_unit' => UomConverter::FEATURE,
            'measurement_unit_name' => L10n::gettext('Enable'),
        ],
    ],

    // Backup - Advanced Edition
    [
        'application_type' => 'backup',
        'edition_name' => 'advanced',
        'offering_item_name' => 'adv_universal_devices',
        'offering_item_friendly_name' => L10n::gettext('Universal'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Universal'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Devices'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'advanced',
        'offering_item_name' => 'adv_workstations',
        'offering_item_friendly_name' => L10n::gettext('Workstations'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Workstations'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Workstations'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'advanced',
        'offering_item_name' => 'adv_servers',
        'offering_item_friendly_name' => L10n::gettext('Servers'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Servers'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Servers'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'advanced',
        'offering_item_name' => 'adv_win_server_essentials',
        'offering_item_friendly_name' => L10n::gettext('Windows Server Essentials'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Windows Server Essentials'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Servers'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'advanced',
        'offering_item_name' => 'adv_virtualhosts',
        'offering_item_friendly_name' => L10n::gettext('Virtual hosts'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Virtual hosts'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Virtual hosts'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'advanced',
        'offering_item_name' => 'adv_vms',
        'offering_item_friendly_name' => L10n::gettext('Virtual machines'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Virtual machines'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Machines'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'advanced',
        'offering_item_name' => 'adv_mobiles',
        'offering_item_friendly_name' => L10n::gettext('Mobile devices'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Mobile devices'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Devices'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'advanced',
        'offering_item_name' => 'adv_o365_seats',
        'offering_item_friendly_name' => L10n::gettext('Office 365 seats'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Office 365 seats'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Seats'),
        ],
        'child_offering_items' => [
            'adv_o365_mailboxes',
            'adv_o365_onedrive',
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'advanced',
        'offering_item_name' => 'adv_o365_mailboxes',
        'offering_item_friendly_name' => L10n::gettext('Mailboxes'),
        'measurement_unit' => UomConverter::FEATURE,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Office 365 Mailboxes'),
            'measurement_unit' => UomConverter::FEATURE,
            'measurement_unit_name' => L10n::gettext('Enable'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'advanced',
        'offering_item_name' => 'adv_o365_onedrive',
        'offering_item_friendly_name' => L10n::gettext('OneDrive'),
        'measurement_unit' => UomConverter::FEATURE,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Office 365 OneDrive'),
            'measurement_unit' => UomConverter::FEATURE,
            'measurement_unit_name' => L10n::gettext('Enable'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'advanced',
        'offering_item_name' => 'adv_o365_sharepoint_sites',
        'offering_item_friendly_name' => L10n::gettext('Office 365 SharePoint Online'),
        'measurement_unit' => UomConverter::FEATURE,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Office 365 SharePoint Online'),
            'measurement_unit' => UomConverter::FEATURE,
            'measurement_unit_name' => L10n::gettext('Enable'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'advanced',
        'offering_item_name' => 'adv_gsuite_seats',
        'offering_item_friendly_name' => L10n::gettext('G Suite seats'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('G Suite seats'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Seats'),
        ],
        'child_offering_items' => [
            'adv_google_mail',
            'adv_google_drive',
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'advanced',
        'offering_item_name' => 'adv_google_mail',
        'offering_item_friendly_name' => L10n::gettext('Gmail (incl. Calendar, Contacts)'),
        'measurement_unit' => UomConverter::FEATURE,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Gmail (incl. Calendar, Contacts)'),
            'measurement_unit' => UomConverter::FEATURE,
            'measurement_unit_name' => L10n::gettext('Enable'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'advanced',
        'offering_item_name' => 'adv_google_drive',
        'offering_item_friendly_name' => L10n::gettext('Google Drive'),
        'measurement_unit' => UomConverter::FEATURE,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Google Drive'),
            'measurement_unit' => UomConverter::FEATURE,
            'measurement_unit_name' => L10n::gettext('Enable'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'advanced',
        'offering_item_name' => 'adv_google_team_drive',
        'offering_item_friendly_name' => L10n::gettext('G Suite Shared Drive'),
        'measurement_unit' => UomConverter::FEATURE,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('G Suite Shared Drive'),
            'measurement_unit' => UomConverter::FEATURE,
            'measurement_unit_name' => L10n::gettext('Enable'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'advanced',
        'offering_item_name' => 'adv_web_hosting_servers',
        'offering_item_friendly_name' => L10n::gettext('Web hosting servers'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Web hosting servers'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Servers'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'advanced',
        'offering_item_name' => 'adv_websites',
        'offering_item_friendly_name' => L10n::gettext('Websites'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Websites'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Websites'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'advanced',
        'offering_item_name' => 'adv_storage',
        'offering_item_friendly_name' => L10n::gettext('Backup storage'),
        'measurement_unit' => UomConverter::BYTES,
        'resource_type' => 'cloud',
        'capability' => 'backup',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Cloud Storage'),
            'measurement_unit' => UomConverter::GIGABYTES,
            'measurement_unit_name' => L10n::gettext('Gb'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'advanced',
        'offering_item_name' => 'adv_child_storages',
        'offering_item_friendly_name' => L10n::gettext('Partner-owned backup storage'),
        'measurement_unit' => UomConverter::FEATURE,
        'resource_type' => 'cloud',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Partner-owned backup storage'),
            'measurement_unit' => UomConverter::FEATURE,
            'measurement_unit_name' => L10n::gettext('Enable'),
        ],
    ],

    // Backup - Disaster Recovery Edition
    [
        'application_type' => 'backup',
        'edition_name' => 'disaster_recovery',
        'offering_item_name' => 'dre_universal_devices',
        'offering_item_friendly_name' => L10n::gettext('Universal'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Universal'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Devices'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'disaster_recovery',
        'offering_item_name' => 'dre_workstations',
        'offering_item_friendly_name' => L10n::gettext('Workstations'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Workstations'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Workstations'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'disaster_recovery',
        'offering_item_name' => 'dre_servers',
        'offering_item_friendly_name' => L10n::gettext('Servers'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Servers'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Servers'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'disaster_recovery',
        'offering_item_name' => 'dre_win_server_essentials',
        'offering_item_friendly_name' => L10n::gettext('Windows Server Essentials'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Windows Server Essentials'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Servers'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'disaster_recovery',
        'offering_item_name' => 'dre_virtualhosts',
        'offering_item_friendly_name' => L10n::gettext('Virtual hosts'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Virtual hosts'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Virtual hosts'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'disaster_recovery',
        'offering_item_name' => 'dre_vms',
        'offering_item_friendly_name' => L10n::gettext('Virtual machines'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Virtual machines'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Machines'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'disaster_recovery',
        'offering_item_name' => 'dre_mobiles',
        'offering_item_friendly_name' => L10n::gettext('Mobile devices'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Mobile devices'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Devices'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'disaster_recovery',
        'offering_item_name' => 'dre_o365_seats',
        'offering_item_friendly_name' => L10n::gettext('Office 365 seats'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Office 365 seats'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Seats'),
        ],
        'child_offering_items' => [
            'dre_o365_mailboxes',
            'dre_o365_onedrive',
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'disaster_recovery',
        'offering_item_name' => 'dre_o365_mailboxes',
        'offering_item_friendly_name' => L10n::gettext('Mailboxes'),
        'measurement_unit' => UomConverter::FEATURE,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Office 365 Mailboxes'),
            'measurement_unit' => UomConverter::FEATURE,
            'measurement_unit_name' => L10n::gettext('Enable'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'disaster_recovery',
        'offering_item_name' => 'dre_o365_onedrive',
        'offering_item_friendly_name' => L10n::gettext('OneDrive'),
        'measurement_unit' => UomConverter::FEATURE,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Office 365 OneDrive'),
            'measurement_unit' => UomConverter::FEATURE,
            'measurement_unit_name' => L10n::gettext('Enable'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'disaster_recovery',
        'offering_item_name' => 'dre_o365_sharepoint_sites',
        'offering_item_friendly_name' => L10n::gettext('Office 365 SharePoint Online'),
        'measurement_unit' => UomConverter::FEATURE,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Office 365 SharePoint Online'),
            'measurement_unit' => UomConverter::FEATURE,
            'measurement_unit_name' => L10n::gettext('Enable'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'disaster_recovery',
        'offering_item_name' => 'dre_gsuite_seats',
        'offering_item_friendly_name' => L10n::gettext('G Suite seats'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('G Suite seats'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Seats'),
        ],
        'child_offering_items' => [
            'dre_google_mail',
            'dre_google_drive',
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'disaster_recovery',
        'offering_item_name' => 'dre_google_mail',
        'offering_item_friendly_name' => L10n::gettext('Gmail (incl. Calendar, Contacts)'),
        'measurement_unit' => UomConverter::FEATURE,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Gmail (incl. Calendar, Contacts)'),
            'measurement_unit' => UomConverter::FEATURE,
            'measurement_unit_name' => L10n::gettext('Enable'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'disaster_recovery',
        'offering_item_name' => 'dre_google_drive',
        'offering_item_friendly_name' => L10n::gettext('Google Drive'),
        'measurement_unit' => UomConverter::FEATURE,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Google Drive'),
            'measurement_unit' => UomConverter::FEATURE,
            'measurement_unit_name' => L10n::gettext('Enable'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'disaster_recovery',
        'offering_item_name' => 'dre_google_team_drive',
        'offering_item_friendly_name' => L10n::gettext('G Suite Shared Drive'),
        'measurement_unit' => UomConverter::FEATURE,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('G Suite Shared Drive'),
            'measurement_unit' => UomConverter::FEATURE,
            'measurement_unit_name' => L10n::gettext('Enable'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'disaster_recovery',
        'offering_item_name' => 'dre_web_hosting_servers',
        'offering_item_friendly_name' => L10n::gettext('Web hosting servers'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Web hosting servers'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Servers'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'disaster_recovery',
        'offering_item_name' => 'dre_websites',
        'offering_item_friendly_name' => L10n::gettext('Websites'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'data',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Websites'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Websites'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'disaster_recovery',
        'offering_item_name' => 'dre_storage',
        'offering_item_friendly_name' => L10n::gettext('Backup storage'),
        'measurement_unit' => UomConverter::BYTES,
        'resource_type' => 'cloud',
        'capability' => 'backup',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Cloud Storage'),
            'measurement_unit' => UomConverter::GIGABYTES,
            'measurement_unit_name' => L10n::gettext('Gb'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'disaster_recovery',
        'offering_item_name' => 'dre_child_storages',
        'offering_item_friendly_name' => L10n::gettext('Partner-owned backup storage'),
        'measurement_unit' => UomConverter::FEATURE,
        'resource_type' => 'cloud',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Partner-owned backup storage'),
            'measurement_unit' => UomConverter::FEATURE,
            'measurement_unit_name' => L10n::gettext('Enable'),
        ],
        'child_offering_items' => [
            'dre_dr_child_storages',
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'disaster_recovery',
        'offering_item_name' => 'dre_dr_child_storages',
        'offering_item_friendly_name' => L10n::gettext('Partner-owned disaster recovery infrastructure'),
        'measurement_unit' => UomConverter::FEATURE,
        'resource_type' => 'cloud',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Partner-owned disaster recovery infrastructure'),
            'measurement_unit' => UomConverter::FEATURE,
            'measurement_unit_name' => L10n::gettext('Enable'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'disaster_recovery',
        'offering_item_name' => 'dre_dr_storage',
        'offering_item_friendly_name' => L10n::gettext('Disaster recovery storage'),
        'measurement_unit' => UomConverter::BYTES,
        'resource_type' => 'cloud',
        'capability' => 'disaster_recovery',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Disaster recovery storage'),
            'measurement_unit' => UomConverter::GIGABYTES,
            'measurement_unit_name' => L10n::gettext('Gb'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'disaster_recovery',
        'offering_item_name' => 'dre_compute_points',
        'offering_item_friendly_name' => L10n::gettext('Compute points'),
        'measurement_unit' => UomConverter::SECONDS,
        'resource_type' => 'cloud',
        'capability' => 'disaster_recovery',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Compute points'),
            'measurement_unit' => UomConverter::HOURS,
            'measurement_unit_name' => L10n::gettext('Compute points'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'disaster_recovery',
        'offering_item_name' => 'dre_public_ips',
        'offering_item_friendly_name' => L10n::gettext('Public IP addresses'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'cloud',
        'capability' => 'disaster_recovery',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Public IP addresses'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('IP addresses'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'disaster_recovery',
        'offering_item_name' => 'dre_dr_cloud_servers',
        'offering_item_friendly_name' => L10n::gettext('Cloud servers'),
        'measurement_unit' => UomConverter::QUANTITY,
        'resource_type' => 'cloud',
        'capability' => 'disaster_recovery',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Cloud servers'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Servers'),
        ],
    ],

    [
        'application_type' => 'backup',
        'edition_name' => 'disaster_recovery',
        'offering_item_name' => 'dre_dr_internet_access',
        'offering_item_friendly_name' => L10n::gettext('Internet access'),
        'measurement_unit' => UomConverter::FEATURE,
        'resource_type' => 'cloud',
        'capability' => 'disaster_recovery',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Internet access'),
            'measurement_unit' => UomConverter::FEATURE,
            'measurement_unit_name' => L10n::gettext('Enable'),
        ],
    ],

    // File Sync & Share

    [
        'application_type' => 'files_cloud',
        'offering_item_name' => 'fc_child_storages',
        'offering_item_friendly_name' => L10n::gettext('Partner-owned cloud storage'),
        'measurement_unit' => UomConverter::FEATURE,
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Partner-owned files cloud storage'),
            'measurement_unit' => UomConverter::FEATURE,
            'measurement_unit_name' => L10n::gettext('Enable'),
        ],
    ],

    [
        'application_type' => 'files_cloud',
        'offering_item_name' => 'fc_seats',
        'offering_item_friendly_name' => L10n::gettext('Users'),
        'measurement_unit' => UomConverter::QUANTITY,
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Files cloud users'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Seats'),
        ],
    ],

    [
        'application_type' => 'files_cloud',
        'offering_item_name' => 'fc_storage',
        'offering_item_friendly_name' => L10n::gettext('Cloud storage'),
        'measurement_unit' => UomConverter::BYTES,
        'capability' => 'files_cloud',
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Files cloud storage'),
            'measurement_unit' => UomConverter::GIGABYTES,
            'measurement_unit_name' => L10n::gettext('Gb'),
        ],
    ],

    // Physical Data Shipping
    [
        'application_type' => 'physical_data_shipping',
        'offering_item_name' => 'drives_shipped_to_cloud',
        'offering_item_friendly_name' => L10n::gettext('To the cloud'),
        'measurement_unit' => UomConverter::QUANTITY,
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Physical Data Shipping to the cloud'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Backups'),
        ],
    ],

    [
        'application_type' => 'physical_data_shipping',
        'offering_item_name' => 'drives_shipped_from_cloud',
        'offering_item_friendly_name' => L10n::gettext('From the cloud'),
        'measurement_unit' => UomConverter::QUANTITY,
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Physical Data Shipping from the cloud'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Backups'),
        ],
    ],

    //Notary
    [
        'application_type' => 'notary',
        'offering_item_name' => 'notary_storage',
        'offering_item_friendly_name' => L10n::gettext('Notary storage'),
        'measurement_unit' => UomConverter::BYTES,
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Notary storage'),
            'measurement_unit' => UomConverter::GIGABYTES,
            'measurement_unit_name' => L10n::gettext('Gb'),
        ],
    ],

    [
        'application_type' => 'notary',
        'offering_item_name' => 'notarizations',
        'offering_item_friendly_name' => L10n::gettext('Notarizations'),
        'measurement_unit' => UomConverter::QUANTITY,
        'configurable_option' => [
            'friendly_name' => L10n::gettext('Notarizations'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('Notarizations'),
        ],
    ],

    [
        'application_type' => 'notary',
        'offering_item_name' => 'esignatures',
        'offering_item_friendly_name' => L10n::gettext('eSignatures'),
        'measurement_unit' => UomConverter::QUANTITY,
        'configurable_option' => [
            'friendly_name' => L10n::gettext('eSignatures'),
            'measurement_unit' => UomConverter::QUANTITY,
            'measurement_unit_name' => L10n::gettext('eSignatures'),
        ],
    ],
];
