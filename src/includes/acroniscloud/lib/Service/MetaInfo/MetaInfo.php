<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\MetaInfo;

use AcronisCloud\Util\Arr;
use AcronisCloud\Util\Str;

class MetaInfo
{
    const META_APPLICATIONS = 'applications';
    const META_EDITIONS = 'editions';
    const META_NOTIFICATIONS = 'notifications';
    const META_OFFERING_ITEMS = 'offering_items';
    const META_CUSTOM_FIELDS = 'custom_fields';

    private $metaStore = [];

    /**
     * @param array $applications
     * @return $this
     */
    public function populateApplicationsMeta($applications)
    {
        $this->createMetaWrappersForItems(
            static::META_APPLICATIONS,
            $applications,
            ApplicationMeta::class,
            ApplicationMeta::PROPERTY_TYPE
        );

        return $this;
    }

    /**
     * @param array $editions
     * @return $this
     */
    public function populateEditionsMeta($editions)
    {
        $this->createMetaWrappersForItems(
            static::META_EDITIONS,
            $editions,
            EditionMeta::class,
            EditionMeta::PROPERTY_EDITION_NAME
        );

        return $this;
    }

    /**
     * @param array $notifications
     * @return $this
     */
    public function populateNotificationsMeta($notifications)
    {
        $this->createMetaWrappersForItems(
            static::META_NOTIFICATIONS,
            $notifications,
            NotificationMeta::class,
            NotificationMeta::PROPERTY_TYPE
        );

        return $this;
    }

    /**
     * @param array $offeringItems
     * @return $this
     */
    public function populateOfferingItemsMeta($offeringItems)
    {
        $this->createMetaWrappersForItems(
            static::META_OFFERING_ITEMS,
            $offeringItems,
            OfferingItemMeta::class,
            OfferingItemMeta::PROPERTY_OFFERING_ITEM_NAME
        );

        return $this;
    }

    /**
     * @param array $customFields
     * @return $this
     */
    public function populateCustomFieldsMeta($customFields)
    {
        $this->createMetaWrappersForItems(
            static::META_CUSTOM_FIELDS,
            $customFields,
            CustomFieldMeta::class,
            CustomFieldMeta::PROPERTY_NAME
        );

        return $this;
    }

    /**
     * @return ApplicationMeta[]
     */
    public function getApplicationsMeta()
    {
        return $this->metaStore[static::META_APPLICATIONS];
    }

    /**
     * @param string $applicationType
     * @return ApplicationMeta|null
     */
    public function getApplicationMeta($applicationType)
    {
        return Arr::get($this->getApplicationsMeta(), $applicationType);
    }

    /**
     * @param string $applicationType
     * @return bool
     */
    public function hasApplicationMeta($applicationType)
    {
        return Arr::has($this->getApplicationsMeta(), $applicationType);
    }

    /**
     * @return EditionMeta[]
     */
    public function getEditionsMeta()
    {
        return $this->metaStore[static::META_EDITIONS];
    }

    /**
     * @param string $editionName
     * @return EditionMeta|null
     */
    public function getEditionMeta($editionName)
    {
        return Arr::get($this->getEditionsMeta(), $editionName);
    }

    /**
     * @param string $editionName
     * @return bool
     */
    public function hasEditionMeta($editionName)
    {
        return Arr::has($this->getEditionsMeta(), $editionName);
    }

    /**
     * @return NotificationMeta[]
     */
    public function getNotificationsMeta()
    {
        return $this->metaStore[static::META_NOTIFICATIONS];
    }

    /**
     * @param string $notificationType
     * @return NotificationMeta|null
     */
    public function getNotificationMeta($notificationType)
    {
        return Arr::get($this->getNotificationsMeta(), $notificationType);
    }

    /**
     * @param string $notificationType
     * @return bool
     */
    public function hasNotificationMeta($notificationType)
    {
        return Arr::has($this->getNotificationsMeta(), $notificationType);
    }

    /**
     * @return OfferingItemMeta[]
     */
    public function getOfferingItemsMeta()
    {
        return $this->metaStore[static::META_OFFERING_ITEMS];
    }

    /**
     * @param string $offeringItemName
     * @return OfferingItemMeta|null
     */
    public function getOfferingItemMeta($offeringItemName)
    {
        return Arr::get($this->getOfferingItemsMeta(), $offeringItemName);
    }

    /**
     * @param string $offeringItemName
     * @return bool
     */
    public function hasOfferingItemMeta($offeringItemName)
    {
        return Arr::has($this->getOfferingItemsMeta(), $offeringItemName);
    }

    /**
     * @return CustomFieldMeta[]
     */
    public function getCustomFieldsMeta()
    {
        return $this->metaStore[static::META_CUSTOM_FIELDS];
    }

    /**
     * @param string $fieldName
     * @return CustomFieldMeta|null
     */
    public function getCustomFieldMeta($fieldName)
    {
        return Arr::get($this->getCustomFieldsMeta(), $fieldName);
    }

    protected function createMetaWrappersForItems($metaName, array $metaItems, $wrapperClass,  $keyProperty)
    {
        if (isset($this->metaStore[$metaName])) {
            throw new \BadMethodCallException(Str::format('Cannot re-initialize %s', $metaName));
        }

        $this->metaStore[$metaName] = Arr::map(
            $metaItems,
            $keyProperty,
            function ($item, $index) use ($wrapperClass) {
                $item[AbstractMeta::PROPERTY_SORT_PRIORITY] = $index + 1;

                return new $wrapperClass($item);
            }
        );
    }
}