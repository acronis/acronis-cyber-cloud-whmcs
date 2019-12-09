<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace WHMCS\Module\Server\AcronisCloud\Controller;

use Acronis\Cloud\Client\HttpException;
use Acronis\Cloud\Client\Model\Applications\Application;
use Acronis\Cloud\Client\Model\Infra\Infra;
use Acronis\Cloud\Client\Model\Tenants\OfferingItem;
use Acronis\Cloud\Client\Model\Tenants\Tenant;
use Acronis\Cloud\Client\Model\Tenants\TenantPut;
use Acronis\Cloud\Client\Model\Users\User;
use AcronisCloud\CloudApi\Api;
use AcronisCloud\CloudApi\ApiInterface;
use AcronisCloud\CloudApi\CloudApiTrait;
use AcronisCloud\CloudApi\CloudServerException;
use AcronisCloud\CloudApi\CloudServerInterface;
use AcronisCloud\Localization\GetTextTrait;
use AcronisCloud\Model\Template;
use AcronisCloud\Model\TemplateApplication;
use AcronisCloud\Model\TemplateOfferingItem;
use AcronisCloud\Service\Database\Repository\RepositoryAwareTrait;
use AcronisCloud\Service\Dispatcher\AbstractController;
use AcronisCloud\Service\Dispatcher\ActionInterface;
use AcronisCloud\Service\Dispatcher\RequestInterface;
use AcronisCloud\Service\Dispatcher\Response\DataResponse;
use AcronisCloud\Service\Dispatcher\Response\StatusCodeInterface;
use AcronisCloud\Service\Dispatcher\ResponseInterface;
use AcronisCloud\Service\Errors\ErrorNotification;
use AcronisCloud\Service\Errors\ProvisioningErrorsAwareTrait;
use AcronisCloud\Service\Logger\LoggerAwareTrait;
use AcronisCloud\Service\MetaInfo\MetaInfoAwareTrait;
use AcronisCloud\Util\Arr;
use AcronisCloud\Util\MemoizeTrait;
use AcronisCloud\Util\Str;
use AcronisCloud\Util\UomConverter;
use Exception;
use RuntimeException;
use WHMCS\Module\Server\AcronisCloud\Exception\ProvisioningException;
use WHMCS\Module\Server\AcronisCloud\Product\CustomFields;
use WHMCS\Module\Server\AcronisCloud\Subscription\RequestParameters\Accessor;
use WHMCS\Module\Server\AcronisCloud\Subscription\RequestParameters\ProductOptions;
use WHMCS\Module\Server\AcronisCloud\Subscription\TenantManager;

class Subscription extends AbstractController
{
    use CloudApiTrait,
        GetTextTrait,
        LoggerAwareTrait,
        MemoizeTrait,
        MetaInfoAwareTrait,
        ProvisioningErrorsAwareTrait,
        RepositoryAwareTrait;

    const UUID_PATTERN = '/^&|^[a-f\d]{8}-(?:[a-f\d]{4}-){3}[a-f\d]{12}&/i';

    /** @var RequestInterface */
    private $request;

    /**
     * @param Exception $e
     * @param ActionInterface $action
     * @param RequestInterface $request
     * @return ResponseInterface|mixed
     */
    public function handleException(Exception $e, ActionInterface $action, RequestInterface $request)
    {
        if ($e instanceof ProvisioningException) {
            return $e->getMessage();
        }

        if ($e instanceof CloudServerException) {
            $errorCode = $e->getCode();
            $serverId = $e->getServer() ? $e->getServer()->getId() : '';
            if ($errorCode & CloudServerException::CODE_EMPTY_SERVER) {
                return $this->gettext('Please select a server.');
            } elseif ($errorCode & CloudServerException::CODE_EMPTY_HOSTNAME) {
                return $this->gettext('Please specify Hostname for server {0}.', [
                    $serverId,
                ]);
            } elseif ($errorCode & CloudServerException::CODE_EMPTY_USERNAME) {
                return $this->gettext('Please specify Username for server {0}.', [
                    $serverId,
                ]);
            } elseif ($errorCode & CloudServerException::CODE_EMPTY_PASSWORD) {
                return $this->gettext('Please specify Password for server {0}.', [
                    $serverId,
                ]);
            }
        }

        if ($e instanceof HttpException) {
            $response = $e->getResponseBody();
            $errors = isset($response->error->details) ? $response->error->details : $response;

            return json_encode($errors);
        }

        return $this->gettext('Error: {0}', [$e->getMessage()]);
    }

