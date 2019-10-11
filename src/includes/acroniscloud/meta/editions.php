<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

use AcronisCloud\Localization\L10n;

return [
    [
        'edition_name' => 'standard',
        'title' => L10n::gettext('Standard Edition'),
        'description' => L10n::gettext('Provides backup and recovery functionality that covers small environment needs.'),
    ],
    [
        'edition_name' => 'advanced',
        'title' => L10n::gettext('Advanced Edition'),
        'description' => L10n::gettext('Provides backup and recovery functionality designed for big environments. It is dedicated to protect advanced workloads such as Microsoft Exchange and Microsoft SQL cluster, and provides group management and plan management.'),
    ],
    [
        'edition_name' => 'disaster_recovery',
        'title' => L10n::gettext('Disaster Recovery Edition'),
        'description' => L10n::gettext('Provides the disaster recovery functionality along with the advanced backup and recovery functionality. It is designed for companies that have high requirements for the Recovery Time Objective and needs in advanced backup and recovery functionality.'),
    ],
    [
        'edition_name' => 'mixed',
        'title' => L10n::gettext('Mixed Edition'),
    ],
];