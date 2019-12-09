<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Errors;

class ErrorNotification
{
    const SERIALIZED_PROPERTY_TITLE = 'title';
    const SERIALIZED_PROPERTY_MESSAGE = 'message';

    /** @var string */
    private $title;

    /** @var string */
    private $message;

    public function __construct($title = '', $message = '')
    {
        $this->title = $title;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize([
            static::SERIALIZED_PROPERTY_TITLE => $this->getTitle(),
            static::SERIALIZED_PROPERTY_MESSAGE => $this->getMessage(),
        ]);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        $this->title = $data[static::SERIALIZED_PROPERTY_TITLE];
        $this->message = $data[static::SERIALIZED_PROPERTY_MESSAGE];
    }
}