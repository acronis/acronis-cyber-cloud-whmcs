<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Config\Settings;

use AcronisCloud\Util\Arr;

class ProductSettings extends AbstractSettings
{
    const PROPERTY_ASK_USER_CREDENTIALS = 'ask_user_credentials';
    const PROPERTY_OVERAGE_RATIO = 'overage_ratio';

    /**
     * @return bool
     */
    public function getAskUserCredentials()
    {
        return (bool)Arr::get($this->settings, static::PROPERTY_ASK_USER_CREDENTIALS, false);
    }

    /**
     * @return float
     */
    public function getOverageRatio()
    {
        $overage = floatval(Arr::get($this->settings, static::PROPERTY_OVERAGE_RATIO, 1));

        return max($overage, 1.0);
    }
}