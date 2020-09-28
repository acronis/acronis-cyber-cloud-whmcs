<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\ModuleMigration;

use Acronis\Cloud\Client\HttpException;
use Acronis\Cloud\Client\Model\Clients\ClientPost;
use Acronis\Cloud\Client\Model\Infra\Infra;
use AcronisCloud\CloudApi\ApiInterface;
use AcronisCloud\CloudApi\AuthorizedApi;
use AcronisCloud\CloudApi\CloudApiTrait;
use AcronisCloud\CloudApi\CloudServerException;
use AcronisCloud\Model\StatusInterface;
use AcronisCloud\Model\WHMCS\Product;
use AcronisCloud\Model\WHMCS\ProductConfigOption;
use AcronisCloud\Model\WHMCS\ProductConfigSubOption;
use AcronisCloud\Model\WHMCS\Server;
use AcronisCloud\Model\WHMCS\Service;
use AcronisCloud\Service\BuildInfo\BuildInfoAwareTrait;
use AcronisCloud\Service\Config\ConfigAwareTrait;
use AcronisCloud\Service\Database\Repository\RepositoryAwareTrait;
use AcronisCloud\Service\Dispatcher\Response\StatusCodeInterface;
use AcronisCloud\Service\Logger\LoggerAwareTrait;
use AcronisCloud\Service\MetaInfo\MetaInfoAwareTrait;
use AcronisCloud\Util\Arr;
use AcronisCloud\Util\MemoizeTrait;
use AcronisCloud\Util\Str;
use AcronisCloud\Util\UomConverter;
use AcronisCloud\Util\WHMCS\ConfigurableOptionHelper;
use WHMCS\Database\Capsule;
use WHMCS\Module\Server\AcronisCloud\Product\CustomFields;
use WHMCS\Module\Server\AcronisCloud\Subscription\RequestParameters\ProductOptions;
use WHMCS\Module\Server\AcronisCloud\Subscription\TenantManager;

class Manager
{
    use BuildInfoAwareTrait,
        CloudApiTrait,
        ConfigAwareTrait,
        LoggerAwareTrait,
        MemoizeTrait,
        MetaInfoAwareTrait,
        RepositoryAwareTrait;

    const ACRONIS_BACKUP_SERVICE = 'AcronisBackupService';
    const MINIMAL_SUPPORTED_VERSION = '1.0.0-177';

    const CUSTOM_FIELD_GROUP_ID = 'GroupID';
    const CUSTOM_FIELD_ADMIN_ID = 'AdminID';
    const CUSTOM_FIELD_LOGIN = 'Login';

    const CONFIG_OPTION_CLOUD_STORAGE_QUOTA = 'configoption2';
    const CONFIG_OPTION_LOCAL_STORAGE_QUOTA = 'configoption6';
    const CONFIG_OPTION_WORKSTATIONS_QUOTA = 'configoption1';
    const CONFIG_OPTION_SERVERS_QUOTA = 'configoption3';
    const CONFIG_OPTION_VIRTUAL_MACHINES_QUOTA = 'configoption5';
    const CONFIG_OPTION_MOBILE_DEVICES_QUOTA = 'configoption7';
    const CONFIG_OPTION_O365_SEATS_QUOTA = 'configoption9';
    const CONFIG_OPTION_WEBSITES_QUOTA = 'configoption13';
    const CONFIG_OPTION_WEB_HOSTING_SERVERS_QUOTA = 'configoption14';

    const CONFIG_OPTION_STORAGE = 'configoption4';
    const CONFIG_OPTION_STORAGE_MEASUREMENT_UNIT = 'configoption12';
    const CONFIG_OPTION_ACCOUNT_TYPE = 'configoption8';
    const CONFIG_OPTION_ADMIN_PERMISSION = 'configoption10';
    const CONFIG_OPTION_ACTIVATION_METHOD = 'configoption11';

