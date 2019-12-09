<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace WHMCS\Module\Server\AcronisCloud\Controller;

use AcronisCloud\Service\Dispatcher\RequestException;
use AcronisCloud\Service\Dispatcher\RequestInterface;
use AcronisCloud\Service\Dispatcher\Response\StatusCodeInterface;
use AcronisCloud\Util\MemoizeTrait;

class ClientRequest
{
    use MemoizeTrait;

    const PARAM_SERVICE_ID = 'id';
    const PARAM_VERSION = 'version';
    const PARAM_NOTIFICATIONS = 'notifications';

    /** @var RequestInterface*/
    private $request;

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * @return int
     * @throws RequestException
     */
    public function getServiceId()
    {
        $serviceId = (int)$this->request
            ->getQueryParameter(static::PARAM_SERVICE_ID);

        if (!$serviceId) {
            throw new RequestException(
                'Service ID must be present.',
                [],
                StatusCodeInterface::HTTP_BAD_REQUEST
            );
        }

        return $serviceId;
    }

    /**
     * @return int
     * @throws RequestException
     */
    public function getVersion()
    {
        $version = (int)$this->request
            ->getQueryParameter(static::PARAM_VERSION);

        if (!$version) {
            throw new RequestException(
                'Version number must be present.',
                [],
                StatusCodeInterface::HTTP_BAD_REQUEST
            );
        }

        return $version;
    }

    /**
     * @return NotificationsManager
     */
    public function getNotifications()
    {
        return $this->memoize(function () {
            $notifications = $this->request->getBodyParameter(static::PARAM_NOTIFICATIONS);
            if (!$notifications) {
                throw new RequestException(
                    'POST data parameter "notifications" must be present.',
                    [
                        'request_post_data' => $this->request->getBodyParameters(),
                    ],
                    StatusCodeInterface::HTTP_BAD_REQUEST
                );
            }

            return new NotificationsManager($notifications);
        });
    }
}