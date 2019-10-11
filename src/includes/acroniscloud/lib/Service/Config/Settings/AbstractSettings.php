<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
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