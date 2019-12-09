<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\CloudApi;

use Acronis\Cloud\Client\ApiException;
use Acronis\Cloud\Client\HttpException;
use Acronis\Cloud\Client\IOException;
use Acronis\Cloud\Client\Model\AccessPolicies\AccessPolicy;
use Acronis\Cloud\Client\Model\Applications\Application;
use Acronis\Cloud\Client\Model\Applications\RoleListItems;
use Acronis\Cloud\Client\Model\Clients\ClientPost;
use Acronis\Cloud\Client\Model\Clients\ClientPostResult;
use Acronis\Cloud\Client\Model\Infra\Infra;
use Acronis\Cloud\Client\Model\Locations\Location;
use Acronis\Cloud\Client\Model\OfferingItems\OfferingItemOutput;
use Acronis\Cloud\Client\Model\Pricing\TenantPricingSettings;
use Acronis\Cloud\Client\Model\Pricing\TenantPricingSettingsPut;
use Acronis\Cloud\Client\Model\Reports\Report;
use Acronis\Cloud\Client\Model\Reports\Stored\StoredReportParamsItems;
use Acronis\Cloud\Client\Model\Tenants\OfferingItem;
use Acronis\Cloud\Client\Model\Tenants\Tenant;
use Acronis\Cloud\Client\Model\Tenants\TenantPost;
use Acronis\Cloud\Client\Model\Tenants\TenantPut;
use Acronis\Cloud\Client\Model\Usages\UsageOutput;
use Acronis\Cloud\Client\Model\Users\User;
use Acronis\Cloud\Client\Model\Users\UserPost;
use Acronis\Cloud\Client\Model\Users\UserPut;
use Acronis\Cloud\Client\Model\Version\Version;

interface ApiInterface
{
    const APPLICATION_TYPE_PLATFORM = 'platform';

    const OFFERING_ITEM_TYPE_COUNT = 'count';
    const OFFERING_ITEM_TYPE_INFRA = 'infra';
    const OFFERING_ITEM_TYPE_FEATURE = 'feature';

    const OFFERING_ITEM_STATUS_ACTIVE = 1;
    const OFFERING_ITEM_STATUS_INACTIVE = 0;

    const TENANT_KIND_CUSTOMER = 'customer';
    const TENANT_KIND_PARTNER = 'partner';

    const UNLIMITED_OFFERING_ITEM_VALUE = null;
    const NULL_UUID = '00000000-0000-0000-0000-000000000000';

    const EDITION_ANY = '*';

    /**
     * @return string
     */
    public function getUrl();

    /**
     * @return string
     */
    public function getLogin();

    /**
     * @return string
     */
    public function getPassword();

    /**
     * @return string
     */
    public function getGrantType();

    /**
     * @return void
     */
    public function resetAccessCache();

    /* ====== Methods for the root tenant ====== */

    /**
     * @return string
     */
    public function getRootTenantId();

    /**
     * @return OfferingItemOutput[]
     */
    public function getRootTenantOfferingItems();

    /**
     * @param $offeringItemName
     * @param $offeringItemInfraId
     * @return OfferingItemOutput
     */
    public function getRootTenantOfferingItem($offeringItemName, $offeringItemInfraId = null);

    /**
     * @return Infra[]
     */
    public function getRootTenantInfras();

    /**
     * @return Location[]
     */
    public function getRootTenantLocations();

    /**
     * @param string $infraId
     * @return bool
     */
    public function isInfraAvailableForRootTenant($infraId);

    /* ====== Users API ====== */

    /**
     * Returns True if the login exists otherwise False
     *
     * @param string $login
     * @return bool
     */
    public function checkLogin($login);

    /**
     * @param string $login
     * @param string $email
     * @param string $password
     */
    public function activateUser($login, $email, $password);

    /**
     * @param string $login
     * @param string $email
     * @return object
     */
    public function sendActivationEmail($login, $email);

    /**
     * @param string $login
     * @param string $email
     * @return object
     */
    public function getActivationEmail($login, $email);

    /**
     * @return Version
     */
    public function getVersions();

    /**
     * @param string $token
     * @param string $password
     * @return object
     */
    public function activateToken($token, $password);

