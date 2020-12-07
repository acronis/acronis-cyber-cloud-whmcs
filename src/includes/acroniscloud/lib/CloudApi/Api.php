<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\CloudApi;

use Acronis\Cloud\Client\ApiException;
use Acronis\Cloud\Client\HttpException;
use Acronis\Cloud\Client\IOException;
use Acronis\Cloud\Client\Model\AccessPolicies\AccessPoliciesList;
use Acronis\Cloud\Client\Model\AccessPolicies\AccessPolicy;
use Acronis\Cloud\Client\Model\Applications\Application;
use Acronis\Cloud\Client\Model\Applications\RoleList;
use Acronis\Cloud\Client\Model\Applications\RoleListItems;
use Acronis\Cloud\Client\Model\Clients\Client;
use Acronis\Cloud\Client\Model\Clients\ClientPost;
use Acronis\Cloud\Client\Model\Clients\ClientPostResult;
use Acronis\Cloud\Client\Model\Infra\Infra;
use Acronis\Cloud\Client\Model\Locations\Location;
use Acronis\Cloud\Client\Model\OfferingItems\OfferingItemOutput;
use Acronis\Cloud\Client\Model\OfferingItems\Quota;
use Acronis\Cloud\Client\Model\Pricing\TenantPricingSettings;
use Acronis\Cloud\Client\Model\Pricing\TenantPricingSettingsPut;
use Acronis\Cloud\Client\Model\Reports\Report;
use Acronis\Cloud\Client\Model\Reports\ReportPost;
use Acronis\Cloud\Client\Model\Reports\ReportPostKind;
use Acronis\Cloud\Client\Model\Reports\ReportPostLevel;
use Acronis\Cloud\Client\Model\Reports\ReportPostParameters;
use Acronis\Cloud\Client\Model\Reports\ReportPostResultAction;
use Acronis\Cloud\Client\Model\Reports\ReportPostSchedule;
use Acronis\Cloud\Client\Model\Reports\ReportPostType;
use Acronis\Cloud\Client\Model\Reports\Stored\StoredReportParamsItems;
use Acronis\Cloud\Client\Model\Tenants\OfferingItem;
use Acronis\Cloud\Client\Model\Tenants\OfferingItemsPut;
use Acronis\Cloud\Client\Model\Tenants\Tenant;
use Acronis\Cloud\Client\Model\Tenants\TenantPost;
use Acronis\Cloud\Client\Model\Tenants\TenantPut;
use Acronis\Cloud\Client\Model\Usages\UsageOutput;
use Acronis\Cloud\Client\Model\Users\User;
use Acronis\Cloud\Client\Model\Users\UserPost;
use Acronis\Cloud\Client\Model\Users\UserPut;
use Acronis\Cloud\Client\Model\Version\Version;
use AcronisCloud\Util\Arr;
use AcronisCloud\Util\Str;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;

class Api extends AuthorizedApi
{
    /**
     * @param string $applicationId
     * @param string $offeringItemName
     * @param string|null $infraId
     * @param int|null|false $quotaValue null - unlimited; false - no quota;
     * @param double $overage
     * @param int $status
     * @return OfferingItem
     */
    public static function createOfferingItem(
        $applicationId,
        $offeringItemName,
        $infraId = null,
        $quotaValue = false,
        $overage = 0.0,
        $status = null
    ) {
        $offeringItem = (new OfferingItem())
            ->setApplicationId($applicationId)
            ->setName($offeringItemName);

        if (!is_null($status)) {
            $offeringItem->setStatus($status);
        }

        if ($infraId) {
            $offeringItem->setInfraId($infraId);
        }

        if ($quotaValue === false || $status === static::OFFERING_ITEM_STATUS_INACTIVE) {
            return $offeringItem;
        }

        $quota = (new Quota())
            ->setValue($quotaValue)
            ->setOverage($overage);

        $offeringItem->setQuota($quota);

        return $offeringItem;
    }

    /**
     * @param UsageOutput|OfferingItem|OfferingItemOutput $offeringItem
     * @return string
     */
    public static function getOfferingItemHash($offeringItem)
    {
        return Str::format(
            '%s:%s',
            $offeringItem->getName(), $offeringItem->hasInfraId() ? $offeringItem->getInfraId() : ''
        );
    }

    /**
     * @param UsageOutput[]|OfferingItem[]|OfferingItemOutput[] $offeringItems
     * @return array
     */
    public static function getOfferingItemsHashTable(array $offeringItems)
    {
        return Arr::map(
            $offeringItems,
            function ($offeringItem) {
                return static::getOfferingItemHash($offeringItem);
            },
            function ($offeringItem) {
                return $offeringItem;
            }
        );
    }

