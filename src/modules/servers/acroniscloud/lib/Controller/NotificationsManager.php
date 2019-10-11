<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace WHMCS\Module\Server\AcronisCloud\Controller;

use AcronisCloud\Service\MetaInfo\MetaInfoAwareTrait;
use AcronisCloud\Service\MetaInfo\NotificationMeta;
use AcronisCloud\Util\Str;

class NotificationsManager
{
    use MetaInfoAwareTrait;

    // grouping redundancy in post data
    const PROPERTY_NOTIFICATIONS = 'notifications';

    // property in an update request that stores notifications
    const PROPERTY_ITEMS = 'items';

    /** @var Notification[][] */
    private $groupedNotifications = [];

    public function __construct($notifications)
    {
        $this->buildGrouping($notifications);
    }

    /**
     * Used in web responses
     *
     * @return array
     */
    public function toClientAreaFormat()
    {
        $notificationsArray = [];

        foreach ($this->groupedNotifications as $appType => $notifications) {
            $notificationsArray[] = [
                'application_type' => $appType,
                'items' => array_map(
                    function($notification) {
                        return [
                            'type' => $notification->getType(),
                            'status' => $notification->getStatus(),
                        ];
                    },
                    $notifications
                ),
            ];
        }

        return $notificationsArray;
    }

    /**
     * Used in setting cloud state of user notifications
     *
     * @return array
     */
    public function toCloudFormat()
    {
        $notifications = [];
        foreach ($this->groupedNotifications as $appNotifications) {
            foreach ($appNotifications as $notification) {
                if ($notification->getStatus() === Notification::STATUS_ACTIVE) {
                    $notifications[] = $notification->getType();
                }
            }
        }

        return $notifications;
    }

    /**
     * @param $notifications
     */
    private function buildGrouping($notifications)
    {
        if ($this->isFlatArray($notifications)) {
            $this->buildFromCloudFormat($notifications);
        } else {
            $this->buildFromClientAreaFormat($notifications);
        }
    }

    /**
     * $notifications is an array of active notifications
     *
     * @param $notifications
     */
    private function buildFromCloudFormat($notifications)
    {
        $notificationsMeta = $this->getMetaInfo()->getNotificationsMeta();
        foreach ($notificationsMeta as $notificationMeta) {
            $isActive = in_array($notificationMeta->getType(), $notifications);
            $appType = $notificationMeta->getApplicationType();
            $this->groupedNotifications[$appType][] = $this->buildNotificationFromMeta($notificationMeta, $isActive);
        }
    }

    /**
     * $notifications is a json object with metadata (from update_details api request)
     *
     * @param $notificationsGroups
     */
    private function buildFromClientAreaFormat($notificationsGroups)
    {
        foreach ($notificationsGroups as $appNotifications) {
            $appType = $appNotifications[NotificationMeta::PROPERTY_APPLICATION_TYPE];
            $this->validateAppType($appType);
            $notifications = $appNotifications[static::PROPERTY_ITEMS];
            foreach ($notifications as $notification) {
                $type = $notification[NotificationMeta::PROPERTY_TYPE];
                $status = $notification[Notification::PROPERTY_STATUS];
                $this->validateNotificationType($type, $appType);
                $notificationObj = new Notification($type, $appType);
                $notificationObj->setStatus($status);
                $this->groupedNotifications[$appType][] = $notificationObj;
            }
        }
    }

    /**
     * @param $notificationMeta
     * @param $isActive
     * @return Notification
     */
    private function buildNotificationFromMeta($notificationMeta, $isActive)
    {
        $notificationType = $notificationMeta->getType();
        $appType = $notificationMeta->getApplicationType();
        $status = $isActive ? Notification::STATUS_ACTIVE : Notification::STATUS_INACTIVE;

        return new Notification($notificationType, $appType, $status);
    }

    /**
     * @param $notifications
     * @return bool
     */
    private function isFlatArray($notifications)
    {
        return count($notifications) === count($notifications, COUNT_RECURSIVE);
    }

    /**
     * @return array
     */
    private function getAllowedApplicationTypes()
    {
        $notificationsMeta = $this->getMetaInfo()->getNotificationsMeta();
        $notificationsAppType = [];
        foreach ($notificationsMeta as $oiMeta) {
            $notificationsAppType[$oiMeta->getApplicationType()] = true;
        }

        return array_keys($notificationsAppType);
    }

    private function validateAppType($appType)
    {
        if (!in_array($appType, $this->getAllowedApplicationTypes())) {
            throw new \InvalidArgumentException(Str::format(
                'Notifications have invalid application type "%s"',
                $appType
            ));
        }
    }

    private function validateNotificationType($type, $appType)
    {
        $metaInfo = $this->getMetaInfo();
        if (!$metaInfo->hasNotificationMeta($type)) {
            throw new \InvalidArgumentException(Str::format(
                'Notification has invalid type "%s"',
                $type
            ));
        }
        if ($metaInfo->getNotificationMeta($type)->getApplicationType() !== $appType) {
            throw new \InvalidArgumentException(Str::format(
                'Notification "%s" does not belong to application "%s"',
                $type, $appType
            ));
        }
    }
}