    /**
     * @param RequestInterface $request
     * @return string
     * @throws HttpException
     * @throws ProvisioningException
     * @throws CloudServerException
     * @throws Exception
     */
    public function createOrUpdate($request)
    {
        try {
            $this->setRequest($request);

            $customFields = $this->getCustomFields();

            // create a tenant
            $tenantId = $customFields->getTenantId();
            if ($tenantId) {
                $tenant = $this->getTenant($tenantId);
                $this->checkTenant($tenant);
                $tenant = $this->updateTenant($tenant);
            } else {
                $tenant = $this->createTenant();
                $tenantId = $tenant->getId();
                $customFields->setTenantId($tenantId);
            }

            $this->updateApplicationsAndOfferingItems($tenant);
            $this->updateTenantPricingMode($tenant);

            // create a user
            $userId = $customFields->getUserId();
            if ($userId) {
                $user = $this->getUser($userId);
                $this->checkUserTenant($tenantId, $user);
                $user = $this->updateUser($user);
            } else {
                $login = $customFields->getCloudLogin();
                $user = $this->createUser($tenantId, $login);
                $customFields->setUserId($user->getId());
                $this->activateUser($user);
            }

            $customFields->setCloudLogin($user->getLogin());
            $this->updateUserRoles($user, $tenant);

            return DataResponse::SUCCESS;
        } catch (Exception $e) {
            // workaround for admin page's Upgrade/Downgrade and client area hiding errors
            if (!$this->isWhmcsAdmin() || strpos($request->getRequestUrl(), '/clientsupgrade.php') !== false) {
                $errorTitle = 'Acronis Cyber Cloud: ' . $this->gettext('Provisioning purchase failed');
                $error = new ErrorNotification($errorTitle, $e->getMessage());
                $this->getProvisioningErrorsManager()->setErrors([$error])->flush();
            }

            throw $e;
        }
    }

    /**
     * @param RequestInterface $request
     * @return string
     * @throws HttpException
     * @throws ProvisioningException
     * @throws CloudServerException
     * @throws Exception
     */
    public function suspend($request)
    {
        $this->setRequest($request);

        $customFields = $this->getCustomFields();
        $tenantId = $customFields->getTenantId();
        if (!$tenantId) {
            return DataResponse::SUCCESS;
        }

        $tenant = $this->getTenant($tenantId);

        if (!$tenant->getEnabled()) {
            return DataResponse::SUCCESS;
        }

        $tenantPut = new TenantPut();
        $tenantPut->setEnabled(false);
        $tenantPut->setVersion($tenant->getVersion());

        $cloudApi = $this->getCloudApi();
        $cloudApi->updateTenant($tenantId, $tenantPut);

        return DataResponse::SUCCESS;
    }

    /**
     * @param RequestInterface $request
     * @return string
     * @throws HttpException
     * @throws ProvisioningException
     * @throws CloudServerException
     * @throws Exception
     */
    public function unsuspend($request)
    {
        $this->setRequest($request);

        $customFields = $this->getCustomFields();
        $tenantId = $customFields->getTenantId();
        if (!$tenantId) {
            throw new ProvisioningException($this->gettext('Cannot enable a non-provisioned service.'));
        }

        $tenant = $this->getTenant($tenantId);

        if ($tenant->getEnabled()) {
            return DataResponse::SUCCESS;
        }

        $tenantPut = new TenantPut();
        $tenantPut->setEnabled(true);
        $tenantPut->setVersion($tenant->getVersion());

        $cloudApi = $this->getCloudApi();
        $cloudApi->updateTenant($tenantId, $tenantPut);

        return DataResponse::SUCCESS;
    }

    /**
     * @param RequestInterface $request
     * @return string
     * @throws HttpException
     * @throws ProvisioningException
     * @throws CloudServerException
     * @throws Exception
     */
    public function terminate($request)
    {
        $this->setRequest($request);

        $customFields = $this->getCustomFields();
        $tenantId = $customFields->getTenantId();
        if (!$tenantId) {
            return DataResponse::SUCCESS;
        }

        $tenant = $this->getTenant($tenantId);

        $cloudApi = $this->getCloudApi();
        if ($tenant->getEnabled()) {
            $tenantPut = new TenantPut();
            $tenantPut->setEnabled(false);
            $tenantPut->setVersion($tenant->getVersion());

            $tenant = $cloudApi->updateTenant($tenantId, $tenantPut);
        }

        $cloudApi->deleteTenant($tenantId, $tenant->getVersion());

        $customFields->resetTenantId()
            ->resetUserId();

        return DataResponse::SUCCESS;
    }

    /**
     * @return CloudServerInterface
     */
    protected function getCloudServer()
    {
        return $this->getService()->cloudServer;
    }

    /**
     * @param RequestInterface $request
     * @return $this
     */
    private function setRequest(RequestInterface $request)
    {
        if ($this->request) {
            throw new RuntimeException('The property "request" is already set.');
        }

        $this->request = $request;

        return $this;
    }

    /**
     * @return RequestInterface
     */
    private function getRequest()
    {
        return $this->request;
    }

    /**
     * @return Accessor
     */
    private function getRequestParameters()
    {
        return $this->memoize(function () {
            return new Accessor($this->getRequest()->getParameters());
        });
    }

    /**
     * @return CustomFields
     */
    private function getCustomFields()
    {
        return $this->memoize(function () {
            $parameters = $this->getRequestParameters();
            $productId = $parameters->getProductId() ?: $this->getService()->product->getId();
            $customFields = new CustomFields($productId, $parameters->getServiceId());

            return $customFields;
        });
    }