    const CONFIG_OPTIONS_NAMES = [
        self::CONFIG_OPTION_WORKSTATIONS_QUOTA => 'Workstations',
        self::CONFIG_OPTION_CLOUD_STORAGE_QUOTA => 'Cloud Storage',
        self::CONFIG_OPTION_SERVERS_QUOTA => 'Servers',
        self::CONFIG_OPTION_STORAGE => 'Storage',
        self::CONFIG_OPTION_VIRTUAL_MACHINES_QUOTA => 'Virtual Machines',
        self::CONFIG_OPTION_LOCAL_STORAGE_QUOTA => 'Local Storage',
        self::CONFIG_OPTION_MOBILE_DEVICES_QUOTA => 'Mobile Devices',
        self::CONFIG_OPTION_ACCOUNT_TYPE => 'Account type',
        self::CONFIG_OPTION_O365_SEATS_QUOTA => 'Microsoft 365 Mailboxes',
        self::CONFIG_OPTION_ADMIN_PERMISSION => 'Administrator permission',
        self::CONFIG_OPTION_ACTIVATION_METHOD => 'Activation method',
        self::CONFIG_OPTION_STORAGE_MEASUREMENT_UNIT => 'Measure storage in',
        self::CONFIG_OPTION_WEBSITES_QUOTA => 'Websites',
        self::CONFIG_OPTION_WEB_HOSTING_SERVERS_QUOTA => 'Web Hosting Servers',
    ];

    const CONFIG_OPTIONS_OFFERING_ITEMS_MAP = [
        self::CONFIG_OPTION_WORKSTATIONS_QUOTA => 'workstations',
        self::CONFIG_OPTION_CLOUD_STORAGE_QUOTA => 'storage',
        self::CONFIG_OPTION_SERVERS_QUOTA => 'servers',
        self::CONFIG_OPTION_VIRTUAL_MACHINES_QUOTA => 'vms',
        self::CONFIG_OPTION_LOCAL_STORAGE_QUOTA => 'local_storage',
        self::CONFIG_OPTION_MOBILE_DEVICES_QUOTA => 'mobiles',
        self::CONFIG_OPTION_O365_SEATS_QUOTA => 'mailboxes',
        self::CONFIG_OPTION_WEBSITES_QUOTA => 'websites',
        self::CONFIG_OPTION_WEB_HOSTING_SERVERS_QUOTA => 'web_hosting_servers',
    ];

    const STORAGE_MEASUREMENT_UNITS = [
        'gb' => UomConverter::GIGABYTES,
        'tb' => UomConverter::TERABYTES,
        'mb' => UomConverter::MEGABYTES,
    ];

    const ACTIVATION_METHOD_EMAIL = 'Send an email message';
    const QUOTA_UNLIMITED = 'unlimited';

    /** @var string[] */
    private $warnings = [];

    /**
     * @return array
     */
    public static function getConfigurableOptionsToOfferingItemsMap()
    {
        static $optionsToOfferingItemsMap = null;
        if (!is_null($optionsToOfferingItemsMap)) {
            return $optionsToOfferingItemsMap;
        }

        $optionsToOfferingItemsMap = [];
        foreach (static::CONFIG_OPTIONS_OFFERING_ITEMS_MAP as $configOption => $offeringItem) {
            $configOptionName = Arr::get(static::CONFIG_OPTIONS_NAMES, $configOption);
            if (!$configOptionName) {
                continue;
            }
            $optionsToOfferingItemsMap[$configOptionName] = $offeringItem;
        }

        return $optionsToOfferingItemsMap;
    }

    /**
     * Returns the list of warnings collected during upgrading
     * @return string[]|null  null is an installation without upgrading
     * @throws \Exception
     */
    public function migrate()
    {
        $logger = $this->getLogger();

        if (!$this->isMigrationNeeded()) {
            $logger->info(
                'No servers or products with the server type "{0}" are found, so there are no items to migrate from the earlier module version to the new version.',
                [static::ACRONIS_BACKUP_SERVICE]
            );
            return null;
        }

        $this->checkPreviousVersion();
        $this->checkLoggingIsEnabled();

        $logger->info('Start upgrading the module.');

        $this->warnings = [];
        Capsule::transaction(function () {
            $this->migrateServers();
            $this->migrateProducts();
        });

        $this->dropModuleV1Table();

        $logger->info('Upgrade was completed successfully. Warnings count: {0}', [count($this->warnings)]);

        return $this->warnings;
    }

    private function warning($message)
    {
        $this->getLogger()->warning($message);
        $this->warnings[] = $message;
    }

    private function migrateServers()
    {
        $logger = $this->getLogger();
        /** @var Server[] $servers */
        $servers = Server::where('type', static::ACRONIS_BACKUP_SERVICE)->get();
        foreach ($servers as $server) {
            $logger->info('Upgrade server {0}.', [$server->getId()]);

            $cloudApi = $this->getCloudApiForServer($server);
            if ($cloudApi->getGrantType() !== AuthorizedApi::GRANT_TYPE_CLIENT_CREDENTIALS) {
                $this->convertServerCredentials($server);
            }
            $cloudApi->getRootTenantId();

            $server->type = ACRONIS_CLOUD_SERVICE_NAME;

            $server->save();

            $logger->info('Server {0} was upgraded successfully.', [$server->getId()]);
        }
    }