    /**
     * @param OfferingItem[] $offeringItems1
     * @param array $otherOfferingItemsArrays
     * @return OfferingItem[]
     */
    public static function mergeOfferingItems(array $offeringItems1, ...$otherOfferingItemsArrays)
    {
        /** @var OfferingItem[] $offeringItems */
        $offeringItems = array_merge($offeringItems1, ...$otherOfferingItemsArrays);
        /** @var OfferingItem[] $offeringItemsHashTable */
        $offeringItemsHashTable = [];
        foreach ($offeringItems as $offeringItem) {
            $offeringItemHash = static::getOfferingItemHash($offeringItem);
            $offeringItemsHashTable[$offeringItemHash][] = $offeringItem;
        }

        $mergedOfferingItems = [];
        foreach ($offeringItemsHashTable as $offeringItemsGroup) {
            if (empty($offeringItemsGroup)) {
                continue;
            }

            $applicationId = null;
            $offeringItemName = null;
            $offeringItemInfraId = null;
            $offeringItemStatus = null;
            $offeringItemOverage = 0;
            $offeringItemQuotaValue = false;

            foreach ($offeringItemsGroup as $offeringItem) {
                /** @var OfferingItem $offeringItem */
                $applicationId = $offeringItem->getApplicationId();
                $offeringItemName = $offeringItem->getName();

                if ($offeringItem->hasInfraId()) {
                    $offeringItemInfraId = $offeringItem->getInfraId();
                }

                $status = $offeringItem->hasStatus()
                    ? $offeringItem->getStatus()
                    : static::OFFERING_ITEM_STATUS_ACTIVE;

                if (!$offeringItemStatus || $offeringItemStatus === static::OFFERING_ITEM_STATUS_INACTIVE) {
                    $offeringItemStatus = $status;
                }

                if ($offeringItemStatus === static::OFFERING_ITEM_STATUS_INACTIVE) {
                    continue;
                }

                if (!$offeringItem->hasQuota()) {
                    continue;
                }

                // if the value is unlimited
                if (is_null($offeringItemQuotaValue)) {
                    continue;
                }

                $quotaValue = $offeringItem->getQuota()->getValue();

                // if the value is unlimited
                if (is_null($quotaValue)) {
                    $offeringItemQuotaValue = $quotaValue;
                    continue;
                }

                if ($offeringItemQuotaValue === false) {
                    $offeringItemQuotaValue = $quotaValue;
                } else {
                    $offeringItemQuotaValue += $quotaValue;
                }

                $overageValue = $offeringItem->getQuota()->getOverage();
                if ($overageValue) {
                    $offeringItemOverage += $overageValue;
                }
            }

            $mergedOfferingItems[] = static::createOfferingItem(
                $applicationId,
                $offeringItemName,
                $offeringItemInfraId,
                $offeringItemQuotaValue,
                $offeringItemOverage,
                $offeringItemStatus
            );
        }

        return $mergedOfferingItems;
    }

    /* ====== Methods for the root tenant ====== */

    /**
     * @return string
     * @throws ApiException
     * @throws HttpException
     */
    public function getRootTenantId()
    {
        return $this->getMe()->getTenantId();
    }

    /**
     * @return OfferingItemOutput[]
     * @throws ApiException
     * @throws HttpException
     */
    public function getRootTenantOfferingItems()
    {
        return $this->memoize(function () {
            return $this->getTenantOfferingItems($this->getRootTenantId());
        });
    }

    /**
     * @param $offeringItemName
     * @param $offeringItemInfraId
     * @return OfferingItemOutput
     * @throws ApiException
     * @throws HttpException
     */
    public function getRootTenantOfferingItem($offeringItemName, $offeringItemInfraId = null)
    {
        $availableOfferingItemsHashTable = $this->memoize(function () {
            return static::getOfferingItemsHashTable($this->getRootTenantOfferingItems());
        });

        $offeringItem = new OfferingItem();
        $offeringItem->setName($offeringItemName);
        if ($offeringItemInfraId) {
            $offeringItem->setInfraId($offeringItemInfraId);
        }

        $offeringItemHash = static::getOfferingItemHash($offeringItem);

        return Arr::get($availableOfferingItemsHashTable, $offeringItemHash);
    }

    /**
     * @return Infra[]
     * @throws ApiException
     * @throws HttpException
     */
    public function getRootTenantInfras()
    {
        return $this->memoize(function () {
            return $this->fetchTenantInfras($this->getRootTenantId());
        });
    }

    /**
     * @return Location[]
     * @throws ApiException
     * @throws HttpException
     */
    public function getRootTenantLocations()
    {
        return $this->memoize(function () {
            return $this->fetchTenantLocations($this->getRootTenantId());
        });
    }

    /**
     * @param string $infraId
     * @return bool
     * @throws ApiException
     * @throws HttpException
     */
    public function isInfraAvailableForRootTenant($infraId)
    {
        $infrasHashTable = $this->memoize(function () {
            $infras = $this->getRootTenantInfras();

            return Arr::map(
                $infras,
                function ($infra) {
                    /** @var Infra $infra */
                    return $infra->getId();
                },
                function () {
                    return true;
                }
            );
        });

        return Arr::has($infrasHashTable, $infraId);
    }

    /* ====== Users API ====== */

