<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Logger\Processor;

class LimitsProcessor
{
    /**
     * @var int
     */
    private $maxLength;

    public function __construct($maxLength = 0)
    {
        $this->maxLength = $maxLength;
    }

    public function __invoke(array $record)
    {
        $message = $record['message'];
        if ($this->maxLength && strlen($message) > $this->maxLength) {
            $record['message'] = substr($message, 0, $this->maxLength) . '...';
        }

        return $record;
    }
}