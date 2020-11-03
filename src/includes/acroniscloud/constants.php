<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

defined('ACRONIS_CLOUD_SERVICE_NAME') ||
define('ACRONIS_CLOUD_SERVICE_NAME', 'acroniscloud');

defined('ACRONIS_CLOUD_FRIENDLY_NAME') ||
define('ACRONIS_CLOUD_FRIENDLY_NAME', 'Acronis Cyber Cloud');

defined('ACRONIS_CLOUD_WHMCS_DIR') ||
define('ACRONIS_CLOUD_WHMCS_DIR', realpath(__DIR__ . '/../../'));

defined('ACRONIS_CLOUD_INCLUDES_DIR') ||
define('ACRONIS_CLOUD_INCLUDES_DIR', ACRONIS_CLOUD_WHMCS_DIR . '/includes/acroniscloud');

defined('ACRONIS_CLOUD_L10N_DIR') ||
define('ACRONIS_CLOUD_L10N_DIR', ACRONIS_CLOUD_INCLUDES_DIR . '/l10n');

defined('ACRONIS_CLOUD_ADDON_MODULE_DIR') ||
define('ACRONIS_CLOUD_ADDON_MODULE_DIR', ACRONIS_CLOUD_WHMCS_DIR . '/modules/addons/acroniscloud');

defined('ACRONIS_CLOUD_SERVER_MODULE_DIR') ||
define('ACRONIS_CLOUD_SERVER_MODULE_DIR', ACRONIS_CLOUD_WHMCS_DIR . '/modules/servers/acroniscloud');

defined('DIR_BIN') ||
define('DIR_BIN', ACRONIS_CLOUD_INCLUDES_DIR);