    /**
     * Returns True if the login exists otherwise False
     *
     * @param string $login
     * @return bool
     * @throws ApiException
     * @throws HttpException
     */
    public function checkLogin($login)
    {
        return $this->authorizedCall(function () use ($login) {
            try {
                $this->getUsersApi()->getUsersCheckLogin($login);

                return false;
            } catch (HttpException $e) {
                if ($e->getCode() != 409) {
                    throw $e;
                }

                return true;
            }
        });
    }

    /**
     * @param string $login
     * @param string $email
     * @param string $password
     * @throws ApiException
     * @throws HttpException
     */
    public function activateUser($login, $email, $password)
    {
        $activationEmail = $this->getActivationEmail($login, $email);
        $this->activateToken($activationEmail->token, $password);
    }

    /**
     * @param string $login
     * @param string $email
     * @return object
     * @throws ApiException
     * @throws HttpException
     */
    public function sendActivationEmail($login, $email)
    {
        return $this->authorizedCall(function () use ($login, $email) {
            return $this->requestApiV1Method('/actions/mail/activate', 'POST', [], [
                'login' => $login,
                'email' => $email,
            ]);
        });
    }

    /**
     * @param string $login
     * @param string $email
     * @return object
     * @throws ApiException
     * @throws HttpException
     */
    public function getActivationEmail($login, $email)
    {
        return $this->authorizedCall(function () use ($login, $email) {
            return $this->requestApiV1Method('/actions/mail/activate', 'GET', [
                'login' => $login,
                'email' => $email,
            ]);
        });
    }

    /**
     * @return Version
     * @throws ApiException
     * @throws HttpException
     */
    public function getVersions()
    {
        return $this->authorizedCall(function () {
            return $this->getVersionsApi()->getVersions();
        });
    }

    /**
     * @param string $token
     * @param string $password
     * @return object
     * @throws ApiException
     * @throws HttpException
     */
    public function activateToken($token, $password)
    {
        return $this->authorizedCall(function () use ($token, $password) {
            return $this->requestApiV1Method('/actions/activate', 'POST', [
                'token' => $token,
            ], [
                'password' => $password,
            ]);
        });
    }

    /**
     * @param string $userId
     * @return User
     * @throws ApiException
     * @throws HttpException
     */
    public function getUser($userId)
    {
        return $this->authorizedCall(function () use ($userId) {
            return $this->getUsersApi()->getUsersByUserId($userId);
        });
    }

    /**
     * @param UserPost $body
     * @return User
     * @throws ApiException
     * @throws HttpException
     */
    public function createUser(UserPost $body)
    {
        return $this->authorizedCall(function () use ($body) {
            return $this->getUsersApi()->postUsers($body);
        });
    }

    /**
     * @param string $userId
     * @param UserPut $body
     * @return User
     * @throws ApiException
     * @throws HttpException
     */
    public function updateUser($userId, UserPut $body)
    {
        return $this->authorizedCall(function () use ($userId, $body) {
            return $this->getUsersApi()->putUsersByUserId($userId, $body);
        });
    }

    /**
     * @param $userId
     * @param AccessPolicy[] $accessPolicies
     * @return AccessPolicy[]
     * @throws ApiException
     * @throws HttpException
     */
    public function updateUserAccessPolicies($userId, array $accessPolicies)
    {
        $body = new AccessPoliciesList();
        $body->setItems($accessPolicies);

        /** @var AccessPoliciesList $response */
        $response = $this->authorizedCall(function () use ($userId, $body) {
            return $this->getUsersApi()->putUsersAccessPoliciesByUserId($userId, $body);
        });

        return $response->getItems();
    }

    /* ====== Clients API ====== */

    /**
     * @param ClientPost $client
     * @return ClientPostResult
     * @throws ApiException
     * @throws HttpException
     */
    public function createClient(ClientPost $client)
    {
        return $this->authorizedCall(function () use ($client) {
            return $this->getClientsApi()->postClients($client);
        });
    }

    /* ====== Methods to manage tenants ====== */

    /**
     * @param string $tenantId
     * @return Tenant
     * @throws HttpException
     * @throws ApiException
     */
    public function getTenant($tenantId)
    {
        return $this->authorizedCall(function () use ($tenantId) {
            return $this->getTenantsApi()->getTenantsByTenantId($tenantId);
        });
    }

    /**
     * @param string[] $uuids
     * @return Tenant[]
     * @throws ApiException
     * @throws HttpException
     */
    public function fetchTenants(array $uuids)
    {
        return $this->batchRun(function ($uuids) {
            return $this->authorizedCall(function () use ($uuids) {
                return $this->getTenantsApi()->getTenants($uuids);
            })->getItems();
        }, $uuids);
    }

    /**
     * @param TenantPost $body
     * @return Tenant
     * @throws HttpException
     * @throws ApiException
     */
    public function createTenant(TenantPost $body)
    {
        return $this->authorizedCall(function () use ($body) {
            return $this->getTenantsApi()->postTenants($body);
        });
    }