    /**
     * @param string $tenantId
     * @return Tenant
     * @throws HttpException
     * @throws ProvisioningException
     * @throws CloudServerException
     */
    private function getTenant($tenantId)
    {
        $cloudApi = $this->getCloudApi();
        try {
            return $cloudApi->getTenant($tenantId);
        } catch (HttpException $e) {
            if ($e->getCode() === StatusCodeInterface::HTTP_NOT_FOUND) {
                $serverId = $this->getCloudServer()->getId();

                throw new ProvisioningException(
                    $this->gettext('Unable to find the tenant "{0}" on the server {1}. Please check that the correct server is selected. If the tenant has been manually removed, please empty the custom field "{2}" and continue the operation.',
                        [$tenantId, $serverId, CustomFields::FIELD_NAME_TENANT_ID]
                    )
                );
            }

            throw $e;
        }
    }

    /**
     * @return Tenant
     * @throws ProvisioningException
     */
    private function createTenant()
    {
        $template = $this->getTemplate();
        $templateServerId = $template->getServerId();
        $tenantKind = $template->getTenantKind();

        $server = $this->getCloudServer();
        if (!$server || $server->getId() != $templateServerId) {
            $service = $this->getService();
            $service->setServerId($templateServerId);
            $service->save();
        }

        return $this->getTenantManager()->createTenant($tenantKind);
    }

    /**
     * @param Tenant $tenant
     * @return Tenant
     * @throws CloudServerException
     * @throws ProvisioningException
     */
    private function updateTenant($tenant)
    {
        return $this->getTenantManager()->updateTenant($tenant);
    }

    /**
     * @param Tenant $tenant
     * @throws Exception
     */
    private function updateTenantPricingMode($tenant)
    {
        $this->getTenantManager()->updateTenantPricingMode($tenant);
    }

    /**
     * @return Template
     */
    private function getTemplate()
    {
        return $this->memoize(function () {
            $productOptions = $this->getRequestParameters()->getProductOptions();
            $templateId = $productOptions->getTemplateId();

            $templateRepository = $this->getRepository()->getTemplateRepository();

            return $templateRepository->find($templateId);
        });
    }

    /**
     * @return TenantManager
     */
    private function getTenantManager()
    {
        return $this->memoize(function () {
            return new TenantManager($this->getRequestParameters(), $this->getCloudApi());
        });
    }

    /**
     * @param string $userId
     * @return User
     * @throws HttpException
     * @throws ProvisioningException
     * @throws CloudServerException
     */
    private function getUser($userId)
    {
        try {
            return $this->getCloudApi()->getUser($userId);
        } catch (HttpException $e) {
            if ($e->getCode() === StatusCodeInterface::HTTP_NOT_FOUND) {
                throw new ProvisioningException(
                    $this->gettext('Unable to find the user "{0}". Please check the user ID or clear the custom field "{2}" to create a new account.',
                        [$userId, CustomFields::FIELD_NAME_USER_ID]
                    )
                );
            }

            throw $e;
        }
    }

    /**
     * @param User $user
     * @return User
     * @throws CloudServerException
     * @throws ProvisioningException
     */
    private function updateUser($user)
    {
        return $this->getTenantManager()->updateUser($user);
    }

    /**
     * @param int $tenantId
     * @param string $login Login set in order form (using custom field)
     * @return User
     * @throws ProvisioningException
     * @throws CloudServerException
     */
    private function createUser($tenantId, $login)
    {
        return $this->getTenantManager()->createUser($tenantId, $login);
    }

    /**
     * @param User $user
     * @param Tenant $tenant
     * @throws CloudServerException
     */
    private function updateUserRoles($user, $tenant)
    {
        $this->getTenantManager()->updateUserRoles(
            $user,
            $tenant,
            $this->getTemplate()->isAdministrator()
                ? TenantManager::ROLE_ADMIN
                : TenantManager::ROLE_USER
        );
    }

    /**
     * @param string $tenantId
     * @param User $user
     * @throws ProvisioningException
     */
    private function checkUserTenant($tenantId, User $user)
    {
        if ($user->getTenantId() !== $tenantId) {
            throw new ProvisioningException($this->gettext('The user "{0}" does not belong to the tenant "{1}".',
                [$user->getId(), $tenantId]
            ));
        }
    }

    /**
     * @param User $user
     * @throws CloudServerException
     * @throws ProvisioningException
     * @throws Exception
     */
    private function activateUser(User $user)
    {
        if ($user->getActivated()) {
            return;
        }

        $login = $user->getLogin();
        $email = $user->getContact()->getEmail();

        $customFields = $this->getCustomFields();
        $cloudApi = $this->getCloudApi();

        $activationMethod = $this->getRequestParameters()
            ->getProductOptions()
            ->getActivationMethod();

        if ($activationMethod === ProductOptions::ACTIVATION_METHOD_PASSWORD) {
            $password = $customFields->getCloudPassword();

            if (!$password) {
                throw new ProvisioningException($this->gettext('Cannot activate the user without a password.'));
            }

            $cloudApi->activateUser($login, $email, $password);
            $customFields->resetCloudPassword();
        } else {
            $cloudApi->sendActivationEmail($login, $email);
        }
    }

