<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace WHMCS\Module\Server\AcronisCloud\Subscription;

use Acronis\Cloud\Client\Model\AccessPolicies\AccessPolicy;
use Acronis\Cloud\Client\Model\AccessPolicies\AccessPolicyTrusteeType;
use Acronis\Cloud\Client\Model\Common\Contact\Contact;
use Acronis\Cloud\Client\Model\Common\Types\Role;
use Acronis\Cloud\Client\Model\Pricing\TenantPricingSettingsMode;
use Acronis\Cloud\Client\Model\Pricing\TenantPricingSettingsPut;
use Acronis\Cloud\Client\Model\Tenants\Tenant;
use Acronis\Cloud\Client\Model\Tenants\TenantPost;
use Acronis\Cloud\Client\Model\Tenants\TenantPut;
use Acronis\Cloud\Client\Model\Users\User;
use Acronis\Cloud\Client\Model\Users\UserPost;
use Acronis\Cloud\Client\Model\Users\UserPut;
use AcronisCloud\CloudApi\ApiInterface;
use AcronisCloud\Localization\GetTextTrait;
use AcronisCloud\Model\WHMCS\Product;
use AcronisCloud\Service\Database\Repository\RepositoryAwareTrait;
use AcronisCloud\Service\Language\IsoCode;
use AcronisCloud\Service\Language\IsoCodeAwareTrait;
use AcronisCloud\Util\Arr;
use AcronisCloud\Util\Str;
use Exception;
use WHMCS\Module\Server\AcronisCloud\Exception\ProvisioningException;
use WHMCS\Module\Server\AcronisCloud\Subscription\RequestParameters\Accessor;
use WHMCS\Module\Server\AcronisCloud\Subscription\RequestParameters\ClientsDetails;

class TenantManager
{
    use GetTextTrait,
        IsoCodeAwareTrait,
        RepositoryAwareTrait;

    const CUSTOMER_ID_WHMCS_VERSION = 'whmcs-version';
    const CUSTOMER_ID_WHMCS_USER_ID = 'whmcs-user-id';
    const CUSTOMER_ID_WHMCS_PRODUCT_ID = 'whmcs-product-id';
    const CUSTOMER_ID_WHMCS_SERVICE_ID = 'whmcs-service-id';

    const ROLE_ADMIN = 'admin';
    const ROLE_USER = 'user';

    /** @var Accessor */
    private $requestParameters;
    /** @var ApiInterface */
    private $cloudApi;

    public function __construct(Accessor $requestParameters, ApiInterface $cloudApi)
    {
        $this->requestParameters = $requestParameters;
        $this->cloudApi = $cloudApi;
    }

    /**
     * @param string $tenantKind
     * @return Tenant
     * @throws ProvisioningException
     */
    public function createTenant($tenantKind)
    {
        $tenantData = new TenantPost();
        $tenantData->setKind($tenantKind);
        $tenantData->setParentId($this->getTenantParentId());
        $tenantData->setCustomerId($this->getTenantCustomerId());
        $tenantData->setName($this->getTenantName());
        $tenantData->setContact($this->getTenantContact());
        $tenantData->setLanguage($this->getTenantLanguage());

        return $this->getCloudApi()->createTenant($tenantData);
    }

    /**
     * @param Tenant $tenant
     * @return Tenant
     * @throws ProvisioningException
     */
    public function updateTenant($tenant)
    {
        $currentCustomerId = $tenant->hasCustomerId()
            ? $tenant->getCustomerId()
            : null;

        $tenantData = new TenantPut();
        $tenantData->setVersion($tenant->getVersion());
        $tenantData->setCustomerId($this->getTenantCustomerId($currentCustomerId));
        $tenantData->setName($this->getTenantName($tenant->getName()));
        $tenantData->setContact($this->getTenantContact());
        $tenantData->setLanguage($this->getTenantLanguage());

        return $this->getCloudApi()->updateTenant($tenant->getId(), $tenantData);
    }

    /**
     * @param Tenant $tenant
     * @throws Exception
     */
    public function updateTenantPricingMode($tenant)
    {
        $pricingMode = $this->getTenantPricingMode();

        $tenantId = $tenant->getId();
        $cloudApi = $this->getCloudApi();
        $pricingSettings = $cloudApi->getTenantPricing($tenantId);
        if ($pricingSettings->getMode() === $pricingMode) {
            return;
        }

        $pricingSettingsPut = new TenantPricingSettingsPut();
        $pricingSettingsPut->setMode($pricingMode);
        $pricingSettingsPut->setVersion($pricingSettings->getVersion());

        $cloudApi->updateTenantPricing($tenantId, $pricingSettingsPut);
    }