    /**
     * @param string $tenantId
     * @param int $version
     * @throws ApiException
     * @throws HttpException
     */
    public function deleteTenant($tenantId, $version)
    {
        $this->authorizedCall(function () use ($tenantId, $version) {
            $this->getTenantsApi()->deleteTenantsByTenantId($tenantId, $version);
        });
    }

    /**
     * @param string $tenantId
     * @param TenantPut $body
     * @return Tenant
     * @throws HttpException
     * @throws ApiException
     */
    public function updateTenant($tenantId, TenantPut $body)
    {
        return $this->authorizedCall(function () use ($tenantId, $body) {
            return $this->getTenantsApi()->putTenantsByTenantId($tenantId, $body);
        });
    }

    /**
     * @param string $tenantId
     * @param string|null $order
     * @return string[]
     * @throws ApiException
     * @throws HttpException
     */
    public function getTenantChildrenUuids($tenantId, $order = null)
    {
        return $this->authorizedCall(function () use ($tenantId, $order) {
            return $this->getTenantsApi()->getTenantsChildrenByTenantId($tenantId, $order);
        })->getItems();
    }

    /**
     * @param string $tenantId
     * @param string|null $order
     * @return Tenant[]
     * @throws ApiException
     * @throws HttpException
     */
    public function fetchTenantChildren($tenantId, $order = null)
    {
        $uuids = $this->getTenantChildrenUuids($tenantId, $order);

        return empty($uuids) ? [] : $this->fetchTenants($uuids);
    }

    /**
     * @param string $tenantId
     * @return TenantPricingSettings
     * @throws ApiException
     * @throws HttpException
     */
    public function getTenantPricing($tenantId)
    {
        return $this->authorizedCall(function () use ($tenantId) {
            return $this->getTenantsApi()->getTenantsPricingByTenantId($tenantId);
        });
    }

    /**
     * @param string $tenantId
     * @param TenantPricingSettingsPut $settings
     * @return TenantPricingSettings
     * @throws ApiException
     * @throws HttpException
     */
    public function updateTenantPricing($tenantId, TenantPricingSettingsPut $settings)
    {
        return $this->authorizedCall(function () use ($tenantId, $settings) {
            return $this->getTenantsApi()->putTenantsPricingByTenantId($tenantId, $settings);
        });
    }

    /**
     * @param string $rootTenantId
     * @param string $leafTenantId
     * @return string[] a list of UUIDs
     * @throws ApiException
     * @throws HttpException
     */
    public function resolveTenantPath($rootTenantId, $leafTenantId)
    {
        $tenantId = $leafTenantId;
        $tenantPath = [$tenantId];
        while ($tenantId !== $rootTenantId) {
            $tenantData = $this->getTenant($tenantId);
            $tenantId = $tenantData->getParentId();

            $tenantPath[] = $tenantId;
        }

        return $tenantPath;
    }

    /**
     * @param string $tenantId
     * @return UsageOutput[]
     * @throws ApiException
     * @throws HttpException
     */
    public function getTenantUsages($tenantId)
    {
        return $this->authorizedCall(function () use ($tenantId) {
            return $this->getTenantsApi()->getTenantsUsagesByTenantId($tenantId);
        })->getItems();
    }

    /* ====== Methods to manage tenant's applications ====== */

    /**
     * @return Application[]
     * @throws ApiException
     * @throws HttpException
     */
    public function getApplications()
    {
        return $this->memoize(function () {
            return $this->authorizedCall(function () {
                return $this->getApplicationsApi()->getApplications();
            })->getItems();
        });
    }

    /**
     * @param string[] $uuids
     * @return Application[]
     * @throws ApiException
     * @throws HttpException
     */
    public function fetchApplications(array $uuids)
    {
        $applications = $this->getApplications();

        return array_filter($applications, function ($app) use ($uuids) {
            return in_array($app->getId(), $uuids);
        });
    }

    /**
     * @return Application[]
     * @throws ApiException
     * @throws HttpException
     */
    public function getRootTenantApplications()
    {
        return $this->memoize(function () {
            $uuids = $this->getTenantApplicationsUuids($this->getRootTenantId());

            return $this->fetchApplications($uuids);
        });
    }

    /**
     * @param string $type
     * @return Application|null
     * @throws ApiException
     * @throws HttpException
     */
    public function getApplicationByType($type)
    {
        $applicationsHashTable = $this->memoize(function () {
            $applications = $this->getApplications(); // todo getRootTenantApplications

            return Arr::map(
                $applications,
                function ($application) {
                    /** @var Application $application */
                    return $application->getType();
                },
                function ($application) {
                    return $application;
                }
            );
        });

        return Arr::get($applicationsHashTable, $type);
    }

    /**
     * @param string $tenantId
     * @return string[] a list of UUIDs
     * @throws ApiException
     * @throws HttpException
     */
    public function getTenantApplicationsUuids($tenantId)
    {
        return $this->authorizedCall(function () use ($tenantId) {
            return $this->getTenantsApi()->getTenantsApplicationsByTenantId($tenantId);
        })->getItems();
    }

