<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace WHMCS\Module\Server\AcronisCloud\Controller;

use AcronisCloud\Service\Dispatcher\AbstractController;
use AcronisCloud\Service\Dispatcher\ActionInterface;
use AcronisCloud\Service\Dispatcher\RequestInterface;
use AcronisCloud\Service\Dispatcher\Response\JsonErrorResponse;
use AcronisCloud\Service\Dispatcher\Response\JsonResponse;
use AcronisCloud\Service\MetaInfo\ApplicationMeta;
use AcronisCloud\Service\MetaInfo\EditionMeta;
use AcronisCloud\Service\MetaInfo\MetaInfoAwareTrait;
use AcronisCloud\Service\MetaInfo\NotificationMeta;
use AcronisCloud\Service\MetaInfo\OfferingItemMeta;
use AcronisCloud\Util\Arr;
use Exception;

class L10n extends AbstractController
{
    use MetaInfoAwareTrait;

    /**
     * @return JsonResponse
     */
    public function getResponseStrategy()
    {
        return new JsonResponse();
    }

    /**
     * @inheritdoc
     */
    public function handleException(
        Exception $e,
        ActionInterface $action,
        RequestInterface $request
    )
    {
        return new JsonErrorResponse($e);
    }

    /**
     * @param RequestInterface $request
     * @return array
     */
    public function getL10n(RequestInterface $request)
    {
        return [
            'applications' => $this->getApplications(),
            'editions' => $this->getEditions(),
            'offering_items' => $this->getOfferingItems(),
            'notifications' => $this->getNotifications(),
        ];
    }

    private function getApplications()
    {
        $metaInfo = $this->getMetaInfo();
        $applicationsMeta = $metaInfo->getApplicationsMeta();
        return Arr::map(
            $applicationsMeta,
            function ($applicationMeta) {
                /** @var ApplicationMeta $applicationMeta */
                return $applicationMeta->getApplicationType();
            },
            function ($applicationMeta) {
                /** @var ApplicationMeta $applicationMeta */
                return [
                    ApplicationMeta::PROPERTY_TITLE => $applicationMeta->getTitle(),
                    ApplicationMeta::PROPERTY_DESCRIPTION => $applicationMeta->getDescription(),
                ];
            }
        );
    }

    private function getEditions()
    {
        $metaInfo = $this->getMetaInfo();
        $editionsMeta = $metaInfo->getEditionsMeta();
        return Arr::map(
            $editionsMeta,
            function ($editionMeta) {
                /** @var EditionMeta $editionMeta */
                return $editionMeta->getEditionName();
            },
            function ($editionMeta) {
                /** @var EditionMeta $editionMeta */
                return [
                    EditionMeta::PROPERTY_TITLE => $editionMeta->getTitle(),
                    EditionMeta::PROPERTY_DESCRIPTION => $editionMeta->getDescription(),
                ];
            }
        );
    }

    private function getOfferingItems()
    {
        $metaInfo = $this->getMetaInfo();
        $offeringItemsMeta = $metaInfo->getOfferingItemsMeta();
        return Arr::map(
            $offeringItemsMeta,
            function ($offeringItemMeta) {
                /** @var OfferingItemMeta $offeringItemMeta */
                return $offeringItemMeta->getOfferingItemName();
            },
            function ($offeringItemMeta) {
                /** @var OfferingItemMeta $offeringItemMeta */
                return $offeringItemMeta->getOfferingItemFriendlyName();
            }
        );
    }

    private function getNotifications()
    {
        $metaInfo = $this->getMetaInfo();
        $notificationsMeta = $metaInfo->getNotificationsMeta();
        return Arr::map(
            $notificationsMeta,
            function ($notificationMeta) {
                /** @var NotificationMeta $notificationMeta */
                return $notificationMeta->getType();
            },
            function ($notificationMeta) {
                /** @var NotificationMeta $notificationMeta */
                return $notificationMeta->getTitle();
            }
        );
    }
}