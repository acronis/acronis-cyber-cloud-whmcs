<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

use AcronisCloud\Localization\L10n;
use WHMCS\UsageBilling\Contracts\Metrics\MetricInterface;
use AcronisCloud\Util\UomConverter;

return [

    // Cyber Protect - Essentials

    [
        'system_name' => 'pw_p_ess_workstations',
        'display_name' => L10n::gettext('Cyber Protect Essentials') . ' - ' . L10n::gettext('Workstations'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::QUANTITY,
    ],

    [
        'system_name' => 'pw_p_ess_servers',
        'display_name' => L10n::gettext('Cyber Protect Essentials') . ' - ' . L10n::gettext('Servers'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::QUANTITY,
    ],

    [
        'system_name' => 'pw_p_ess_vms',
        'display_name' => L10n::gettext('Cyber Protect Essentials') . ' - ' . L10n::gettext('Virtual machines'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::QUANTITY,
    ],

    // Cyber Protect - Standard

    [
        'system_name' => 'pw_p_workstations',
        'display_name' => L10n::gettext('Cyber Protect Standard') . ' - ' . L10n::gettext('Workstations'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::QUANTITY,
    ],

    [
        'system_name' => 'pw_p_servers',
        'display_name' => L10n::gettext('Cyber Protect Standard') . ' - ' . L10n::gettext('Servers'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::QUANTITY,
    ],

    [
        'system_name' => 'pw_p_vms',
        'display_name' => L10n::gettext('Cyber Protect Standard') . ' - ' . L10n::gettext('Virtual machines'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::QUANTITY,
    ],

    [
        'system_name' => 'pw_p_web_hosting_servers',
        'display_name' => L10n::gettext('Cyber Protect Standard') . ' - ' . L10n::gettext('Web hosting servers'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::QUANTITY,
    ],

    // Cyber Protect - Advanced

    [
        'system_name' => 'pw_p_adv_workstations',
        'display_name' => L10n::gettext('Cyber Protect Advanced') . ' - ' . L10n::gettext('Workstations'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::QUANTITY,
    ],

    [
        'system_name' => 'pw_p_adv_servers',
        'display_name' => L10n::gettext('Cyber Protect Advanced') . ' - ' . L10n::gettext('Servers'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::QUANTITY,
    ],

    [
        'system_name' => 'pw_p_adv_vms',
        'display_name' => L10n::gettext('Cyber Protect Advanced') . ' - ' . L10n::gettext('Virtual machines'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::QUANTITY,
    ],

    [
        'system_name' => 'pw_p_adv_web_hosting_servers',
        'display_name' => L10n::gettext('Cyber Protect Advanced') . ' - ' . L10n::gettext('Web hosting servers'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::QUANTITY,
    ],

    // Cyber Protect - Backup Standard

    [
        'system_name' => 'pw_workstations',
        'display_name' => L10n::gettext('Cyber Backup Standard') . ' - ' . L10n::gettext('Workstations'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::QUANTITY,
    ],

    [
        'system_name' => 'pw_servers',
        'display_name' => L10n::gettext('Cyber Backup Standard') . ' - ' . L10n::gettext('Servers'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::QUANTITY,
    ],

    [
        'system_name' => 'pw_vms',
        'display_name' => L10n::gettext('Cyber Backup Standard') . ' - ' . L10n::gettext('Virtual machines'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::QUANTITY,
    ],

    [
        'system_name' => 'pw_web_hosting_servers',
        'display_name' => L10n::gettext('Cyber Backup Standard') . ' - ' . L10n::gettext('Web hosting servers'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::QUANTITY,
    ],

    [
        'system_name' => 'pw_mobiles',
        'display_name' => L10n::gettext('Cyber Backup Standard') . ' - ' . L10n::gettext('Mobile devices'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::QUANTITY,
    ],

    [
        'system_name' => 'pw_o365_seats',
        'display_name' => L10n::gettext('Cyber Backup Standard') . ' - ' . L10n::gettext('Microsoft 365 seats'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::QUANTITY,
    ],

    [
        'system_name' => 'pw_o365_teams',
        'display_name' => L10n::gettext('Cyber Backup Standard') . ' - ' . L10n::gettext('Microsoft 365 Teams'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::QUANTITY,
    ],

    [
        'system_name' => 'pw_gsuite_seats',
        'display_name' => L10n::gettext('Cyber Backup Standard') . ' - ' . L10n::gettext('Gmail (incl. Calendar, Contacts)'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::QUANTITY,
    ],

    [
        'system_name' => 'pw_websites',
        'display_name' => L10n::gettext('Cyber Backup Standard') . ' - ' . L10n::gettext('Websites'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::QUANTITY,
    ],

    // Cyber Protect Cloud

    [
        'system_name' => 'pw_storage_acronis',
        'display_name' => L10n::gettext('Cyber Protect Cloud') . ' - ' . L10n::gettext('Acronis hosted storage'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::BYTES,
        'metric_unit' => UomConverter::GIGABYTES,
    ],

    [
        'system_name' => 'pw_storage_google',
        'display_name' => L10n::gettext('Cyber Protect Cloud') . ' - ' . L10n::gettext('Google hosted storage'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::BYTES,
        'metric_unit' => UomConverter::GIGABYTES,
    ],

    [
        'system_name' => 'pw_storage_azure',
        'display_name' => L10n::gettext('Cyber Protect Cloud') . ' - ' . L10n::gettext('Azure hosted storage'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::BYTES,
        'metric_unit' => UomConverter::GIGABYTES,
    ],

    [
        'system_name' => 'pw_storage',
        'display_name' => L10n::gettext('Cyber Protect Cloud') . ' - ' . L10n::gettext('Cloud storage'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::BYTES,
        'metric_unit' => UomConverter::GIGABYTES,
    ],

    // Disaster Recovery Cloud

    [
        'system_name' => 'pw_dr_storage',
        'display_name' => L10n::gettext('Disaster Recovery Cloud') . ' - ' . L10n::gettext('Cloud storage'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::BYTES,
        'metric_unit' => UomConverter::GIGABYTES,
    ],

    [
        'system_name' => 'pw_dr_child_storage',
        'display_name' => L10n::gettext('Disaster Recovery Cloud') . ' - ' . L10n::gettext('Partner storage'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::BYTES,
        'metric_unit' => UomConverter::GIGABYTES,
    ],

    [
        'system_name' => 'pw_compute_points',
        'display_name' => L10n::gettext('Disaster Recovery Cloud') . ' - ' . L10n::gettext('Acronis Hosted - 1 compute point'),
        'type' => MetricInterface::TYPE_PERIOD_MONTH,
        'unit' => UomConverter::SECONDS,
        'metric_unit' => UomConverter::HOURS,
    ],

    [
        'system_name' => 'pw_public_ips',
        'display_name' => L10n::gettext('Disaster Recovery Cloud') . ' - ' . L10n::gettext('Acronis Hosted - Public IP'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::QUANTITY,
    ],

    // Cyber Backup

    [
        'system_name' => 'pg_storage_acronis',
        'display_name' => L10n::gettext('Per GB') . ': ' .  L10n::gettext('Cyber Backup Cloud Standard') . ' - ' . L10n::gettext('Acronis hosted storage'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::BYTES,
        'metric_unit' => UomConverter::GIGABYTES
    ],

    [
        'system_name' => 'pg_storage_google',
        'display_name' => L10n::gettext('Per GB') . ': ' .  L10n::gettext('Cyber Backup Cloud Standard') . ' - ' . L10n::gettext('Google hosted storage'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::BYTES,
        'metric_unit' => UomConverter::GIGABYTES
    ],

    [
        'system_name' => 'pg_storage_azure',
        'display_name' => L10n::gettext('Per GB') . ': ' .  L10n::gettext('Cyber Backup Cloud Standard') . ' - ' . L10n::gettext('Azure hosted storage'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::BYTES,
        'metric_unit' => UomConverter::GIGABYTES
    ],

    [
        'system_name' => 'pg_storage',
        'display_name' => L10n::gettext('Per GB') . ': ' .  L10n::gettext('Cyber Backup Cloud Standard') . ' - ' . L10n::gettext('Cloud storage'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::BYTES,
        'metric_unit' => UomConverter::GIGABYTES
    ],

    [
        'system_name' => 'pg_child_storages',
        'display_name' => L10n::gettext('Per GB') . ': ' .  L10n::gettext('Cyber Backup Cloud Standard') . ' - ' . L10n::gettext('Partner storage'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::BYTES,
        'metric_unit' => UomConverter::GIGABYTES
    ],

    [
        'system_name' => 'local_storage',
        'display_name' => L10n::gettext('Per GB') . ': ' .  L10n::gettext('Cyber Backup Cloud Standard') . ' - ' . L10n::gettext('Local storage'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::BYTES,
        'metric_unit' => UomConverter::GIGABYTES
    ],

    // Cyber Files Cloud

    [
        'system_name' => 'fc_seats',
        'display_name' => L10n::gettext('Cyber Files Cloud') . ' - ' . L10n::gettext('Licensed users'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::QUANTITY,
    ],

    [
        'system_name' => 'fc_storage',
        'display_name' => L10n::gettext('Cyber Files Cloud') . ' - ' . L10n::gettext('Cloud storage'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::BYTES,
        'metric_unit' => UomConverter::GIGABYTES
    ],

    [
        'system_name' => 'fc_child_storages',
        'display_name' => L10n::gettext('Cyber Files Cloud') . ' - ' . L10n::gettext('Partner hosted storage'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::BYTES,
        'metric_unit' => UomConverter::GIGABYTES
    ],

    // Cyber Notary

    [
        'system_name' => 'notary_storage',
        'display_name' => L10n::gettext('Cyber Notary') . ' - ' . L10n::gettext('Notary storage'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::BYTES,
        'metric_unit' => UomConverter::GIGABYTES
    ],

    [
        'system_name' => 'notarizations',
        'display_name' => L10n::gettext('Cyber Notary') . ' - ' . L10n::gettext('Notarization (Ethereum)'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::QUANTITY,
    ],

    [
        'system_name' => 'esignatures',
        'display_name' => L10n::gettext('Cyber Notary') . ' - ' . L10n::gettext('eSignatures (Ethereum)'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::QUANTITY,
    ],

    // Cyber Infrastructure

    [
        'system_name' => 'hci_total_storage',
        'display_name' => L10n::gettext('Cyber Infrastructure') . ' - ' . L10n::gettext('Storage'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::BYTES,
        'metric_unit' => UomConverter::GIGABYTES
    ],

    // Physical Data Shipping

    [
        'system_name' => 'drives_shipped_to_cloud',
        'display_name' => L10n::gettext('Physical Data Shipping') . ' - ' . L10n::gettext('To the Cloud'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::QUANTITY,
    ],

    [
        'system_name' => 'drives_shipped_from_cloud',
        'display_name' => L10n::gettext('Physical Data Shipping') . ' - ' . L10n::gettext('From the Cloud'),
        'type' => MetricInterface::TYPE_SNAPSHOT,
        'unit' => UomConverter::QUANTITY,
    ],

];