    /**
     * @param Tenant $tenant
     * @throws Exception
     */
    private function updateApplicationsAndOfferingItems($tenant)
    {
        $this->validateOrderServer();

        $applicationsFromTemplate = $this->getApplicationsFromTemplate();
        $offeringItemsFromTemplate = $this->getOfferingItemsFromTemplate($tenant);

        $offeringItemsFromConfigurableOptions = $this->getOfferingItemsFromConfigurableOptions(
            $tenant, $offeringItemsFromTemplate
        );
        $applicationsFromConfigurableOptions = $this->getApplicationsFromOfferingItems($offeringItemsFromConfigurableOptions);

        $applications = $applicationsFromConfigurableOptions + $applicationsFromTemplate;

        $offeringItemsToEnable = Api::mergeOfferingItems(
            $offeringItemsFromConfigurableOptions,
            $offeringItemsFromTemplate
        );

        $this->checkDependencyDisabled($offeringItemsToEnable);
        $cloudApi = $this->getCloudApi();

        foreach ($applications as $application) {
            $cloudApi->enableApplicationForTenantsChain(
                $cloudApi->getRootTenantId(),
                $tenant->getId(),
                $application->getId()
            );
        }

        $tenantOfferingItems = $cloudApi->getTenantOfferingItems($tenant->getId());
        $offeringItemsToDisable = $this->resolveOfferingItemsToDisable($tenantOfferingItems, $offeringItemsToEnable);

        $offeringItemsToUpdate = array_merge($offeringItemsToEnable, $offeringItemsToDisable);

        $cloudApi->updateOfferingItemsForTenantsChain(
            $cloudApi->getRootTenantId(),
            $tenant->getId(),
            $offeringItemsToUpdate
        );
    }

