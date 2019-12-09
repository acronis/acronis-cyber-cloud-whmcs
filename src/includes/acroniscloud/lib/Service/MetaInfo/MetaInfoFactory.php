<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\MetaInfo;

use AcronisCloud\Service\FactoryInterface;

class MetaInfoFactory implements FactoryInterface
{
    const NAME = 'meta_info';

    /**
     * @return MetaInfo
     */
    public function createInstance()
    {
        $applications = require ACRONIS_CLOUD_INCLUDES_DIR . '/meta/applications.php';
        $editions = require ACRONIS_CLOUD_INCLUDES_DIR . '/meta/editions.php';
        $notifications = require ACRONIS_CLOUD_INCLUDES_DIR . '/meta/notifications.php';
        $offeringItems = require ACRONIS_CLOUD_INCLUDES_DIR . '/meta/offering_items.php';
        $customFields = require ACRONIS_CLOUD_INCLUDES_DIR . '/meta/custom_fields.php';

        return (new MetaInfo())
            ->populateApplicationsMeta($applications)
            ->populateEditionsMeta($editions)
            ->populateNotificationsMeta($notifications)
            ->populateOfferingItemsMeta($offeringItems)
            ->populateCustomFieldsMeta($customFields);
    }
}