    /**
     * @param string $userId
     * @return User
     */
    public function getUser($userId);

    /**
     * @param UserPost $body
     * @return User
     */
    public function createUser(UserPost $body);

    /**
     * @param string $userId
     * @param UserPut $body
     * @return User
     */
    public function updateUser($userId, UserPut $body);

    /**
     * @param $userId
     * @param AccessPolicy[] $accessPolicies
     * @return AccessPolicy[]
     */
    public function updateUserAccessPolicies($userId, array $accessPolicies);

    /* ====== Clients API ====== */

    /**
     * @param ClientPost $client
     * @return ClientPostResult
     * @throws ApiException
     * @throws HttpException
     */
    public function createClient(ClientPost $client);

    /**
     * @param $clientId
     * @param $clientSecret
     * @throws HttpException
     * @throws IOException
     */
    public function setClientCredentials($clientId, $clientSecret);

    /* ====== Methods to manage tenants ====== */

    /**
     * @param string $tenantId
     * @return Tenant
     */
    public function getTenant($tenantId);

    /**
     * @param string[] $uuids
     * @return Tenant[]
     */
    public function fetchTenants(array $uuids);

    /**
     * @param TenantPost $body
     * @return Tenant
     */
    public function createTenant(TenantPost $body);

    /**
     * @param string $tenantId
     * @param TenantPut $body
     * @return Tenant
     */
    public function updateTenant($tenantId, TenantPut $body);

    /**
     * @param string $tenantId
     * @param int $version
     */
    public function deleteTenant($tenantId, $version);

    /**
     * @param string $tenantId
     * @param string|null $order
     * @return string[]
     */
    public function getTenantChildrenUuids($tenantId, $order = null);

    /**
     * @param string $tenantId
     * @param string|null $order
     * @return Tenant[]
     */
    public function fetchTenantChildren($tenantId, $order = null);

    /**
     * @param string $tenantId
     * @return TenantPricingSettings
     */
    public function getTenantPricing($tenantId);

    /**
     * @param string $tenantId
     * @param TenantPricingSettingsPut $settings
     * @return TenantPricingSettings
     */
    public function updateTenantPricing($tenantId, TenantPricingSettingsPut $settings);

    /**
     * @param string $rootTenantId
     * @param string $leafTenantId
     * @return string[] a list of UUIDs
     */
    public function resolveTenantPath($rootTenantId, $leafTenantId);

    /**
     * @param string $tenantId
     * @return UsageOutput[]
     */
    public function getTenantUsages($tenantId);

    /* ====== Methods to manage tenant's applications ====== */

    /**
     * @return Application[]
     */
    public function getApplications();

    /**
     * @param string[] $uuids
     * @return Application[]
     */
    public function fetchApplications(array $uuids);

    /**
     * @return Application[]
     */
    public function getRootTenantApplications();

    /**
     * @param string $type
     * @return Application|null
     */
    public function getApplicationByType($type);

    /**
     * @param string $tenantId
     * @return string[] a list of UUIDs
     */
    public function getTenantApplicationsUuids($tenantId);

    /**
     * @param string $tenantId
     * @return RoleListItems[]
     */
    public function getTenantApplicationsRoles($tenantId);

    /**
     * @param string $tenantId
     * @param string $applicationId
     * @return bool
     */
    public function isApplicationEnabledForTenant($tenantId, $applicationId);

    /**
     * @param string $tenantId
     * @param string $applicationId
     */
    public function enableApplicationForTenant($tenantId, $applicationId);

    /**
     * @param string $tenantId
     * @param string $applicationId
     */
    public function disableApplicationForTenant($tenantId, $applicationId);

    /* ====== Methods to manage editions ====== */

    /**
     * @return string[]
     */
    public function getEditions();

    /* ====== Methods to manage offering items ====== */

    /**
     * @param string $tenantId
     * @param string $edition
     * @return OfferingItemOutput[]
     */
    public function getTenantOfferingItems($tenantId, $edition = self::EDITION_ANY);

    /**
     * @param string $tenantId
     * @param string $applicationId
     * @return OfferingItemOutput[]
     */
    public function getTenantApplicationOfferingItems($tenantId, $applicationId);