    /**
     * @param $tenantId
     * @return ClientPost
     */
    private function createClientPost($tenantId)
    {
        $client = new ClientPost();
        $client->setType('agent');
        $client->setTenantId($tenantId);
        $client->setTokenEndpointAuthMethod('client_secret_basic');

        return $client;
    }

    /**
     * @param Server $server
     * @throws HttpException
     * @throws \Acronis\Cloud\Client\ApiException
     * @throws \Acronis\Cloud\Client\IOException
     */
    private function convertServerCredentials($server)
    {
        try {
            $this->getLogger()->info('Converting credentials for server "{0}".', [$server->getName()]);
            $cloudApi = $this->createCloudApiInstance($server);
            $tenantId = $cloudApi->getRootTenantId();
            $clientPost = $this->createClientPost($tenantId);
            $client = $cloudApi->createClient($clientPost);
            $cloudApi->setClientCredentials($client->getClientId(), $client->getClientSecret());
            $cloudApiUrl = parse_url($cloudApi->getUrl(), PHP_URL_HOST);
            $server
                ->setUsername($client->getClientId())
                ->setPassword($client->getClientSecret())
                ->setAccessHash(AuthorizedApi::GRANT_TYPE_CLIENT_CREDENTIALS)
                ->setHostname($cloudApiUrl)
                ->save();
        } catch (\Exception $e) {
            $this->warning(Str::format(
                'Could not automatically convert the user credentials for the server "{0}". Open this server and save it again without any changes for converting them to the new format.',
                $server->getName()
            ));
        }
    }

    private function dropModuleV1Table()
    {
        $tableName = static::ACRONIS_BACKUP_SERVICE . '_modulevars';
        $this->getLogger()->info('Delete the table "{0}".', [$tableName]);
        try {
            Capsule::schema()->dropIfExists($tableName);
        } catch (\Exception $e) {
            $this->warning(Str::format(
                'Cannot remove the table "{0}" due to the error "{1}". Please remove it manually.',
                $tableName, $e->getMessage()
            ));
        }
    }

    /**
     * @throws \Exception
     */
    private function migrateProducts()
    {
        $logger = $this->getLogger();
        /** @var Product[] $products */
        $products = Product::where('servertype', static::ACRONIS_BACKUP_SERVICE)->get();
        foreach ($products as $product) {
            $logger->info('Upgrade product {0}.', [$product->getId()]);

            $this->migrateCustomFields($product);
            $this->migrateConfigurableOptions($product);

            $templateId = $this->createTemplateForProduct($product);
            $activationMethod = $this->resolveActivationMethod($product);

            $this->resetOptions($product);
            $product->configoption1 = $templateId;
            $product->configoption2 = $activationMethod;
            $product->servertype = ACRONIS_CLOUD_SERVICE_NAME;
            $product->save();

            $logger->info('Product {0} was upgraded successfully.', [$product->getId()]);
        }
    }

    /**
     * @param Product $product
     * @return int
     * @throws \Exception
     */
    private function createTemplateForProduct(Product $product)
    {
        $logger = $this->getLogger();
        $logger->info('Creating a template for product {0}.', [$product->getId()]);

        /** @var Server $server */
        $server = $this->resolveServer($product);
        $tenantKind = $this->resolveTenantKind($product);
        $offeringItems = $this->resolveOfferingItems($product);
        $userRole = $this->resolveUserRole($product);
        $template = [
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'server_id' => $server->getId(),
            'tenant_kind' => $tenantKind,
            'user_role' => $userRole,
            'applications' => [
                [
                    'type' => 'backup',
                    'status' => StatusInterface::STATUS_ACTIVE,
                    'editions' => [
                        [
                            'name' => 'standard',
                            'status' => StatusInterface::STATUS_ACTIVE,
                        ],
                    ],
                    'offering_items' => $offeringItems,
                ],
            ],
        ];

        $templateId = $this->getRepository()->getTemplateRepository()->create($template);

        $logger->info('Template {0} was successfully created for product {1}.', [$templateId, $product->getId()]);

        return $templateId;
    }

