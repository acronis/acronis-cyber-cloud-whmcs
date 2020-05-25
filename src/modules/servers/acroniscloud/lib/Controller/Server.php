<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace WHMCS\Module\Server\AcronisCloud\Controller;

use Acronis\Cloud\Client\HttpException;
use Acronis\Cloud\Client\Model\Clients\ClientPost;
use Acronis\Cloud\Client\Model\Tenants\TenantPut;
use AcronisCloud\CloudApi\AuthorizedApi;
use AcronisCloud\CloudApi\CloudApiTrait;
use AcronisCloud\Localization\GetTextTrait;
use AcronisCloud\Model\WHMCS\Server as ServerModel;
use AcronisCloud\Service\BuildInfo\BuildInfoAwareTrait;
use AcronisCloud\Service\Database\Repository\RepositoryAwareTrait;
use AcronisCloud\Service\Dispatcher\AbstractController;
use AcronisCloud\Service\Dispatcher\RequestInterface;
use AcronisCloud\Service\Logger\LoggerAwareTrait;
use AcronisCloud\Util\Arr;
use AcronisCloud\Util\Str;
use AcronisCloud\Util\WHMCS\Lang;
use AcronisCloud\View\ViewLoader;
use Exception;
use WHMCS\Module\Server\AcronisCloud\Subscription\RequestParameters\Server as RequestParametersServer;

class Server extends AbstractController
{
    use BuildInfoAwareTrait,
        CloudApiTrait,
        GetTextTrait,
        LoggerAwareTrait,
        RepositoryAwareTrait;

    const WHMCS_VERSION = 'whmcs-version';
    const WHMCS_SERVER_ID = 'whmcs-server-id';
    const WHMCS_MODULE_VERSION = 'whmcs-module-version';

    /** @var ServerModel */
    private $cloudServer;

    /**
     * @param RequestInterface $request
     */
    public function updateServerInfo($request)
    {
        $parameters = $request->getParameters();
        $serverId = Arr::get($parameters, RequestParametersServer::PARAMETER_SERVER_ID);
        if (!$this->initServer($serverId) || $this->getCloudServer()->getType() !== ACRONIS_CLOUD_SERVICE_NAME) {
            return;
        }

        $whmcsVersion = $this->getRepository()
            ->getConfigurationRepository()
            ->getVersion();

        $moduleVersion = $this->getBuildInfo()
            ->getPackageVersion();

        $internalTagParts = [
            static::WHMCS_VERSION => $whmcsVersion,
            static::WHMCS_SERVER_ID => $serverId,
            static::WHMCS_MODULE_VERSION => $moduleVersion,
        ];

        try {
            $this->getLogger()->notice(
                'Update internal tag for server {0}.',
                [$serverId]
            );

            $cloudApi = $this->getCloudApi();
            $cloudApi->resetAccessCache();
            if ($cloudApi->getGrantType() !== AuthorizedApi::GRANT_TYPE_CLIENT_CREDENTIALS) {
                $this->createClientCredentials();
            }

            $tenant = $cloudApi->getTenant($cloudApi->getRootTenantId());

            $internalTag = $tenant->hasInternalTag()
                ? $tenant->getInternalTag()
                : null;

            if ($internalTag) {
                $internalTagParts += Arr::decode($internalTag);
            }

            $internalTag = Arr::encode($internalTagParts);

            $tenantPut = new TenantPut();
            $tenantPut->setVersion($tenant->getVersion());
            $tenantPut->setInternalTag($internalTag);

            $cloudApi->updateTenant($tenant->getId(), $tenantPut);
        } catch (Exception $e) {
            // Always suppress exceptions for this operation
            $this->getLogger()->warning(
                'Unable to set internal tag "{0}" for the root tenant at server {1}. Error: {2}',
                [Arr::encode($internalTagParts), $serverId, $e->getMessage()]
            );
        }
    }

    /**
     * @param RequestInterface $request
     */
    public function deleteInternalTag($request)
    {
        $parameters = $request->getParameters();
        $serverId = Arr::get($parameters, RequestParametersServer::PARAMETER_SERVER_ID);
        if (!$this->initServer($serverId) || $this->getCloudServer()->getType() !== ACRONIS_CLOUD_SERVICE_NAME) {
            return;
        }

        try {
            $this->getLogger()->notice(
                'Clear internal tag for server {0}.',
                [$serverId]
            );

            $cloudApi = $this->getCloudApi();
            $tenant = $cloudApi->getTenant($cloudApi->getRootTenantId());

            $internalTag = $tenant->hasInternalTag()
                ? $tenant->getInternalTag()
                : null;

            if (!$internalTag) {
                $this->getLogger()->notice(
                    'Internal tag is already empty for server {0}.',
                    [$serverId]
                );

                return;
            }

            $internalTagParts = Arr::decode($internalTag);

            unset($internalTagParts[static::WHMCS_VERSION]);
            unset($internalTagParts[static::WHMCS_SERVER_ID]);
            unset($internalTagParts[static::WHMCS_MODULE_VERSION]);

            $internalTag = Arr::encode($internalTagParts);

            $tenantPut = new TenantPut();
            $tenantPut->setVersion($tenant->getVersion());
            $tenantPut->setInternalTag($internalTag);

            $cloudApi->updateTenant($tenant->getId(), $tenantPut);
        } catch (Exception $e) {
            // Always suppress exceptions for this operation
            $this->getLogger()->warning(
                'Unable to clear internal tag for the root tenant at server {0}. Error: {1}',
                [$serverId, $e->getMessage()]
            );
        }
    }

