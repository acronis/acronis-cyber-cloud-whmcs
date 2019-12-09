<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

use AcronisCloud\Localization\L10n;

return [
    [
        'type' => 'platform',
        'title' => L10n::gettext('Management Portal'),
    ],
    [
        'type' => 'backup',
        'title' => L10n::gettext('Backup & Disaster Recovery'),
        'description' => L10n::gettext('Enables backup and recovery of physical and virtual machines, files, databases, and other data to local or cloud storage.'),
        'tenant_kinds' => [
            'partner',
            'customer'
        ],
    ],
    [
        'type' => 'files_cloud',
        'title' => L10n::gettext('File Sync & Share'),
        'description' => L10n::gettext('Provides file-sharing capabilities, enabling users to store, synchronize, and share encrypted content in the cloud and across their devices.'),
        'tenant_kinds' => [
            'partner',
            'customer'
        ],
    ],
    [
        'type' => 'hci',
        'title' => L10n::gettext('Cyber Infrastructure SPLA'),
        'description' => L10n::gettext('Enables service providers to use a Service Provider License Agreement (SPLA) for Acronis Cyber Infrastructure, instead of a license key.'),
        'tenant_kinds' => [
            'partner',
        ],
    ],
    [
        'type' => 'physical_data_shipping',
        'title' => L10n::gettext('Physical Data Shipping'),
        'description' => L10n::gettext('Enables users to send data to the cloud data center on a hard disk drive instead of transferring the data over the Internet.'),
        'tenant_kinds' => [
            'partner',
            'customer'
        ],
    ],
    [
        'type' => 'notary',
        'title' => L10n::gettext('Notary'),
        'description' => L10n::gettext('Enables users to notarize and verify files by using the Blockchain technology, and sign files electronically.'),
        'tenant_kinds' => [
            'partner',
            'customer'
        ],
    ],
];