    /**
     * @param Product $product
     * @throws \Exception
     */
    private function migrateCustomFields(Product $product)
    {
        $logger = $this->getLogger();

        $productId = $product->getId();
        $logger->info('Update custom fields for product {0}.', [$productId]);

        /** @var Service[] $services */
        $services = Service::where(Service::COLUMN_PRODUCT_ID, $productId)->get();
        foreach ($services as $service) {
            $serviceId = $service->getId();
            $logger->info('Convert custom fields for service {0}.', [$serviceId]);

            $server = $service->cloudServer;

            $groupId = $this->getAndResetFieldValue($productId, $serviceId, static::CUSTOM_FIELD_GROUP_ID);
            $adminId = $this->getAndResetFieldValue($productId, $serviceId, static::CUSTOM_FIELD_ADMIN_ID);
            $this->getAndResetFieldValue($productId, $serviceId, static::CUSTOM_FIELD_LOGIN); // rest only

            if (!$groupId) {
                $this->warning(Str::format(
                    'There is no group ID specified for product %s.',
                    $productId
                ));
                continue;
            }

            $customFieldsManager = new CustomFields($productId, $serviceId);
            $customFieldsManager->createField(CustomFields::FIELD_NAME_TENANT_ID);
            $customFieldsManager->createField(CustomFields::FIELD_NAME_USER_ID);
            $customFieldsManager->createField(CustomFields::FIELD_NAME_CLOUD_LOGIN);

            try {
                $group = $this->getGroupInfo($server, $groupId);
            } catch (HttpException $e) {
                if ($e->getCode() === StatusCodeInterface::HTTP_NOT_FOUND) {
                    throw new \Exception(Str::format(
                        'Cannot find group %s at server %s. Please check server ID for service %s or empty/update custom field "%s" if the group was manually deleted.',
                        $groupId, $server->getId(), $serviceId, static::CUSTOM_FIELD_GROUP_ID
                    ));
                }
                throw $e;
            }
            $customFieldsManager->setTenantId($group->uuid);

            if (!$adminId) {
                $this->warning(Str::format(
                    'There is no admin ID specified for product %s.',
                    $productId
                ));
                continue;
            }

            try {
                $user = $this->getUserInfo($server, $group, $adminId);
            } catch (HttpException $e) {
                if ($e->getCode() === StatusCodeInterface::HTTP_NOT_FOUND) {
                    throw new \Exception(Str::format(
                        'Cannot find user %s at server %s. Please check server ID for service %s or empty/update custom field "%s" if the user was manually deleted.',
                        $adminId, $server->getId(), $serviceId, static::CUSTOM_FIELD_ADMIN_ID
                    ));
                }
                throw $e;
            }

            $customFieldsManager->setUserId($user->uuid);
            $customFieldsManager->setCloudLogin($user->login);
        }

        $this->removeCustomField($productId, static::CUSTOM_FIELD_GROUP_ID);
        $this->removeCustomField($productId, static::CUSTOM_FIELD_ADMIN_ID);
        $this->removeCustomField($productId, static::CUSTOM_FIELD_LOGIN);
    }

    /**
     * @throws \Exception
     */
    private function checkPreviousVersion()
    {
        $versionFilePath = ACRONIS_CLOUD_WHMCS_DIR . '/includes/Acronis/Vers.ion';
        $buildInfo = new ModuleV1BuildInfoParser($versionFilePath);

        if (version_compare($buildInfo->getPackageVersion(), static::MINIMAL_SUPPORTED_VERSION, '<')) {
            throw new \Exception(Str::format(
                'Direct upgrade from the current version %s to the new version %s is not supported. Please upgrade the module to the version %s first.',
                $buildInfo->getPackageVersion(), $this->getBuildInfo()->getPackageVersion(), static::MINIMAL_SUPPORTED_VERSION
            ));
        }
    }


    /**
     * @throws \Exception
     */
    private function checkLoggingIsEnabled()
    {
        if (!$this->getConfig()->getLoggerSettings()->getEnabled()) {
            throw new \Exception('Logging is not enabled for the module. To enable it, please refer to the Upgrade section in the module documentation.');
        }
    }

    /**
     * @return boolean
     */
    private function isMigrationNeeded()
    {
        return Server::where('type', static::ACRONIS_BACKUP_SERVICE)->count()
            || Product::where('servertype', static::ACRONIS_BACKUP_SERVICE)->count();
    }

