<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

use AcronisCloud\Service\Dispatcher\Request;
use AcronisCloud\Service\Dispatcher\Router\ModuleActionRouter;
use AcronisCloud\Service\Dispatcher\Router\ModuleRouter;
use AcronisCloud\Service\Dispatcher\Router\QueryParameterRouter;
use WHMCS\Module\Addon\AcronisCloud\Controller\AddonPage;
use WHMCS\Module\Addon\AcronisCloud\Controller\AddonSettings;
use WHMCS\Module\Addon\AcronisCloud\Controller\ConfigurableOptions;
use WHMCS\Module\Addon\AcronisCloud\Controller\ServiceTemplate;
use WHMCS\Module\Server\AcronisCloud\Controller\ClientAreaApi;
use WHMCS\Module\Server\AcronisCloud\Controller\ClientAreaPage;
use WHMCS\Module\Server\AcronisCloud\Controller\ContactInfo;
use WHMCS\Module\Server\AcronisCloud\Controller\L10n;
use WHMCS\Module\Server\AcronisCloud\Controller\CustomHeaderOutput;
use WHMCS\Module\Server\AcronisCloud\Controller\Product;
use WHMCS\Module\Server\AcronisCloud\Controller\Server;
use WHMCS\Module\Server\AcronisCloud\Controller\Subscription;

return new ModuleRouter([
    'addons' => new ModuleActionRouter([
        'config' => [AddonSettings::class, 'getConfig'],
        'activate' => [AddonSettings::class, 'activate'],
        'deactivate' => [AddonSettings::class, 'deactivate'],
        'output' => new QueryParameterRouter([
            'index' => [AddonPage::class, 'index', Request::GET],
            'create_template' => [ServiceTemplate::class, 'create', Request::POST],
            'update_template' => [ServiceTemplate::class, 'update', Request::POST],
            'delete_template' => [ServiceTemplate::class, 'delete', Request::POST],
            'get_template' => [ServiceTemplate::class, 'getTemplate', Request::GET],
            'list_templates' => [ServiceTemplate::class, 'listTemplates', Request::GET],
            'list_servers' => [ServiceTemplate::class, 'listServers', Request::GET],
            'get_applications' => [ServiceTemplate::class, 'getServerApplications', Request::GET],
            'get_offering_items' => [ServiceTemplate::class, 'getServerOfferingItems', Request::GET],
            'get_locations' => [ServiceTemplate::class, 'getLocations', Request::GET],
            'create_options' => [ConfigurableOptions::class, 'create', Request::POST],
            'l10n' => [L10n::class, 'getL10n', Request::GET],
        ], 'index', 'action', 'index'),
    ]),
    'servers' => new ModuleActionRouter([
        'MetaData' => [Product::class, 'getMetaData'],
        'ConfigOptions' => [Product::class, 'getConfigOptions'],

        'CreateAccount' => [Subscription::class, 'createOrUpdate'],
        'ChangePackage' => [Subscription::class, 'createOrUpdate'],
        'SuspendAccount' => [Subscription::class, 'suspend'],
        'UnsuspendAccount' => [Subscription::class, 'unsuspend'],
        'TerminateAccount' => [Subscription::class, 'terminate'],

        'ClientAreaCustomButtonArray' => [ClientAreaPage::class, 'getActions'],
        'ClientAreaManagement' => [ClientAreaPage::class, 'index'],
        'ServiceSingleSignOn' => [ClientAreaApi::class, 'singleSignOn'],
        'TestConnection' => [Server::class, 'testConnection'],
        'ClientArea' => new QueryParameterRouter([
            'index' => [ClientAreaPage::class, 'index', Request::GET],
            'get_details' => [ClientAreaApi::class, 'getDetails', Request::GET],
            'update_details' => [ClientAreaApi::class, 'updateDetails', Request::POST],
            'get_subscription' => [ClientAreaApi::class, 'getSubscription', Request::GET],
            'get_usages' => [ClientAreaApi::class, 'getUsages', Request::GET],
            'l10n' => [L10n::class, 'getL10n', Request::GET],
        ], 'index', 'a'),
    ]),
    'hooks' => new ModuleActionRouter([
        'ServerAdd' => [Server::class, 'updateServerInfo'],
        'ServerEdit' => [Server::class, 'updateServerInfo'],
        'ServerDelete' => [Server::class, 'deleteInternalTag'],
        'ClientEdit' => [ContactInfo::class, 'updateTenants'],
        'ProductEdit' => [Product::class, 'setupCustomFields'],
        'ServiceDelete' => [Subscription::class, 'terminate'],
        'OrderProductUpgradeOverride' => [Product::class, 'beforeUpgrade'],
        'AdminAreaHeaderOutput' => [Server::class, 'adminOutput'],
        'ClientAreaHeaderOutput' => [CustomHeaderOutput::class, 'clientOutput'],
    ]),
]);