    /**
     * @param RequestInterface $request
     * @return string
     * @throws \SmartyException
     */
    public function adminOutput(RequestInterface $request)
    {
        $action = $request->getQueryParameter('action');
        $isServerEdit = strpos($request->getRequestUrl(), 'configservers.php')
            && ($action === 'manage' || ($action === 'save' && !$request->getQueryParameter('id')));

        if ($isServerEdit) {
            $viewLoader = new ViewLoader(
                ACRONIS_CLOUD_SERVER_MODULE_DIR . '/views',
                ACRONIS_CLOUD_SERVER_MODULE_DIR . '/assets'
            );

            $labels = [
                'url_hostname' => $this->gettext('URL, Hostname or IP'),
                'username' => $this->gettext('Username'),
                'password' => $this->gettext('Password'),
                'client_id' => $this->gettext('Client ID'),
                'client_secret' => $this->gettext('Client Secret'),
                'client_id_method' => $this->gettext('Client ID (recommended)'),
                'authentication' => $this->gettext('Authentication method'),
                'hint' => $this->gettext('Username and password will be replaced with Client ID and Client Secret after you click "{0}"',
                    [Lang::trans('global.savechanges')]
                ),
            ];

            return $viewLoader->assign('acronisService', ACRONIS_CLOUD_SERVICE_NAME)
                ->assign('labels', $labels)
                ->fetch('server_custom_edit.tpl');
        } else {
            // in many cases, we want to show custom errors from actions, which the CustomHeaderOutput displays
            // note: that's not needed on the server edit page
            return (new CustomHeaderOutput())->adminOutput($request);
        }
    }

    /**
     * @param RequestInterface $request
     * @return array
     */
    public function testConnection(RequestInterface $request)
    {
        $success = true;
        $error = '';
        try {
            $serverId = $request->getBodyParameter(RequestParametersServer::PARAMETER_SERVER_ID);

            if (empty($serverId)) {
                $server = new RequestParametersServer([
                    RequestParametersServer::PARAMETER_SERVER_ID => -1,
                    RequestParametersServer::PARAMETER_SERVER_HOSTNAME => $request->getBodyParameter('hostname'),
                    RequestParametersServer::PARAMETER_SERVER_USERNAME => $request->getBodyParameter('username'),
                    RequestParametersServer::PARAMETER_SERVER_PASSWORD => $request->getBodyParameter('password'),
                    RequestParametersServer::PARAMETER_SERVER_ACCESSHASH => $request->getBodyParameter('accesshash'),
                ]);
                $this->setCloudServer($server);
            } elseif (!$this->initServer($serverId)) {
                throw new \Exception(Str::format(
                    'Could not find server with id "%s".',
                    $serverId
                ));
            }
            if (!$this->getCloudServer()->isSecure()) {
                throw new \Exception('Cannot connect over insecure channel. Please, enable the SSL Mode for Connections bellow.');
            }
            $this->getCloudApi()->getRootTenantId();
        } catch (Exception $e) {
            $this->getLogger()->error(
                'Test server connection error: {0}, {1}',
                [$e->getMessage(), $e->getTraceAsString()]
            );

            $success = false;
            if ($e instanceof HttpException) {
                $response = $e->getResponseBody();
                $error = isset($response->error_description)
                    ? $response->error_description
                    : $response->error->message;
            } else {
                $error = $e->getMessage();
            }
        }

        return [
            'success' => $success,
            'error' => $error,
        ];
    }

    /**
     *
     */
    protected function createClientCredentials()
    {
        try {
            $cloudApi = $this->getCloudApi();
            $tenantId = $cloudApi->getRootTenantId();
            $clientPost = $this->createClientPost($tenantId);
            $client = $cloudApi->createClient($clientPost);
            $cloudApi->setClientCredentials($client->getClientId(), $client->getClientSecret());
            $cloudApiUrl = parse_url($cloudApi->getUrl(), PHP_URL_HOST);
            $this->getCloudServer()
                ->setUsername($client->getClientId())
                ->setPassword($client->getClientSecret())
                ->setAccessHash(AuthorizedApi::GRANT_TYPE_CLIENT_CREDENTIALS)
                ->setHostname($cloudApiUrl)
                ->save();
        } catch (Exception $e) {
            // only log error, we keep the username/password credentials in DB
            $this->getLogger()->error(
                'Client creation failed. Error: {0}',
                [$e->getMessage()]
            );
            $this->getLogger()->debug($e->getTraceAsString());
        }
    }

    /**
     * @param $serverId
     * @return bool
     */
    protected function initServer($serverId)
    {
        if (!$serverId) {
            $this->getLogger()->warning(
                'Parameters don\'t contains property "{0}".',
                [RequestParametersServer::PARAMETER_SERVER_ID]
            );

            return false;
        }

        $server = $this->getRepository()
            ->getAcronisServerRepository()
            ->find($serverId);

        if (!$server) {
            $this->getLogger()->warning(
                'Unknown server with ID {0}.',
                [$serverId]
            );

            return false;
        }

        $this->setCloudServer($server);

        return true;
    }

    /**
     * @param ServerModel $server
     */
    protected function setCloudServer($server)
    {
        $this->cloudServer = $server;
    }

    /**
     * @return ServerModel
     */
    protected function getCloudServer()
    {
        return $this->cloudServer;
    }

    /**
     * @param $tenantId
     * @return ClientPost
     */
    protected function createClientPost($tenantId)
    {
        $client = new ClientPost();
        $client->setType('agent');
        $client->setTenantId($tenantId);
        $client->setTokenEndpointAuthMethod('client_secret_basic');

        return $client;
    }
}