    /**
     * @param $tenantId
     * @param $login
     * @return User
     * @throws ProvisioningException
     */
    public function createUser($tenantId, $login)
    {
        $userBody = new UserPost();
        $userBody->setTenantId($tenantId);
        $userBody->setLogin($this->resolveUserLogin($login));
        $userBody->setLanguage($this->getTenantLanguage());
        $userBody->setContact($this->getTenantContact());

        return $this->getCloudApi()->createUser($userBody);
    }

    /**
     * @param User $user
     * @return User
     * @throws ProvisioningException
     */
    public function updateUser($user)
    {
        $userBody = new UserPut();
        $userBody->setVersion($user->getVersion());
        $userBody->setLanguage($this->getTenantLanguage());
        $userBody->setContact($this->getTenantContact());

        return $this->getCloudApi()->updateUser($user->getId(), $userBody);
    }

    /**
     * @param User $user
     * @param Tenant $tenant
     * @param string $userRole
     */
    public function updateUserRoles($user, $tenant, $userRole)
    {
        $cloudApi = $this->getCloudApi();
        $platformId = $cloudApi->getApplicationByType(ApiInterface::APPLICATION_TYPE_PLATFORM)
            ->getId();

        $roles = $cloudApi->getTenantApplicationsRoles($user->getTenantId());
        $userRoles = [];
        foreach ($roles as $role) {
            $roleName = $role->getRole();

            // skip all roles for Management Portal
            if ($role->getApplicationId() === $platformId) {
                continue;
            }

            // enable all roles for other services
            $userRoles[] = $roleName;
        }

        // enable roles for Management Portal based on the template
        $isAdmin = $userRole === static::ROLE_ADMIN;
        $tenantKind = $tenant->getKind();
        if ($tenantKind === ApiInterface::TENANT_KIND_CUSTOMER) {
            if ($isAdmin) {
                $userRoles[] = Role::COMPANY_ADMIN;
            }
        } elseif ($tenantKind === ApiInterface::TENANT_KIND_PARTNER) {
            $userRoles[] = $isAdmin
                ? Role::PARTNER_ADMIN
                : Role::READONLY_ADMIN;
        }

        $accessPolicies = array_map(function ($role) use ($user) {
            $accessPolicy = new AccessPolicy();
            $accessPolicy->setId(ApiInterface::NULL_UUID)
                ->setIssuerId(ApiInterface::NULL_UUID)
                ->setTrusteeId($user->getId())
                ->setTrusteeType(AccessPolicyTrusteeType::USER)
                ->setVersion(0)
                ->setTenantId($user->getTenantId())
                ->setRoleId($role);

            return $accessPolicy;
        }, $userRoles);

        $cloudApi->updateUserAccessPolicies($user->getId(), $accessPolicies);
    }

    /**
     * @param string|null $currentTenantName
     * @return string
     */
    public function getTenantName($currentTenantName = null)
    {
        $clientsDetails = $this->getRequestParameters()
            ->getClientsDetails();

        $clientName = $clientsDetails->getCompanyName()
            ?: Str::format(
                '%s %s',
                $clientsDetails->getFirstName(), $clientsDetails->getLastName()
            );
        $clientName = trim($clientName);

        $pattern = Str::format('/^%s(?: \/(\d+))?$/i', preg_quote($clientName, '/'));
        if ($currentTenantName && preg_match($pattern, $currentTenantName)) {
            return $currentTenantName;
        }

        $parentTenantId = $this->getTenantParentId();

        $suffixes = [];
        $cloudApi = $this->getCloudApi();
        //TODO: Optimize it. Fetch only tenants whose names start from $clientName
        $children = $cloudApi->fetchTenantChildren($parentTenantId);
        foreach ($children as $child) {
            if (!preg_match($pattern, $child->getName(), $matches)) {
                continue;
            }

            $suffixes[] = (int)Arr::get($matches, 1, 0);
        }

        $tenantName = $suffixes
            ? Str::format('%s /%s', $clientName, max($suffixes) + 1)
            : $clientName;

        return $tenantName;
    }

    /**
     * @return Contact
     * @throws ProvisioningException
     */
    public function getTenantContact()
    {
        $clientsDetails = $this->getRequestParameters()
            ->getClientsDetails();
        $this->validateClientDetails($clientsDetails);

        $contact = new Contact();

        $contact->setAddress1($clientsDetails->getAddress1());
        $contact->setAddress2($clientsDetails->getAddress2());
        $contact->setCity($clientsDetails->getCity());
        $contact->setCountry($clientsDetails->getCountry());
        $contact->setEmail($clientsDetails->getEmail());
        $contact->setFirstname($clientsDetails->getFirstName());
        $contact->setLastname($clientsDetails->getLastName());
        $contact->setPhone($clientsDetails->getPhoneNumber());
        $contact->setState($clientsDetails->getState());
        $contact->setZipcode($clientsDetails->getPostcode());

        return $contact;
    }