    /**
     * @param Tenant $tenant
     * @param OfferingItem[] $offeringItemsFromTemplate
     * @return OfferingItem[]
     * @throws Exception
     */
    private function getOfferingItemsFromConfigurableOptions($tenant, array $offeringItemsFromTemplate)
    {
        $cloudApi = $this->getCloudApi();
        $rootTenantId = $cloudApi->getRootTenantId();
        $tenantKind = $tenant->getKind();

        $configurableOptions = $this->getRequestParameters()->getConfigurableOptions();

        $metaInfo = $this->getMetaInfo();
        $overageRatio = $tenantKind === Api::TENANT_KIND_CUSTOMER
            ? $this->getCloudApi()->getOfferingItemsOverageRatio()
            : 1;
        $offeringItems = [];
        foreach ($configurableOptions as $option) {
            $offeringItemName = $option->getOfferingItemName();
            $offeringItemMeta = $metaInfo->getOfferingItemMeta($offeringItemName);
            if (!$offeringItemMeta) {
                throw new ProvisioningException(
                    $this->gettext('The unsupported offering item "{0}" is used for the configurable option "{1}".', [
                        $offeringItemName,
                        $option->getName(),
                    ]),
                    StatusCodeInterface::HTTP_BAD_REQUEST
                );
            }

            $optionMeasurementUnit = $option->getMeasurementUnit();
            $optionMeasurementKind = UomConverter::getMeasurementKind($optionMeasurementUnit);
            if (!$optionMeasurementKind) {
                throw new ProvisioningException(
                    $this->gettext('The unsupported measurement unit "{0}" is used for the configurable option "{1}".',
                        [
                            $optionMeasurementUnit,
                            $option->getName(),
                        ]),
                    StatusCodeInterface::HTTP_BAD_REQUEST
                );
            }

            $offeringItemInfraId = $option->getInfraId();
            if (preg_match(static::UUID_PATTERN, $offeringItemInfraId)) {
                throw new ProvisioningException(
                    $this->gettext('The invalid infrastructure component ID "{0}" is specified for the configurable option "{1}". Please specify UUID or leave blank.',
                        [
                            $offeringItemInfraId,
                            $option->getName(),
                        ]),
                    StatusCodeInterface::HTTP_BAD_REQUEST
                );
            }

            $applicationType = $offeringItemMeta->getApplicationType();
            $application = $cloudApi->getApplicationByType($applicationType);
            if (!$application) {
                throw new ProvisioningException(
                    $this->gettext('The offering item "{0}" requires the enabled application "{1}". Please enable this application for your root tenant or edit the configurable option "{3}" for the product.',
                        [
                            $offeringItemName,
                            $applicationType,
                            $option->getName(),
                        ]),
                    StatusCodeInterface::HTTP_NOT_FOUND
                );
            }

            if (!$offeringItemInfraId && $offeringItemMeta->isInfra()) {
                if ($tenantKind === Api::TENANT_KIND_PARTNER) {
                    throw new ProvisioningException(
                        $this->gettext('The infrastructure component ID is required for the configurable option "{0}". Please specify the ID for the configurable option.',
                            [$option->getName()]
                        )
                    );
                }

                $offeringItemInfraId = $this->resolveInfraIdByTemplateOfferingItems(
                    $offeringItemsFromTemplate,
                    $offeringItemName
                );

                if (!$offeringItemInfraId) {
                    if ($offeringItemMeta->getCapability() === 'disaster_recovery') {
                        $offeringItemInfraId = $this->resolveDisasterRecoveryInfraId($offeringItemsFromTemplate);
                    }
                }

                if (!$offeringItemInfraId) {
                    throw new ProvisioningException(
                        $this->gettext('Cannot resolve the infrastructure component ID for the configurable option "{0}". Please specify the ID for the configurable option or enable the offering item "{1}" for the template {2}.',
                            [
                                $option->getName(),
                                $offeringItemMeta->getOfferingItemFriendlyName(),
                                $this->getTemplate()->getId(),
                            ]
                        )
                    );
                }
            }

            if ($offeringItemInfraId && !$cloudApi->isInfraAvailableForRootTenant($offeringItemInfraId)) {
                throw new ProvisioningException(
                    $this->gettext('An unknown infrastructure component "{0}". Please enable this infrastructure component for your root tenant or edit the configurable option "{1}".',
                        [
                            $offeringItemInfraId,
                            $option->getName(),
                        ]),
                    StatusCodeInterface::HTTP_NOT_FOUND
                );
            }

            $tenantOfferingItem = $cloudApi->getRootTenantOfferingItem($offeringItemName, $offeringItemInfraId);
            if (!$tenantOfferingItem) {
                throw new ProvisioningException(
                    $this->gettext('The offering item "{0}" is not enabled. Please enable this offering item for your root tenant or edit the configurable option "{1}" for the product.',
                        [
                            $offeringItemName,
                            $option->getName(),
                        ]),
                    StatusCodeInterface::HTTP_NOT_FOUND
                );
            }

            if (!$cloudApi->isOfferingItemAvailableForChild($rootTenantId, $tenantKind, $offeringItemName)) {
                throw new ProvisioningException(
                    $this->gettext('Cannot enable offering items "{0}" for a tenant of type "{1}". Please remove or edit the configurable option "{2}".',
                        [
                            $offeringItemName,
                            $tenantKind,
                            $option->getName(),
                        ]),
                    StatusCodeInterface::HTTP_BAD_REQUEST
                );
            }

            $optionValue = $option->getValue();
            // skip options with empty values
            if ($optionValue == 0) {
                continue;
            }

            $quotaValue = false;
            $overage = 0;
            if ($optionMeasurementUnit !== UomConverter::FEATURE) {
                if ($tenantOfferingItem->getType() === ApiInterface::OFFERING_ITEM_TYPE_FEATURE) {
                    throw new ProvisioningException(
                        $this->gettext('Unable to specify any quota for the feature offering item "{0}". Please change the measurement unit "{1}" to "{2}" for the configurable option "{3}".',
                            [
                                $offeringItemName,
                                $optionMeasurementUnit,
                                UomConverter::FEATURE,
                                $option->getName(),
                            ]),
                        StatusCodeInterface::HTTP_BAD_REQUEST
                    );
                }

                $offeringItemMeasurementUnit = $tenantOfferingItem->getMeasurementUnit();
                $offeringItemMeasurementKind = UomConverter::getMeasurementKind($offeringItemMeasurementUnit);
                if ($offeringItemMeasurementKind !== $optionMeasurementKind) {
                    throw new ProvisioningException(
                        $this->gettext('Unable to convert the configurable option measurement unit "{0}" to the offering item measurement unit "{1}" for the configurable option "{2}".',
                            [
                                $optionMeasurementUnit,
                                $offeringItemMeasurementUnit,
                                $option->getName(),
                            ]),
                        StatusCodeInterface::HTTP_BAD_REQUEST
                    );
                }

                $quotaValue = UomConverter::convert(
                    $optionValue,
                    $optionMeasurementUnit,
                    $offeringItemMeasurementUnit
                );
                $overage = $this->calculateOverage($quotaValue, $overageRatio);
            }

            $offeringItems[] = Api::createOfferingItem(
                $application->getId(),
                $offeringItemName,
                $offeringItemInfraId,
                $quotaValue,
                $overage,
                ApiInterface::OFFERING_ITEM_STATUS_ACTIVE
            );
        }

        return $offeringItems;
    }

    /**
     * @param OfferingItem[] $offeringItems
     * @return Application[]
     * @throws Exception
     */
    private function getApplicationsFromOfferingItems(array $offeringItems)
    {
        $cloudApi = $this->getCloudApi();
        $metaInfo = $this->getMetaInfo();
        $applications = [];
        foreach ($offeringItems as $offeringItem) {
            $offeringItemName = $offeringItem->getName();
            $offeringItemMeta = $metaInfo->getOfferingItemMeta($offeringItemName);
            if (!$offeringItemMeta) {
                $this->getLogger()->warning('Unsupported offering item "{0}".', [
                    $offeringItemName,
                ]);

                continue;
            }

            $applicationType = $offeringItemMeta->getApplicationType();
            $application = $cloudApi->getApplicationByType($applicationType);
            if (!$application) {
                $this->getLogger()->warning('Unsupported application type "{0}".', [
                    $applicationType,
                ]);

                continue;
            }

            $applications[$applicationType] = $application;
        }

        return $applications;
    }