    /**
     * @param Product $product
     * @throws \Exception
     */
    private function migrateConfigurableOptions(Product $product)
    {
        $metaInfo = $this->getMetaInfo();
        $optionsToOfferingItemsMap = static::getConfigurableOptionsToOfferingItemsMap();
        $tenantKind = $this->resolveTenantKind($product);

        /** @var ProductConfigOption[] $options */
        $options = ProductConfigOption::join('tblproductconfiglinks', 'tblproductconfiglinks.gid', '=',
            'tblproductconfigoptions.gid')
            ->select('tblproductconfigoptions.*')
            ->where('tblproductconfiglinks.pid', $product->getId())
            ->get();

        foreach ($options as $option) {
            $offeringItemName = Arr::get($optionsToOfferingItemsMap, $option->optionname);
            if (!$offeringItemName) {
                continue;
            }

            $offeringItemMeta = $metaInfo->getOfferingItemMeta($offeringItemName);
            if (!$offeringItemMeta) {
                throw new \Exception(Str::format(
                    'Internal error. Unsupported offering item "%s" is used for configurable option "%s" in group %s.',
                    $offeringItemName, $option->optionname, $option->gid
                ));
            }

            $configurableOptionMeta = $offeringItemMeta->getConfigurableOption();

            $measurementUnit = $offeringItemMeta->getMeasurementUnit() === UomConverter::BYTES
                ? $this->resolveStorageMeasurementUnitForConfigurableOption($option)
                : $configurableOptionMeta->getMeasurementUnit();

            $option->optionname = ConfigurableOptionHelper::getFullName(
                $configurableOptionMeta->getFriendlyName(),
                $offeringItemName,
                $measurementUnit
            );

            $option->save();

            if ($tenantKind === ApiInterface::TENANT_KIND_PARTNER && $offeringItemName === 'storage') {
                $this->warning(Str::format(
                    'Configurable option "%s" from group %s cannot be used for product %s without infrastructure component ID specified. Please update or remove the configurable option.',
                    $option->optionname, $option->gid, $product->getId()
                ));
            }
        }
    }

    private function resetOptions(Product $product)
    {
        //reset all configoptions for the product
        for ($i = 1; $i <= 24; $i++) {
            $product->setAttribute('configoption' . $i, '');
        }
    }