    /**
     * @param string $tenantId
     * @param OfferingItem[] $offeringItems
     * @return OfferingItemOutput[]
     */
    public function updateTenantOfferingItems($tenantId, array $offeringItems);

    /**
     * @return float
     */
    public function getOfferingItemsOverageRatio();

    /**
     * Recursive enable an application for a hierarchy of tenants except root tenant.
     *
     * @param string $rootTenantId
     * @param string $leafTenantId
     * @param string $applicationId
     */
    public function enableApplicationForTenantsChain($rootTenantId, $leafTenantId, $applicationId);

    /**
     * Recursive enable specified offering items with unlimited quotas for a hierarchy of tenants except root tenant.
     * Note: Enabled or not specified offering items won't be affected
     *
     * @param string $rootTenantId
     * @param string $leafTenantId
     * @param OfferingItem[] $offeringItems
     */
    public function enableOfferingItemsForTenantsChain(
        $rootTenantId,
        $leafTenantId,
        array $offeringItems
    );

    /**
     * Update specified only offering items for $leafTenantId.
     * Recursive enable offering items for tenant's parent (see method enableUnlimitedOfferingItemsForTenantsChain)
     *
     * @param string $rootTenantId
     * @param string $leafTenantId
     * @param OfferingItem[] $offeringItems
     */
    public function updateOfferingItemsForTenantsChain(
        $rootTenantId,
        $leafTenantId,
        array $offeringItems
    );

    /**
     * @param string $tenantId
     * @param string $kind
     * @return OfferingItemOutput[]
     */
    public function getOfferingItemsAvailableForChild($tenantId, $kind);

    /**
     * @param string $tenantId
     * @param string $kind
     * @param string $offeringItemName
     * @return bool
     */
    public function isOfferingItemAvailableForChild($tenantId, $kind, $offeringItemName);

    /* ====== Methods to manage reports ====== */

    /**
     * @param string $reportId
     * @return StoredReportParamsItems[]
     */
    public function getStoredUsageReports($reportId);

    /**
     * @param string $reportId
     * @param string $storedReportId
     * @param string $destinationFile
     * @return void
     */
    public function downloadStoredUsageReportArchive($reportId, $storedReportId, $destinationFile);

    /**
     * @param string $tenantId
     * @return Report
     */
    public function orderCurrentUsageReportForAccounts($tenantId);

    /* ====== Methods to manage locations ====== */

    /**
     * @param string $tenantId
     * @return string[] a list of UUIDs
     */
    public function getTenantLocationsUuids($tenantId);

    /**
     * @param string[] $uuids
     * @return Location[]
     */
    public function fetchLocations($uuids);

    /**
     * @param string $tenantId
     * @return Location[]
     */
    public function fetchTenantLocations($tenantId);

    /* ====== Methods to manage infra objects ====== */

    /**
     * @param string $tenantId
     * @return string[] a list of UUIDs
     */
    public function getTenantInfrasUuids($tenantId);

    /**
     * @param string[] $uuids
     * @return Infra[]
     */
    public function fetchInfras($uuids);

    /**
     * @param string $tenantId
     * @return Infra[]
     */
    public function fetchTenantInfras($tenantId);

    /**
     * @param string $rootTenantId
     * @param string $leafTenantId
     * @return string[] a list of UUIDs
     */
    public function getInfrasUuidsForTenantsChain($rootTenantId, $leafTenantId);

    /**
     * @param string $rootTenantId
     * @param string $leafTenantId
     * @return Infra[]
     */
    public function fetchInfrasForTenantsChain($rootTenantId, $leafTenantId);

    /**
     * @deprecated
     * todo remove this method in next update
     * @param $groupId
     * @return null|object
     */
    public function getGroupV1($groupId);

    /**
     * @deprecated
     * todo remove this method in next update
     * @param $adminId
     * @return null|object
     */
    public function getAdminV1($adminId);

    /**
     * @deprecated
     * todo remove this method in next update
     * @param $userId
     * @return null|object
     */
    public function getUserV1($userId);

    /**
     * @deprecated
     * todo remove this method in next update
     * @return array
     */
    public function getRootGroupChildrenV1();
}