    /**
     * @param string|null $currentCustomerId
     * @return string
     */
    public function getTenantCustomerId($currentCustomerId = null)
    {
        $properties = $currentCustomerId
            ? Arr::decode($currentCustomerId)
            : [];

        $parameters = $this->getRequestParameters();

        $properties[static::CUSTOMER_ID_WHMCS_VERSION] = $parameters->getWhmcsVersion();
        $properties[static::CUSTOMER_ID_WHMCS_USER_ID] = $parameters->getUserId();
        $properties[static::CUSTOMER_ID_WHMCS_PRODUCT_ID] = $parameters->getProductId();
        $properties[static::CUSTOMER_ID_WHMCS_SERVICE_ID] = $parameters->getServiceId();

        return Arr::encode($properties);
    }

    /**
     * @return string
     */
    public function getTenantLanguage()
    {
        // get client language
        $language = $this->getRequestParameters()->getClientsDetails()->getLanguage();
        // if client language is not specified (it means to use default setting)
        if (!$language) {
            // get default setting for language
            $language = $this->getRepository()->getConfigurationRepository()->getLanguage();
            $language = $language ?: IsoCode::NAME_EN;
        }

        return $this->getLanguageIsoCode()->getCode($language, IsoCode::CODE_EN);
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getTenantPricingMode()
    {
        $productId = $this->getRequestParameters()->getProductId();
        $product = $this->getRepository()->getProductRepository()->find($productId);
        if (!$product) {
            throw new ProvisioningException(
                $this->gettext('Cannot find the product with the ID {0}.',
                    [$productId]
                )
            );
        }

        return $product->paytype === Product::PAY_TYPE_FREE
            ? TenantPricingSettingsMode::TRIAL
            : TenantPricingSettingsMode::PRODUCTION;
    }

    /**
     * @return string
     */
    public function getTenantParentId()
    {
        $cloudApi = $this->getCloudApi();
        $me = $cloudApi->getMe();

        return $me->getTenantId();
    }

    /**
     * @param $orderLogin
     * @return string
     * @throws ProvisioningException
     */
    protected function resolveUserLogin($login)
    {
        if (!$login) {
            return $this->generateUserLogin();
        }

        $this->checkUserLogin($login);

        return $login;
    }

    /**
     * @return string
     */
    private function generateUsername()
    {
        return 'W' . strtoupper(bin2hex(openssl_random_pseudo_bytes(4)));
    }

    /**
     * @return string
     * @throws ProvisioningException
     */
    private function generateUserLogin()
    {
        $cloudApi = $this->getCloudApi();
        for ($i = 1; $i <= 10; $i++) {
            $login = $this->generateUsername();
            if (!$cloudApi->checkLogin($login)) {
                return $login;
            }
        }

        throw new ProvisioningException('Cannot generate the login for the user.');
    }

    /**
     * @param $login
     * @throws ProvisioningException
     */
    private function checkUserLogin($login)
    {
        if (!preg_match('/^[a-zA-Z0-9\.\_\-\+\@]+$/', $login)) {
            throw new ProvisioningException($this->gettext('The login can consist of uppercase and lowercase Latin letters (a-z, A-Z) (ASCII:65-90, 97-122), digits 0 to 9 (ASCII: 48-57), special characters: period symbol ("."), underscore symbol ("_"), hyphen symbol ("-"), plus symbol ("+"), and symbol "@"'));
        }

        if ($this->getCloudApi()->checkLogin($login)) {
            throw new ProvisioningException($this->gettext('The login "{0}" is already used for another client.',
                [$login]
            ));
        }
    }

    /**
     * @return ApiInterface
     */
    private function getCloudApi()
    {
        return $this->cloudApi;
    }

    /**
     * @return Accessor
     */
    private function getRequestParameters()
    {
        return $this->requestParameters;
    }

    /**
     * @param ClientsDetails $clientDetails
     * @throws ProvisioningException
     */
    private function validateClientDetails($clientDetails)
    {
        if (!$clientDetails->getEmail()) {
            // for WHMCS, we always need an email
            throw new ProvisioningException($this->gettext('The user email is required for provisioning.'));
        }
    }
}