    /**
     * @param Product $product
     * @return array
     * @throws \Exception
     */
    private function resolveOfferingItems(Product $product)
    {
        $server = $this->resolveServer($product);
        $tenantKind = $this->resolveTenantKind($product);
        $storageMeasurementUnit = $this->resolveStorageMeasurementUnit($product);

        $cloudApi = $this->getCloudApiForServer($server);
        $rootTenantId = $cloudApi->getRootTenantId();

        $metaInfo = $this->getMetaInfo();
        $offeringItems = [];
        foreach (static::CONFIG_OPTIONS_OFFERING_ITEMS_MAP as $configOption => $offeringItemName) {
            $offeringItemMeta = $metaInfo->getOfferingItemMeta($offeringItemName);
            if (!$offeringItemMeta) {
                throw new \Exception(Str::format(
                    'Internal error. Unsupported offering item "%s" is used for module setting "%s".',
                    $offeringItemName, $configOption
                ));
            }

            if (!$cloudApi->isOfferingItemAvailableForChild($rootTenantId, $tenantKind, $offeringItemName)) {
                $this->warning(Str::format(
                    'Offering item "%s" (%s) is not enabled for your root tenant "%s". This offering item will be disabled in the new template related product %s.',
                    $offeringItemMeta->getOfferingItemFriendlyName(),
                    $offeringItemName,
                    $rootTenantId,
                    $product->getId()
                ));
                continue;
            }

            $quotaValue = $this->resolveQuotaValue($product, $configOption);
            if ($quotaValue === 0) {
                continue;
            }

            $measurementUnit = $offeringItemMeta->getMeasurementUnit();
            if ($measurementUnit === UomConverter::BYTES) {
                $quotaValue = UomConverter::convert($quotaValue, $storageMeasurementUnit, UomConverter::BYTES);
            }

            if ($offeringItemName === 'storage') {
                $infras = $this->resolveStorageInfras($product);
                foreach ($infras as $infra) {
                    $offeringItems[] = [
                        'status' => StatusInterface::STATUS_ACTIVE,
                        'name' => $offeringItemName,
                        'quota_value' => $quotaValue,
                        'measurement_unit' => $measurementUnit,
                        'infra_id' => $infra->getId(),
                    ];
                }

                if ($cloudApi->isOfferingItemAvailableForChild($rootTenantId, $tenantKind, 'child_storages')) {
                    $offeringItems[] = [
                        'status' => StatusInterface::STATUS_ACTIVE,
                        'name' => 'child_storages',
                        'quota_value' => null,
                        'measurement_unit' => UomConverter::FEATURE,
                    ];
                }
            } elseif ($offeringItemName === 'mailboxes') {
                $offeringItems[] = [
                    'status' => StatusInterface::STATUS_ACTIVE,
                    'name' => $offeringItemName,
                    'quota_value' => $quotaValue,
                    'measurement_unit' => $measurementUnit,
                ];

                if ($cloudApi->isOfferingItemAvailableForChild($rootTenantId, $tenantKind, 'o365_mailboxes')) {
                    $offeringItems[] = [
                        'status' => StatusInterface::STATUS_ACTIVE,
                        'name' => 'o365_mailboxes',
                        'quota_value' => null,
                        'measurement_unit' => UomConverter::FEATURE,
                    ];
                }

                if ($cloudApi->isOfferingItemAvailableForChild($rootTenantId, $tenantKind, 'o365_mailboxes')) {
                    $offeringItems[] = [
                        'status' => StatusInterface::STATUS_ACTIVE,
                        'name' => 'o365_onedrive',
                        'quota_value' => null,
                        'measurement_unit' => UomConverter::FEATURE,
                    ];
                }

                if ($cloudApi->isOfferingItemAvailableForChild($rootTenantId, $tenantKind, 'o365_sharepoint_sites')) {
                    $offeringItems[] = [
                        'status' => StatusInterface::STATUS_ACTIVE,
                        'name' => 'o365_sharepoint_sites',
                        'quota_value' => null,
                        'measurement_unit' => UomConverter::QUANTITY,
                    ];
                }
            } else {
                $offeringItems[] = [
                    'status' => StatusInterface::STATUS_ACTIVE,
                    'name' => $offeringItemName,
                    'quota_value' => $quotaValue,
                    'measurement_unit' => $measurementUnit,
                ];
            }
        }

        return $offeringItems;
    }

    /**
     * @param Product $product
     * @return Infra
     */
    private function resolveStorageInfras(Product $product)
    {
        return $this->memoize(function () use ($product) {
            $server = $this->resolveServer($product);
            $cloudApi = $this->getCloudApiForServer($server);
            $infras = $cloudApi->getRootTenantInfras();
            // ignore infras which doesn't have capability backup
            $infras = array_filter($infras, function ($infra) {
                return in_array('backup', $infra->getCapabilities());
            });
            $tenantKind = $this->resolveTenantKind($product);
            if ($tenantKind === ApiInterface::TENANT_KIND_PARTNER) {
                // return all infras for partner tenants
                return $infras;
            }

            $infraName = $this->resolveStorageInfraName($product);
            foreach ($infras as $infra) {
                if ($infra->getName() === $infraName) {
                    return [$infra];
                }
            }

            throw new \Exception(Str::format(
                'Cannot find an infrastructure component with the name "%s" in %s. Please open the product "%s" and update the module setting "%s".',
                $infraName,
                ACRONIS_CLOUD_FRIENDLY_NAME,
                $product->getName(),
                Arr::get(static::CONFIG_OPTIONS_NAMES, static::CONFIG_OPTION_STORAGE)
            ));
        }, $product->getId());
    }

    /**
     * @param Product $product
     * @return string
     * @throws \Exception
     */
    private function resolveServerName(Product $product)
    {
        $configOptionValue = $product->getAttribute(static::CONFIG_OPTION_STORAGE);
        // Example: [DC2] 10.248.53.83:44445 (ci-qa3.msp.ru.corp.acronis.com)
        if (!preg_match('/^\[(.*?)\]\s*/', $configOptionValue, $matches)) {
            throw new \Exception(Str::format(
                'Invalid infrastructure component name. Please update module setting "%s" in product %s.',
                Arr::get(static::CONFIG_OPTIONS_NAMES, static::CONFIG_OPTION_STORAGE),
                $product->getId()
            ));
        }

        return $matches[1];
    }

