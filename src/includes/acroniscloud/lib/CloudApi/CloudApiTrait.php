<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\CloudApi;

use AcronisCloud\Model\WHMCS\Server;
use AcronisCloud\Util\Str;
use BadMethodCallException;

trait CloudApiTrait
{
    /** @var ApiInterface[] */
    private $cloudApiInstances;

    /**
     * @return CloudServerInterface
     * @throws BadMethodCallException
     */
    protected function getCloudServer()
    {
        throw new BadMethodCallException(Str::format(
            'Please override method "%s" before to call "getCloudApi".',
            __METHOD__
        ));
    }

    /**
     * @return ApiInterface
     * @throws CloudServerException
     */
    protected function getCloudApi()
    {
        $server = $this->getCloudServer();

        return $this->getCloudApiForServer($server);
    }

    /**
     * @param CloudServerInterface $server
     * @return ApiInterface
     * @throws CloudServerException
     */
    protected function getCloudApiForServer(CloudServerInterface $server)
    {
        $this->validateServer($server);
        $serverId = $server->getId();
        if (!isset($this->cloudApiInstances[$serverId])) {
            $this->cloudApiInstances[$serverId] = $this->createCloudApiInstance($server);
        }

        return $this->cloudApiInstances[$serverId];
    }

    /**
     * @param ApiInterface $cloudApi
     * @param Server|CloudServerInterface $server
     * @throws \Exception
     */
    private function checkPlatformVersion($cloudApi, $server)
    {
        $serverVersion = $cloudApi->getVersions();
        $platformVersionParts = explode('.', $serverVersion->getApplication()->getVersion());
        $platformMajorVersion = reset($platformVersionParts);
        if ($platformMajorVersion < CloudServerInterface::MINIMAL_SUPPORTED_CLOUD_MAJOR_VERSION) {
            throw new \Exception(Str::format(
                'The platform version of the server "%s" is earlier than the minimum supported version "%s.0".',
                $server->getName(),
                CloudServerInterface::MINIMAL_SUPPORTED_CLOUD_MAJOR_VERSION
            ));
        }
    }

    /**
     * @param CloudServerInterface $server
     * @return ApiInterface
     * @throws CloudServerException
     * @throws \Exception
     */
    private function createCloudApiInstance($server)
    {
        $cloudApi = new Api(
            $this->buildCloudApiUrl($server),
            $server->getUsername(),
            $server->getPassword(),
            $server->getAccessHash()
        );
        $this->checkPlatformVersion($cloudApi, $server);

        return $cloudApi;
    }

    /**
     * @param CloudServerInterface $server
     * @throws CloudServerException
     */
    private function validateServer($server)
    {
        if (!$server) {
            throw new CloudServerException(
                Str::format('Server is not specified.'),
                CloudServerException::CODE_EMPTY_SERVER
            );
        }

        $serverId = $server->getId();
        if (!$server->getHostname()) {
            throw new CloudServerException(
                Str::format('Hostname is not specified for server {0}.', [
                    '{0}' => $serverId,
                ]),
                CloudServerException::CODE_EMPTY_HOSTNAME,
                $server
            );
        }

        if (!$server->getUsername()) {
            throw new CloudServerException(
                Str::format('Username is not specified for server {0}.', [
                    '{0}' => $serverId,
                ]),
                CloudServerException::CODE_EMPTY_USERNAME,
                $server
            );
        }

        if (!$server->getPassword()) {
            throw new CloudServerException(
                Str::format('Password is not specified for server {0}.', [
                    '{0}' => $serverId,
                ]),
                CloudServerException::CODE_EMPTY_PASSWORD,
                $server
            );
        }
    }

    /**
     * @param CloudServerInterface $server
     * @return string
     * @throws CloudServerException
     */
    private function buildCloudApiUrl($server)
    {
        $schema = $server->isSecure()
            ? CloudServerInterface::SCHEMA_HTTPS
            : CloudServerInterface::SCHEMA_HTTP;

        // if you provide just the host (ex. "www.google.com"), parse_url assumes it's a path
        $hostname = parse_url($server->getHostname(), PHP_URL_PATH);

        if (!$hostname || $hostname !== $server->getHostname()) {
            throw new CloudServerException(
                Str::format('Hostname is missing or has invalid format for server {0}.', [
                    '{0}' => $server->getId(),
                ]),
                CloudServerException::CODE_EMPTY_HOSTNAME,
                $server
            );
        }
        $port = $server->getPort();
        if ($port) {
            $port = ':' . $port;
        }

        return Str::format(
            '%s://%s%s',
            $schema, $hostname, $port
        );
    }
}