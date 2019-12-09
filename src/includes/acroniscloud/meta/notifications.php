<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

use Acronis\Cloud\Client\Model\Common\Types\UserNotification;

use AcronisCloud\Localization\L10n;

return [
    // Backup
    [
        'type' => UserNotification::BACKUP_INFO,
        'title' => L10n::gettext('Success'),
        'application_type' => 'backup',
    ],
    [
        'type' => UserNotification::BACKUP_ERROR,
        'title' => L10n::gettext('Failure'),
        'application_type' => 'backup',
    ],
    [
        'type' => UserNotification::BACKUP_WARNING,
        'title' => L10n::gettext('Warning'),
        'application_type' => 'backup',
    ],
    [
        'type' => UserNotification::BACKUP_DAILY_REPORT,
        'title' => L10n::gettext('Daily recap about active alerts'),
        'application_type' => 'backup',
    ],

    // Platform
    [
        'type' => UserNotification::QUOTA,
        'title' => L10n::gettext('Quota overuse'),
        'application_type' => 'platform',
    ],
    [
        'type' => UserNotification::REPORTS,
        'title' => L10n::gettext('Scheduled usage reports'),
        'application_type' => 'platform',
    ],
];