    /**
     * @param Product $product
     * @return string
     * @throws \Exception
     */
    private function resolveStorageInfraName(Product $product)
    {
        $configOption = $product->getAttribute(static::CONFIG_OPTION_STORAGE);
        // Example: [DC2] 10.248.53.83:44445 (ci-qa3.msp.ru.corp.acronis.com)
        if (!preg_match('/^\[(?:.*?)\]\s+(.*)\s+\(/', $configOption, $matches)) {
            throw new \Exception(Str::format(
                'Invalid data center name. Please update module setting "%s" in product %s.',
                Arr::get(static::CONFIG_OPTIONS_NAMES, static::CONFIG_OPTION_STORAGE),
                $product->getId()
            ));
        }

        return $matches[1];
    }

    /**
     * @param Product $product
     * @return Server
     */
    private function resolveServer(Product $product)
    {
        return $this->memoize(function () use ($product) {
            $serverName = $this->resolveServerName($product);
            $servers = $this->getRepository()->getAcronisServerRepository()->all();
            foreach ($servers as $server) {
                if ($server->name === $serverName) {
                    return $server;
                }
            }
            throw new \Exception(Str::format(
                'Cannot find a server with name "%s". Please update module setting "%s" in product %s.',
                $serverName,
                Arr::get(static::CONFIG_OPTIONS_NAMES, static::CONFIG_OPTION_STORAGE),
                $product->getId()
            ));
        }, $product->getId());
    }

    /**
     * @param Product $product
     * @return string
     * @throws \Exception
     */
    private function resolveTenantKind(Product $product)
    {
        $tenantKind = strtolower(trim($product->getAttribute(static::CONFIG_OPTION_ACCOUNT_TYPE)));
        if (!in_array($tenantKind, [
            ApiInterface::TENANT_KIND_PARTNER,
            ApiInterface::TENANT_KIND_CUSTOMER,
        ])) {
            throw new \Exception(Str::format(
                'Unsupported account type "%s" is specified for module setting "%s". Please update the setting in product %s.',
                $product->getAttribute(static::CONFIG_OPTION_ACCOUNT_TYPE),
                Arr::get(static::CONFIG_OPTIONS_NAMES, static::CONFIG_OPTION_ACCOUNT_TYPE),
                $product->getId()
            ));
        }

        return $tenantKind;
    }

    /**
     * @param $productId
     * @param $serviceId
     * @param $fieldName
     * @return string
     * @throws \Exception
     */
    private function getAndResetFieldValue($productId, $serviceId, $fieldName)
    {
        $valuesRepository = $this->getRepository()
            ->getCustomFieldsValuesRepository();
        $fieldsRepository = $this->getRepository()
            ->getCustomFieldsRepository();

        $productField = $fieldsRepository->getProductField($productId, $fieldName);
        if (!$productField) {
            return null;
        }
        $valueField = $valuesRepository->getCustomFieldServiceValue($productField->id, $serviceId);
        if (!$valueField) {
            return null;
        }
        $valuesRepository->deleteCustomFieldServiceValue($productField->id, $serviceId);

        return $valueField->value;
    }

    /**
     * @param $productId
     * @param $fieldName
     * @throws \Exception
     */
    private function removeCustomField($productId, $fieldName)
    {
        $fieldsRepository = $this->getRepository()
            ->getCustomFieldsRepository();

        $field = $fieldsRepository->getProductField($productId, $fieldName);
        if (!$field) {
            return;
        }
        $fieldsRepository->delete($field->id);
    }

    /**
     * @param Product $product
     * @return string
     * @throws \Exception
     */
    private function resolveUserRole(Product $product)
    {
        $tenantKind = $this->resolveTenantKind($product);
        if ($tenantKind === ApiInterface::TENANT_KIND_PARTNER) {
            return TenantManager::ROLE_ADMIN;
        }

        $permission = strtolower(trim($product->getAttribute(static::CONFIG_OPTION_ADMIN_PERMISSION)));
        if (!in_array($permission, ['enabled', 'disabled'])) {
            $this->warning(Str::format(
                'Invalid value "%s" is specified for module setting "%s". Administrator privileges will be disabled for product %s.',
                $product->getAttribute(static::CONFIG_OPTION_ADMIN_PERMISSION),
                Arr::get(static::CONFIG_OPTIONS_NAMES, static::CONFIG_OPTION_ADMIN_PERMISSION),
                $product->getId()
            ));

            return TenantManager::ROLE_USER;
        }

        return $permission === 'enabled'
            ? TenantManager::ROLE_ADMIN
            : TenantManager::ROLE_USER;
    }

