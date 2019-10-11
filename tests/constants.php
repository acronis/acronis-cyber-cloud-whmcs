<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

defined('ACRONIS_CLOUD_TESTS_DIR') ||
define('ACRONIS_CLOUD_TESTS_DIR', realpath(__DIR__));

defined('ACRONIS_CLOUD_TESTS_RESOURCES_DIR') ||
define('ACRONIS_CLOUD_TESTS_RESOURCES_DIR', ACRONIS_CLOUD_TESTS_DIR . '/resources/');

// Re-defined constants from the source code of the module
defined('ACRONIS_CLOUD_L10N_DIR') ||
define('ACRONIS_CLOUD_L10N_DIR', ACRONIS_CLOUD_TESTS_RESOURCES_DIR . 'Service/Localization/');

// Resource used for tests that have create template payload
defined('ACRONIS_CLOUD_TEMPLATE_DATA_DIR') ||
define('ACRONIS_CLOUD_TEMPLATE_DATA_DIR', ACRONIS_CLOUD_TESTS_RESOURCES_DIR . 'lib/Repository/Validation/Template/');

defined('ACRONIS_CLOUD_WHMCS_DIR') ||
define('ACRONIS_CLOUD_WHMCS_DIR', realpath(ACRONIS_CLOUD_TESTS_DIR . '/../src/'));