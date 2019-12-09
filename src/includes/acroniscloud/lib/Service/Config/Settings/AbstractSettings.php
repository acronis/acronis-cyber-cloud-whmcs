<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */


namespace AcronisCloud\Service\Config\Settings;

abstract class AbstractSettings
{
    /** @var array */
    protected $settings;

    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }
}