    /**
     * @param Server $server
     * @param int $groupId
     * @return object
     * @throws CloudServerException
     */
    private function getGroupInfo($server, $groupId)
    {
        $cloudApi = $this->getCloudApiForServer($server);
        $children = $this->memoize(function () use ($cloudApi) {
            $children = $cloudApi->getRootGroupChildrenV1();

            return Arr::map(
                Arr::get($children, 'items', []),
                'id',
                function ($group) {
                    return $group;
                }
            );
        }, $server->getId());

        $group = Arr::get($children, $groupId);
        if (!$group) {
            $group = $cloudApi->getGroupV1($groupId);
        }

        return $group;
    }

    /**
     * @param Server $server
     * @param object $group
     * @param int $userId
     * @return object
     * @throws CloudServerException
     */
    private function getUserInfo($server, $group, $userId)
    {
        $cloudApi = $this->getCloudApiForServer($server);

        return $group->kind === 31 // tenant is partner
            ? $cloudApi->getAdminV1($userId)
            : $cloudApi->getUserV1($userId);
    }

    private function resolveActivationMethod(Product $product)
    {
        $activationMethod = $product->getAttribute(static::CONFIG_OPTION_ACTIVATION_METHOD);
        if ($activationMethod !== static::ACTIVATION_METHOD_EMAIL) {
            $this->warning(Str::format(
                'Activation method "%s" will be replaced with "%s" for product %s.',
                $activationMethod, static::ACTIVATION_METHOD_EMAIL, $product->getId()
            ));
        }

        return ProductOptions::ACTIVATION_METHOD_EMAIL;
    }

    /**
     * @param Product $product
     * @param string $configOption
     * @return int|null null - unlimited
     * @throws \Exception
     */
    private function resolveQuotaValue(Product $product, $configOption)
    {
        $configOptionValue = strtolower(trim($product->{$configOption}));
        if ($configOptionValue === static::QUOTA_UNLIMITED) {
            return null; // unlimited
        }

        if ($configOptionValue === '') {
            return 0;
        }

        $quotaValue = intval($configOptionValue);
        if (filter_var($configOptionValue, FILTER_VALIDATE_INT) === false) {
            $this->warning(Str::format(
                'Value "%s" of module setting "%s" was converted to %s. Please define the value in template for product %s.',
                $product->{$configOption},
                Arr::get(static::CONFIG_OPTIONS_NAMES, $configOption),
                $quotaValue,
                $product->getId()
            ));
        }

        if ($quotaValue < 0) {
            return null; // unlimited
        }

        return $quotaValue;
    }

    /**
     * @param Product $product
     * @return mixed
     * @throws \Exception
     */
    private function resolveStorageMeasurementUnit(Product $product)
    {
        $unit = strtolower(trim($product->getAttribute(static::CONFIG_OPTION_STORAGE_MEASUREMENT_UNIT)));
        if (!in_array($unit, array_keys(static::STORAGE_MEASUREMENT_UNITS))) {
            $this->warning(Str::format(
                'The unsupported unit of measure "%s" was specified for the module setting "%s" in the product "%s". It was changed to GB during the upgrade.',
                $product->getAttribute(static::CONFIG_OPTION_STORAGE_MEASUREMENT_UNIT),
                Arr::get(static::CONFIG_OPTIONS_NAMES, static::CONFIG_OPTION_STORAGE_MEASUREMENT_UNIT),
                $product->getName()
            ));
        }

        return Arr::get(static::STORAGE_MEASUREMENT_UNITS, $unit, UomConverter::GIGABYTES);
    }

    /**
     * @param ProductConfigOption $option
     * @return mixed
     * @throws \Exception
     */
    private function resolveStorageMeasurementUnitForConfigurableOption(ProductConfigOption $option)
    {
        /** @var ProductConfigSubOption $subOption */
        $subOption = $option->subOptions()->first();
        $unit = strtolower(trim($subOption->optionname));

        if (!in_array($unit, array_keys(static::STORAGE_MEASUREMENT_UNITS))) {
            $this->warning(Str::format(
                'The unsupported unit of measure "%s" was specified for the configurable option "%s" in the group "%s". It was changed to GB during the upgrade.',
                $subOption->optionname,
                $option->optionname,
                $option->group->getName()
            ));
        }

        return Arr::get(static::STORAGE_MEASUREMENT_UNITS, $unit, UomConverter::GIGABYTES);
    }
}