    /**
     * @param string $tenantId
     * @return RoleListItems[]
     * @throws ApiException
     * @throws HttpException
     */
    public function getTenantApplicationsRoles($tenantId)
    {
        /** @var RoleList $response */
        $response = $this->authorizedCall(function () use ($tenantId) {
            return $this->getTenantsApi()->getTenantsApplicationsRolesByTenantId($tenantId);
        });

        return $response->getItems();
    }

    /**
     * @param string $tenantId
     * @param string $applicationId
     * @return bool
     * @throws ApiException
     * @throws HttpException
     */
    public function isApplicationEnabledForTenant($tenantId, $applicationId)
    {
        return in_array($applicationId, $this->getTenantApplicationsUuids($tenantId));
    }

    /**
     * @param string $tenantId
     * @param string $applicationId
     * @throws ApiException
     * @throws HttpException
     */
    public function enableApplicationForTenant($tenantId, $applicationId)
    {
        $this->authorizedCall(function () use ($applicationId, $tenantId) {
            return $this->getApplicationsApi()->postApplicationsBindingsTenants($applicationId, $tenantId);
        });
    }

    /**
     * @param string $tenantId
     * @param string $applicationId
     * @throws ApiException
     * @throws HttpException
     */
    public function disableApplicationForTenant($tenantId, $applicationId)
    {
        $this->authorizedCall(function () use ($applicationId, $tenantId) {
            return $this->getApplicationsApi()->deleteApplicationsBindingsTenants($applicationId, $tenantId);
        });
    }

    /* ====== Methods to manage editions ====== */

    /**
     * @return string[]
     * @throws ApiException
     * @throws HttpException
     */
    public function getEditions()
    {
        return $this->memoize(function () {
            return $this->authorizedCall(function () {
                return $this->getEditionsApi()->getEditions();
            })->getItems();
        });
    }

    /* ====== Methods to manage offering items ====== */

    /**
     * @param string $tenantId
     * @param string $edition
     * @return OfferingItemOutput[]
     * @throws ApiException
     * @throws HttpException
     */
    public function getTenantOfferingItems($tenantId, $edition = self::EDITION_ANY)
    {
        return $this->authorizedCall(function () use ($tenantId, $edition) {
            return $this->getTenantsApi()->getTenantsOfferingItemsByTenantId($tenantId, null, $edition);
        })->getItems();
    }

    /**
     * @param string $tenantId
     * @param string $applicationId
     * @param string $edition
     * @return OfferingItemOutput[]
     * @throws ApiException
     * @throws HttpException
     */
    public function getTenantApplicationOfferingItems($tenantId, $applicationId, $edition = self::EDITION_ANY)
    {
        return $this->authorizedCall(function () use ($tenantId, $applicationId, $edition) {
            return $this->getTenantsApi()->getTenantsApplicationsOfferingItems($tenantId, $applicationId, $edition);
        })->getItems();
    }

    /**
     * @param string $tenantId
     * @param OfferingItem[] $offeringItems
     * @return OfferingItemOutput[]
     * @throws ApiException
     * @throws HttpException
     */
    public function updateTenantOfferingItems($tenantId, array $offeringItems)
    {
        $body = new OfferingItemsPut();
        $body->setOfferingItems($offeringItems);

        return $this->authorizedCall(function () use ($tenantId, $body) {
            return $this->getTenantsApi()->putTenantsOfferingItemsByTenantId($tenantId, $body);
        })->getItems();
    }

    /**
     * @return float
     */
    public function getOfferingItemsOverageRatio()
    {
        return $this->getConfig()->getProductSettings()->getOverageRatio();
    }

    /**
     * Recursive enable an application for a hierarchy of tenants except root tenant.
     *
     * @param string $rootTenantId
     * @param string $leafTenantId
     * @param string $applicationId
     * @throws ApiException
     * @throws HttpException
     */
    public function enableApplicationForTenantsChain($rootTenantId, $leafTenantId, $applicationId)
    {
        if ($leafTenantId === $rootTenantId) {
            return;
        }

        if ($this->isApplicationEnabledForTenant($leafTenantId, $applicationId)) {
            return;
        }

        // enable for the parent
        $tenant = $this->getTenant($leafTenantId);
        $this->enableApplicationForTenantsChain($rootTenantId, $tenant->getParentId(), $applicationId);
        // enable for the tenant
        $this->enableApplicationForTenant($leafTenantId, $applicationId);
    }