    /**
     * @return Application[]
     * @throws Exception
     */
    private function getApplicationsFromTemplate()
    {
        $cloudApi = $this->getCloudApi();
        $template = $this->getTemplate();
        $templateApplications = $template->applications()
            ->where(TemplateApplication::COLUMN_STATUS, TemplateApplication::STATUS_ACTIVE)
            ->get();

        $applications = [];
        foreach ($templateApplications as $templateApplication) {
            $applicationType = $templateApplication->type;
            $application = $cloudApi->getApplicationByType($applicationType);

            if (!$application) {
                $this->getLogger()->warning('Unsupported application type "{0}".', [
                    $applicationType,
                ]);

                continue;
            }

            $applications[$applicationType] = $application;
        }

        return $applications;
    }

    /**
     * @param Tenant $tenant
     * @return OfferingItem[]
     * @throws Exception
     */
    private function getOfferingItemsFromTemplate($tenant)
    {
        $cloudApi = $this->getCloudApi();
        $rootTenantId = $cloudApi->getRootTenantId();
        $tenantKind = $tenant->getKind();
        $template = $this->getTemplate();
        $metaInfo = $this->getMetaInfo();
        $overageRatio = $tenantKind === Api::TENANT_KIND_CUSTOMER
            ? $this->getCloudApi()->getOfferingItemsOverageRatio()
            : 1;

        $offeringItems = [];

        /** @var TemplateApplication[] $templateApplications */
        $templateApplications = $template->applications()
            ->where(TemplateApplication::COLUMN_STATUS, TemplateApplication::STATUS_ACTIVE)
            ->get();
        foreach ($templateApplications as $templateApplication) {
            /** @var TemplateOfferingItem[] $applicationOfferingItems */
            $applicationOfferingItems = $templateApplication->offeringItems()
                ->where(TemplateOfferingItem::COLUMN_STATUS, TemplateOfferingItem::STATUS_ACTIVE)
                ->get();

            foreach ($applicationOfferingItems as $templateOfferingItem) {
                $offeringItemName = $templateOfferingItem->getName();
                $offeringItemMeta = $metaInfo->getOfferingItemMeta($offeringItemName);
                if (!$offeringItemMeta) {
                    throw new ProvisioningException(
                        $this->gettext('The unsupported offering item "{0}" is used for the template {1}.', [
                            $offeringItemName,
                            $template->getId(),
                        ]),
                        StatusCodeInterface::HTTP_BAD_REQUEST
                    );
                }

                $templateMeasurementUnit = $templateOfferingItem->getMeasurementUnit();
                $templateMeasurementKind = UomConverter::getMeasurementKind($templateMeasurementUnit);
                if (!$templateMeasurementKind) {
                    throw new ProvisioningException(
                        $this->gettext('The unsupported measurement unit "{0}" is used for the offering item "{1}" in the template {2}.',
                            [
                                $templateMeasurementUnit,
                                $offeringItemName,
                                $template->getId(),
                            ]),
                        StatusCodeInterface::HTTP_BAD_REQUEST
                    );
                }

                $applicationType = $templateApplication->getType();
                $application = $cloudApi->getApplicationByType($applicationType);
                if (!$application) {
                    throw new ProvisioningException(
                        $this->gettext('The offering item "{0}" requires the enabled application "{1}". Please enable this application for your root tenant or edit the template {2}.',
                            [
                                $offeringItemName,
                                $applicationType,
                                $template->getId(),
                            ]),
                        StatusCodeInterface::HTTP_NOT_FOUND
                    );
                }

                $offeringItemInfraId = $templateOfferingItem->getInfraId();
                if ($offeringItemInfraId && !$cloudApi->isInfraAvailableForRootTenant($offeringItemInfraId)) {
                    throw new ProvisioningException(
                        $this->gettext('The unknown infrastructure component "{0}" is used for the offering item "{1}". Please enable this infrastructure component for your root tenant or edit the template {2}.',
                            [
                                $offeringItemInfraId,
                                $offeringItemName,
                                $template->getId(),
                            ]),
                        StatusCodeInterface::HTTP_NOT_FOUND
                    );
                }

                $tenantOfferingItem = $cloudApi->getRootTenantOfferingItem($offeringItemName, $offeringItemInfraId);
                if (!$tenantOfferingItem) {
                    throw new ProvisioningException(
                        $this->gettext('The offering item "{0}" is not enabled. Please enable this offering item for your root tenant or edit the template {1}.',
                            [
                                $offeringItemName,
                                $template->getId(),
                            ]),
                        StatusCodeInterface::HTTP_NOT_FOUND
                    );
                }

                if (!$cloudApi->isOfferingItemAvailableForChild($rootTenantId, $tenantKind, $offeringItemName)) {
                    throw new ProvisioningException(
                        $this->gettext('Cannot enable offering items "{0}" for a tenant of type "{1}". Please remove or edit the template {2}.',
                            [
                                $offeringItemName,
                                $tenantKind,
                                $template->getId(),
                            ]),
                        StatusCodeInterface::HTTP_BAD_REQUEST
                    );
                }

                $quotaValue = false;
                $overage = 0;
                if ($templateMeasurementUnit !== UomConverter::FEATURE) {
                    if ($tenantOfferingItem->getType() === ApiInterface::OFFERING_ITEM_TYPE_FEATURE) {
                        throw new ProvisioningException(
                            $this->gettext('Unable to specify any quota for the feature offering item "{0}". Please change the measurement unit "{1}" to "{2}" for the template {3}.',
                                [
                                    $offeringItemName,
                                    $templateMeasurementUnit,
                                    UomConverter::FEATURE,
                                    $template->getId(),
                                ]),
                            StatusCodeInterface::HTTP_BAD_REQUEST
                        );
                    }

                    $offeringItemMeasurementUnit = $tenantOfferingItem->getMeasurementUnit();
                    $offeringItemMeasurementKind = UomConverter::getMeasurementKind($offeringItemMeasurementUnit);
                    if ($offeringItemMeasurementKind !== $templateMeasurementKind) {
                        throw new ProvisioningException(
                            $this->gettext('Unable to convert the measurement unit "{0}" from the template to the measurement unit "{1}" for the offering item "{2}" in the template {3}.',
                                [
                                    $templateMeasurementUnit,
                                    $offeringItemMeasurementUnit,
                                    $offeringItemName,
                                    $template->getId(),
                                ]),
                            StatusCodeInterface::HTTP_BAD_REQUEST
                        );
                    }

                    $templateValue = $templateOfferingItem->getQuotaValue();

                    $quotaValue = $templateValue === ApiInterface::UNLIMITED_OFFERING_ITEM_VALUE
                        ? ApiInterface::UNLIMITED_OFFERING_ITEM_VALUE
                        : UomConverter::convert(
                            $templateValue,
                            $templateMeasurementUnit,
                            $offeringItemMeasurementUnit
                        );
                    $overage = $this->calculateOverage($quotaValue, $overageRatio);
                }

                $offeringItems[] = Api::createOfferingItem(
                    $application->getId(),
                    $offeringItemName,
                    $offeringItemInfraId,
                    $quotaValue,
                    $overage,
                    ApiInterface::OFFERING_ITEM_STATUS_ACTIVE
                );
            }
        }

        return $offeringItems;
    }

