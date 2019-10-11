<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

use AcronisCloud\Localization\L10n;

return [
    [
        'name' => 'cloud_login',
        'friendly_name' => L10n::gettext('Cloud Login (optional)'),
        'type' => 'text',
        'description' => L10n::gettext('Login name used to log in to {0}. Login can have characters in ranges a-z, A-Z, 0-9, and the special characters "._+-@".',
            [ACRONIS_CLOUD_FRIENDLY_NAME]
        ),
        'validation' => '/^[a-zA-Z0-9\.\-_+@]*$/s',
        'admin_only' => false,
        'required' => false,
        'show_on_order' => true,
        'show_on_invoice' => false,
    ],
    [
        'name' => 'cloud_password',
        'friendly_name' => L10n::gettext('Cloud Password'),
        'type' => 'password',
        'description' => L10n::gettext('Password used to login to {0}. Needs to have both lowercase and uppercase letters, at least one digit and have minimum six characters.',
            [ACRONIS_CLOUD_FRIENDLY_NAME]
        ),
        'validation' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{6,}$/s',
        'admin_only' => false,
        'required' => true,
        'show_on_order' => true,
        'show_on_invoice' => false,
    ],
    [
        'name' => 'tenant_id',
        'friendly_name' => L10n::gettext('Tenant ID'),
        'type' => 'text',
        'description' => '',
        'validation' => '/^$|^[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}$/i',
        'admin_only' => true,
        'required' => false,
        'show_on_order' => false,
        'show_on_invoice' => false,
    ],
    [
        'name' => 'user_id',
        'friendly_name' => L10n::gettext('User ID'),
        'type' => 'text',
        'description' => '',
        'validation' => '/^$|^[\da-f]{8}-[\da-f]{4}-[\da-f]{4}-[\da-f]{4}-[\da-f]{12}$/i',
        'admin_only' => true,
        'required' => false,
        'show_on_order' => false,
        'show_on_invoice' => false,
    ],
];