    /**
     * Recursive enable specified offering items with unlimited quotas for a hierarchy of tenants except root tenant.
     * Note: Enabled or not specified offering items won't be affected
     *
     * @param string $rootTenantId
     * @param string $leafTenantId
     * @param OfferingItem[] $offeringItems
     * @throws ApiException
     * @throws HttpException
     */
    public function enableOfferingItemsForTenantsChain(
        $rootTenantId,
        $leafTenantId,
        array $offeringItems
    ) {
        if ($leafTenantId === $rootTenantId) {
            return;
        }

        // enable only OIs with status 1, other will be ignored
        $neededOfferingItems = static::getOfferingItemsHashTable(
            $this->getActiveOfferingItems($offeringItems)
        );
        $availableOfferingItems = static::getOfferingItemsHashTable(
            $this->getTenantOfferingItems($leafTenantId)
        );

        // Enable not available offering items for the parent
        $notAvailableOfferingItems = array_diff_key($neededOfferingItems, $availableOfferingItems);
        if (!empty($notAvailableOfferingItems)) {
            $tenant = $this->getTenant($leafTenantId);
            $this->enableOfferingItemsForTenantsChain(
                $rootTenantId, $tenant->getParentId(), $notAvailableOfferingItems
            );
            $availableOfferingItems = static::getOfferingItemsHashTable(
                $this->getTenantOfferingItems($leafTenantId)
            );
        }

        // Exist if all needed offering items are enabled
        $enabledOfferingItems = array_filter($availableOfferingItems, function ($offeringItem) {
            /** @var OfferingItem $offeringItem */
            return $offeringItem->getStatus();
        });
        $disabledOfferingItems = array_diff_key($neededOfferingItems, $enabledOfferingItems);
        if (empty($disabledOfferingItems)) {
            return;
        }

        // Enable disabled offering items
        $offeringItemsToUpdate = [];
        foreach ($disabledOfferingItems as $offeringItemHash => $neededOfferingItem) {
            /** @var OfferingItem $neededOfferingItem */

            $oi = new OfferingItem();
            $oi->setApplicationId($neededOfferingItem->getApplicationId());
            $oi->setName($neededOfferingItem->getName());
            $oi->setStatus(static::OFFERING_ITEM_STATUS_ACTIVE);

            if ($neededOfferingItem->hasInfraId()) {
                $oi->setInfraId($neededOfferingItem->getInfraId());
            }

            $offeringItemsToUpdate [] = $oi;
        }

        // Sent request to update offering items
        $this->updateTenantOfferingItems($leafTenantId, $offeringItemsToUpdate);
    }

    /**
     * Update specified only offering items for $leafTenantId.
     * Recursive enable offering items for tenant's parent (see method enableUnlimitedOfferingItemsForTenantsChain)
     *
     * @param string $rootTenantId
     * @param string $leafTenantId
     * @param OfferingItem[] $offeringItems
     * @throws ApiException
     * @throws HttpException
     */
    public function updateOfferingItemsForTenantsChain(
        $rootTenantId,
        $leafTenantId,
        array $offeringItems
    ) {
        if ($leafTenantId === $rootTenantId) {
            throw new HttpException(Str::format(
                'Unable to update offers for yourself. Root tenant ID equals leaf tenant ID %s.',
                $leafTenantId
            ), 403);
        }

        $neededOfferingItems = static::getOfferingItemsHashTable($offeringItems);
        $availableOfferingItems = static::getOfferingItemsHashTable(
            $this->getTenantOfferingItems($leafTenantId)
        );

        // enable not available offering items for tenant's parent if there are such
        $notAvailableOfferingItems = array_diff_key(
            $this->getActiveOfferingItems($neededOfferingItems),
            $availableOfferingItems
        );
        if (!empty($notAvailableOfferingItems)) {
            $tenant = $this->getTenant($leafTenantId);
            $this->enableOfferingItemsForTenantsChain(
                $rootTenantId, $tenant->getParentId(), $notAvailableOfferingItems
            );

            // refresh a list of available offering items
            $availableOfferingItems = static::getOfferingItemsHashTable(
                $this->getTenantOfferingItems($leafTenantId)
            );
        }

        $offeringItemsToUpdate = [];
        foreach ($neededOfferingItems as $offeringItemHash => $offeringItem) {
            /** @var OfferingItem $offeringItem */
            $status = $offeringItem->hasStatus()
                ? $offeringItem->getStatus()
                : static::OFFERING_ITEM_STATUS_ACTIVE;

            $oi = new OfferingItem();
            $oi->setStatus($status);
            $oi->setApplicationId($offeringItem->getApplicationId());
            $oi->setName($offeringItem->getName());

            if ($offeringItem->hasInfraId()) {
                $oi->setInfraId($offeringItem->getInfraId());
            }

            if ($status !== static::OFFERING_ITEM_STATUS_INACTIVE && $offeringItem->hasQuota()) {
                $offeringItemQuota = $offeringItem->getQuota();

                $quota = new Quota();
                $quota->setValue($offeringItemQuota->getValue());
                $quota->setOverage($offeringItemQuota->getOverage());

                $quota->setVersion(0);

                // set current version of quota
                /** @var OfferingItem $availableOfferingItem */
                $availableOfferingItem = Arr::get($availableOfferingItems, $offeringItemHash);

                if ($availableOfferingItem && $availableOfferingItem->hasQuota()) {
                    $availableOfferingItemQuota = $availableOfferingItem->getQuota();
                    if ($availableOfferingItemQuota->hasVersion()) {
                        $quota->setVersion($availableOfferingItemQuota->getVersion());
                    }
                }

                $oi->setQuota($quota);
            }

            $offeringItemsToUpdate[] = $oi;
        }

        // sent request to update offering items
        $this->updateTenantOfferingItems($leafTenantId, $offeringItemsToUpdate);
    }