    /**
     * @param OfferingItem[] $offeringItems
     * @throws ProvisioningException
     */
    private function checkDependencyDisabled(array $offeringItems)
    {
        $parentOfferingItems = [];
        foreach ($this->getMetaInfo()->getOfferingItemsMeta() as $offeringItemMeta) {
            $offeringItemName = $offeringItemMeta->getOfferingItemName();
            $childOfferingItems = $offeringItemMeta->getChildOfferingItems();
            foreach ($childOfferingItems as $childName) {
                $parentOfferingItems[$childName] = $offeringItemName;
            }
        }

        foreach ($offeringItems as $index => $offeringItem) {
            $name = $offeringItem->getName();
            if (!isset($parentOfferingItems[$name])) {
                continue;
            }
            $parentName = $parentOfferingItems[$name];
            $parent = array_filter($offeringItems, function ($oi) use ($parentName) {
                return $oi->getName() === $parentName;
            });
            if (!$parent) {
                throw new ProvisioningException('The selected configurable option(s) belong to one or more offering items that are disabled.');
            }
        }
    }

    /**
     * @param OfferingItem[] $tenantOfferingItems
     * @param OfferingItem[] $offeringItemsToEnable
     * @return OfferingItem[]
     */
    private function resolveOfferingItemsToDisable(array $tenantOfferingItems, array $offeringItemsToEnable)
    {
        $offeringItemsToDisable = [];
        $metaInfo = $this->getMetaInfo();
        $offeringItemsToEnableHashTable = Api::getOfferingItemsHashTable($offeringItemsToEnable);
        foreach ($tenantOfferingItems as $tenantOfferingItem) {
            // skip offering items which need to update
            $offeringItemHash = Api::getOfferingItemHash($tenantOfferingItem);
            if (Arr::has($offeringItemsToEnableHashTable, $offeringItemHash)) {
                continue;
            }

            // never touch not supported offering items
            if (!$metaInfo->hasOfferingItemMeta($tenantOfferingItem->getName())) {
                continue;
            }

            // don't touch already disabled offering items
            if ($tenantOfferingItem->getStatus() === ApiInterface::OFFERING_ITEM_STATUS_INACTIVE) {
                continue;
            }

            $offeringItemsToDisable[] = Api::createOfferingItem(
                $tenantOfferingItem->getApplicationId(),
                $tenantOfferingItem->getName(),
                $tenantOfferingItem->hasInfraId() ? $tenantOfferingItem->getInfraId() : null,
                false,
                0,
                ApiInterface::OFFERING_ITEM_STATUS_INACTIVE
            );
        }

        return $offeringItemsToDisable;
    }

    /**
     * @param Tenant $tenant
     * @throws ProvisioningException
     */
    private function checkTenant($tenant)
    {
        $template = $this->getTemplate();
        $tenantKind = $template->getTenantKind();
        if ($tenant->getKind() !== $tenantKind) {
            throw new ProvisioningException(
                $this->gettext('Unable to change the tenant type from "{0}" to "{1}" for the tenant "{2}". Please use another template for the product.',
                    [
                        $tenant->getKind(),
                        $tenantKind,
                        $tenant->getId(),
                    ]
                )
            );
        }
    }

