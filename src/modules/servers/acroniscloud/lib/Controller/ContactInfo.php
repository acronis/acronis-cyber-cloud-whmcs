<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace WHMCS\Module\Server\AcronisCloud\Controller;

use AcronisCloud\CloudApi\CloudApiTrait;
use AcronisCloud\Model\WHMCS\Server as CloudServer;
use AcronisCloud\Model\WHMCS\Service;
use AcronisCloud\Service\Database\Repository\RepositoryAwareTrait;
use AcronisCloud\Service\Dispatcher\AbstractController;
use AcronisCloud\Service\Dispatcher\RequestInterface;
use AcronisCloud\Service\Logger\LoggerAwareTrait;
use AcronisCloud\Util\Arr;
use AcronisCloud\Util\MemoizeTrait;
use Exception;
use WHMCS\Module\Server\AcronisCloud\Product\CustomFields;
use WHMCS\Module\Server\AcronisCloud\Subscription\RequestParameters\Accessor;
use WHMCS\Module\Server\AcronisCloud\Subscription\RequestParameters\Server;
use WHMCS\Module\Server\AcronisCloud\Subscription\TenantManager;

class ContactInfo extends AbstractController
{
    use MemoizeTrait,
        CloudApiTrait,
        LoggerAwareTrait,
        RepositoryAwareTrait;

    /**
     * @param RequestInterface $request
     */
    public function updateTenants($request)
    {
        $requestParameters = $request->getParameters();
        $clientId = Arr::get($requestParameters, Accessor::PARAMETER_USER_ID);

        $this->getLogger()->notice(
            'Update contact information for client {0}.',
            [$clientId]
        );

        $services = $this->getClientServices($clientId);
        foreach ($services as $service) {
            $serviceId = $service->getId();
            $serverId = $service->getServerId();
            $productId = $service->getProductId();
            /** @var CloudServer $server */
            $server = $service->cloudServer;

            $parameters = new Accessor([
                Accessor::PARAMETER_USER_ID => $clientId,
                Accessor::PARAMETER_PID => $productId,
                Accessor::PARAMETER_SERVICE_ID => $serviceId,
                Accessor::PARAMETER_CLIENTS_DETAILS => $requestParameters,
                Accessor::PARAMETER_SERVER => true,
                Server::PARAMETER_SERVER_ID => $server->getId(),
                Server::PARAMETER_SERVER_SECURE => $server->isSecure(),
                Server::PARAMETER_SERVER_HOSTNAME => $server->getHostname(),
                Server::PARAMETER_SERVER_PORT => $server->getPort(),
                Server::PARAMETER_SERVER_USERNAME => $server->getUsername(),
                Server::PARAMETER_SERVER_PASSWORD => $server->getPassword(),
            ]);

            try {
                $this->updateContactInfo($parameters);
            } catch (Exception $e) {
                $this->getLogger()->error(
                    'Unable to update contact information for client {0} for product {1} / service {2} at server {3}. Error: {4}',
                    [$clientId, $productId, $serviceId, $serverId, $e->getMessage()]
                );
                $this->getLogger()->debug($e->getTraceAsString());
            }
        }
    }

    /**
     * @param $clientId
     * @return Service[]
     */
    private function getClientServices($clientId)
    {
        return $this->getRepository()
            ->getServiceRepository()
            ->getClientServicesWithServers($clientId);
    }

    /**
     * @param Accessor $parameters
     * @throws Exception
     */
    private function updateContactInfo(Accessor $parameters)
    {
        $clientId = $parameters->getUserId();
        $productId = $parameters->getProductId();
        $serviceId = $parameters->getServiceId();

        $this->getLogger()->notice(
            'Update contact information for client {0} for product {1} / service {2} at server {3}.',
            [$clientId, $productId, $serviceId]
        );

        $customFields = $this->getCustomFields($productId, $serviceId);
        $tenantId = $customFields->getTenantId();
        if (!$tenantId) {
            $this->getLogger()->warning(
                'Unable to update contact information for the tenant of client {0}. There is no value for custom property "{1}" for product {2} / service {3}.',
                [$clientId, CustomFields::FIELD_NAME_TENANT_ID, $productId, $serviceId]
            );

            return;
        }

        $cloudApi = $this->getCloudApiForServer($parameters->getServer());
        $tenant = $cloudApi->getTenant($tenantId);

        $tenantHelper = new TenantManager($parameters, $cloudApi);
        $tenantHelper->updateTenant($tenant);

        $userId = $customFields->getUserId();
        if (!$userId) {
            $this->getLogger()->warning(
                'Unable to update contact information for the user of client {0}. There is no value for custom property "{1}" for product {2} / service {3}.',
                [$clientId, CustomFields::FIELD_NAME_USER_ID, $productId, $serviceId]
            );

            return;
        }

        $user = $cloudApi->getUser($userId);
        $tenantHelper->updateUser($user);
    }

    /**
     * @param int $productId
     * @param int $serviceId
     * @return CustomFields
     */
    private function getCustomFields($productId, $serviceId)
    {
        return $this->memoize(function () use ($productId, $serviceId) {
            return new CustomFields($productId, $serviceId);
        }, $productId . '_' . $serviceId);
    }
}