    /**
     * @param string $tenantId
     * @param string $kind
     * @param string $edition
     * @return OfferingItemOutput[]
     * @throws ApiException
     * @throws HttpException
     */
    public function getOfferingItemsAvailableForChild($tenantId, $kind, $edition = self::EDITION_ANY)
    {
        return $this->authorizedCall(function () use ($tenantId, $kind, $edition) {
            return $this->getTenantsApi()->getTenantsOfferingItemsAvailableForChild($tenantId, $kind, $edition);
        })->getItems();
    }

    /**
     * @param string $tenantId
     * @param string $kind
     * @param string $offeringItemName
     * @param bool $force
     * @return bool
     * @throws ApiException
     * @throws HttpException
     */
    public function isOfferingItemAvailableForChild($tenantId, $kind, $offeringItemName, $force = false)
    {
        $hashtable = $this->memoize(function () use ($tenantId, $kind) {
            return Arr::map(
                $this->getOfferingItemsAvailableForChild($tenantId, $kind),
                'name',
                function () {
                    return true;
                },
                false
            );
        }, $tenantId . '_' . $kind, $force);

        return Arr::has($hashtable, $offeringItemName);
    }

    /**
     * @param string $reportId
     * @return StoredReportParamsItems[]
     * @throws ApiException
     * @throws HttpException
     */
    public function getStoredUsageReports($reportId)
    {
        return $this->authorizedCall(function () use ($reportId) {
            return $this->getReportsApi()->getReportsStoredByReportId($reportId);
        })->getItems();
    }

    /**
     * @param string $reportId
     * @param string $storedReportId
     * @param string $destinationFile
     * @throws ApiException
     * @throws HttpException
     */
    public function downloadStoredUsageReportArchive($reportId, $storedReportId, $destinationFile)
    {
        $this->authorizedCall(function () use ($reportId, $storedReportId, $destinationFile) {
            $resourcePath = Str::format('/reports/%s/stored/%s', $reportId, $storedReportId);
            $apiClientConfig = $this->getApiClient()->getConfig();
            $url = $apiClientConfig->getHost() . $resourcePath;
            $headers = [
                'Authorization' => 'Bearer ' . $apiClientConfig->getAccessToken(),
                'User-Agent' => $apiClientConfig->getUserAgent(),
            ];
            $this->getLogger()->info(Str::format(
                'Download file "%s" to "%s".',
                $url, $destinationFile
            ));
            $this->downloadAndSaveFile($url, $destinationFile, $headers);
        });
    }

    /**
     * @param string $tenantId
     * @return Report
     * @throws ApiException
     * @throws HttpException
     */
    public function orderCurrentUsageReportForAccounts($tenantId)
    {
        $params = new ReportPostParameters();
        $params->setTenantId($tenantId);
        $params->setKind(ReportPostKind::CURRENT);
        $params->setLevel(ReportPostLevel::ACCOUNTS);

        $schedule = new ReportPostSchedule();
        $schedule->setType(ReportPostType::ONCE);

        $body = new ReportPost();
        $body->setParameters($params);
        $body->setSchedule($schedule);
        $body->setRecipients([]);
        $body->setResultAction(ReportPostResultAction::SAVE);

        return $this->authorizedCall(function () use ($body) {
            return $this->getReportsApi()->postReports($body);
        });
    }

    /* ====== Methods to manage locations ====== */

    /**
     * @param string $tenantId
     * @return string[] a list of UUIDs
     * @throws ApiException
     * @throws HttpException
     */
    public function getTenantLocationsUuids($tenantId)
    {
        return $this->authorizedCall(function () use ($tenantId) {
            return $this->getTenantsApi()->getTenantsLocationsByTenantId($tenantId);
        })->getLocations();
    }

    /**
     * @param string[] $uuids
     * @return Location[]
     * @throws ApiException
     * @throws HttpException
     */
    public function fetchLocations($uuids)
    {
        return $this->batchRun(function ($uuids) {
            return $this->authorizedCall(function () use ($uuids) {
                return $this->getLocationsApi()->getLocations($uuids);
            })->getItems();
        }, $uuids);
    }

    /**
     * @param string $tenantId
     * @return Location[]
     * @throws ApiException
     * @throws HttpException
     */
    public function fetchTenantLocations($tenantId)
    {
        $uuids = $this->getTenantLocationsUuids($tenantId);

        return $uuids ? $this->fetchLocations($uuids) : [];
    }

    /* ====== Methods to manage infra objects ====== */