    /**
     * @throws ProvisioningException
     */
    private function validateOrderServer()
    {
        $templateServerId = $this->getTemplate()->getServerId();
        $orderServerId = $this->getCloudServer()->getId();
        if ($templateServerId !== $orderServerId) {
            // keep on one line for text collection script
            $errorMsg = $this->gettext('Product upgrade/downgrade cannot be performed using a different server than the order (order server: {0}, used server: {1}). Please, during upgrade/downgrade of an order, make sure that the server used in the product\'s template or in the configurable options is the same as the one in the order.',
                [$orderServerId, $templateServerId]
            );

            throw new ProvisioningException($errorMsg);
        }
    }

    /**
     * @return \AcronisCloud\Model\WHMCS\Service
     */
    private function getService()
    {
        $userId = $this->getRequestParameters()->getUserId();
        $serviceId = $this->getRequestParameters()->getServiceId();

        return $this->getRepository()
            ->getServiceRepository()
            ->getClientServiceWithServersById($userId, $serviceId);
    }

    /**
     * @param int $quotaValue
     * @param float $overageRatio
     * @return float|int
     */
    private function calculateOverage($quotaValue, $overageRatio)
    {
        return $quotaValue * ($overageRatio - 1);
    }

    /**
     * @return bool
     */
    private function isWhmcsAdmin()
    {
        global $_ADMINLANG;

        return !!$_ADMINLANG;
    }

    /**
     * @param OfferingItem[] $offeringItemsFromTemplate
     * @param string $offeringItemName
     * @return string|null
     * @throws ProvisioningException
     */
    private function resolveInfraIdByTemplateOfferingItems(array $offeringItemsFromTemplate, $offeringItemName)
    {
        $templateOfferingItems = array_filter($offeringItemsFromTemplate,
            function ($offeringItem) use ($offeringItemName) {
                /** @var OfferingItem $offeringItem */
                return $offeringItem->getName() === $offeringItemName;
            });
        if (count($templateOfferingItems) > 1) {
            throw new \Exception(Str::format(
                'There are several infrastructure components which are associated with the offering item "%s".',
                $offeringItemName
            ));
        }
        $templateOfferingItem = reset($templateOfferingItems);
        if (!$templateOfferingItem || !$templateOfferingItem->hasInfraId()) {
            return null;
        }

        return $templateOfferingItem->getInfraId();
    }

    private function resolveDisasterRecoveryInfraId($offeringItemsFromTemplate)
    {
        return $this->memoize(function () use ($offeringItemsFromTemplate) {
            // We can enable only one Disaster Recovery infrastructure component for a customer tenant
            // so find the first infrastructure component with the capability 'disaster_recovery'
            $disasterRecoveryInfraId = $this->findInfraIdByCapability($offeringItemsFromTemplate, 'disaster_recovery');
            if (!$disasterRecoveryInfraId) {
                // We can enable only one Backup infrastructure component for a customer tenant
                // so find the first infrastructure component with the capability 'backup'
                $backupInfraId = $this->findInfraIdByCapability($offeringItemsFromTemplate, 'backup');
                if ($backupInfraId) {
                    $disasterRecoveryInfraId = $this->resolveDisasterRecoveryInfraIdByRelativeInfra($backupInfraId);
                }
            }

            return $disasterRecoveryInfraId;
        });
    }

    /**
     * @param OfferingItem[] $offeringItems
     * @param string $capability
     * @return string|null
     */
    private function findInfraIdByCapability($offeringItems, $capability)
    {
        $meta = $this->getMetaInfo();
        $offeringItem = Arr::find($offeringItems, function ($offeringItem) use ($meta, $capability) {
            /** @var OfferingItem $offeringItem */
            if (!$offeringItem->hasInfraId()) {
                return false;
            }
            $offeringItemMeta = $meta->getOfferingItemMeta($offeringItem->getName());

            return $offeringItemMeta && $offeringItemMeta->getCapability() === $capability;
        });

        return $offeringItem ? $offeringItem->getInfraId() : null;
    }

    /**
     * @param string $infraId
     * @return string|null
     */
    private function resolveDisasterRecoveryInfraIdByRelativeInfra($infraId)
    {
        // only one DR infrastructure component can be placed into each location
        // we use this fact to find it by Backup infrastructure component from the same location
        $cloudApi = $this->getCloudApi();
        $tenantInfras = $cloudApi->getRootTenantInfras();
        $infras = array_filter($tenantInfras, function ($infra) use ($infraId) {
            /** @var Infra $infra */
            return $infra->getId() === $infraId;
        });
        $infra = reset($infras);
        if (!$infra) {
            return null;
        }
        $locationId = $infra->getLocationId();
        $disasterRecoveryInfras = array_filter($tenantInfras, function ($infra) use ($locationId) {
            /** @var Infra $infra */
            return $infra->getLocationId() === $locationId
                && in_array('disaster_recovery', $infra->getCapabilities());
        });

        if (count($disasterRecoveryInfras) > 1) {
            // throw an exception if Cloud change this behavior
            throw new \Exception(Str::format(
                'There are several infrastructure components with capability "disaster_recovery" in the location "%s".',
                $locationId
            ));
        }
        $disasterRecoveryInfra = reset($disasterRecoveryInfras);

        return $disasterRecoveryInfra ? $disasterRecoveryInfra->getId() : null;
    }
}