    /**
     * @param string $tenantId
     * @return string[] a list of UUIDs
     * @throws ApiException
     * @throws HttpException
     */
    public function getTenantInfrasUuids($tenantId)
    {
        return $this->authorizedCall(function () use ($tenantId) {
            return $this->getTenantsApi()->getTenantsInfraByTenantId($tenantId);
        })->getInfras();
    }

    /**
     * @param string[] $uuids
     * @return Infra[]
     * @throws ApiException
     * @throws HttpException
     */
    public function fetchInfras($uuids)
    {
        return $this->batchRun(function ($uuids) {
            return $this->authorizedCall(function () use ($uuids) {
                return $this->getInfraApi()->getInfra($uuids);
            })->getItems();
        }, $uuids);
    }

    /**
     * @param string $tenantId
     * @return Infra[]
     * @throws ApiException
     * @throws HttpException
     */
    public function fetchTenantInfras($tenantId)
    {
        $uuids = $this->getTenantInfrasUuids($tenantId);

        return $uuids ? $this->fetchInfras($uuids) : [];
    }

    /**
     * @param string $rootTenantId
     * @param string $leafTenantId
     * @return string[] a list of UUIDs
     * @throws ApiException
     * @throws HttpException
     */
    public function getInfrasUuidsForTenantsChain($rootTenantId, $leafTenantId)
    {
        $tenantPath = $this->resolveTenantPath($rootTenantId, $leafTenantId);

        $infras = [];
        foreach ($tenantPath as $tenantId) {
            $infras = array_merge($infras, $this->getTenantInfrasUuids($tenantId));
        }

        return array_unique($infras);
    }

    /**
     * @param string $rootTenantId
     * @param string $leafTenantId
     * @return Infra[]
     * @throws ApiException
     * @throws HttpException
     */
    public function fetchInfrasForTenantsChain($rootTenantId, $leafTenantId)
    {
        $uuids = $this->getInfrasUuidsForTenantsChain($rootTenantId, $leafTenantId);

        return $uuids ? $this->fetchInfras($uuids) : [];
    }

    /**
     * todo remove this method in next update
     * @param $groupId
     * @return null|object
     */
    public function getGroupV1($groupId)
    {
        return $this->authorizedCall(function () use ($groupId) {
            return $this->requestApiV1Method('/groups/' . $groupId, 'GET');
        });
    }

    /**
     * todo remove this method in next update
     * @param $adminId
     * @return null|object
     */
    public function getAdminV1($adminId)
    {
        return $this->authorizedCall(function () use ($adminId) {
            return $this->requestApiV1Method('/admins/' . $adminId, 'GET');
        });
    }

    /**
     * todo remove this method in next update
     * @param $userId
     * @return null|object
     */
    public function getUserV1($userId)
    {
        return $this->authorizedCall(function () use ($userId) {
            return $this->requestApiV1Method('/users/' . $userId, 'GET');
        });
    }

    /**
     * @deprecated
     * todo remove this method in next update
     * @return array
     */
    public function getRootGroupChildrenV1()
    {
        return $this->authorizedCall(function () {
            return $this->requestApiV1Method('/groups/self/children/', 'GET');
        });
    }

    /* ====== Private methods ====== */

    /**
     * @param string $url
     * @param string $destinationFile
     * @param array $headers
     * @throws IOException
     * @throws HttpException
     */
    private function downloadAndSaveFile($url, $destinationFile, $headers)
    {
        $client = new GuzzleClient();

        $fp = fopen($destinationFile, 'wb');
        try {
            if (method_exists($client, 'createRequest')) {
                $request = $client->createRequest('GET', $url, [
                    'save_to' => $fp,
                    'headers' => $headers,
                    'verify' => false,
                    'decode_content' => false,
                ]);

                $client->send($request);
            } else {
                $client->request('GET', $url, [
                    'sink' => $fp,
                    'headers' => $headers,
                    'verify' => false,
                    'decode_content' => false,
                ]);
            }
        } catch (BadResponseException $e) {
            throw new HttpException($e->getMessage(), $e->getCode());
        } catch (GuzzleException $e) {
            throw new IOException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * @param OfferingItem[] $offeringItems
     * @return array
     */
    private function getActiveOfferingItems(array $offeringItems)
    {
        return array_filter($offeringItems, function ($offeringItem) {
            /** @var OfferingItem $offeringItem */
            // if status is not specified the offering item will be enabled
            if (!$offeringItem->hasStatus()) {
                return true;
            }

            return $offeringItem->getStatus() === static::OFFERING_ITEM_STATUS_ACTIVE;
        });
    }

    /**
     * @return User|Client
     * @throws ApiException
     * @throws HttpException
     */
    private function getMe()
    {
        return $this->memoize(function () {
            return $this->authorizedCall(function () {
                if ($this->getGrantType() === static::GRANT_TYPE_CLIENT_CREDENTIALS) {
                    return $this->getClientsApi()->getClientsByClientId($this->getLogin());
                } else {
                    return $this->getUsersApi()->getUsersMe();
                }
            });
        });
    }
}