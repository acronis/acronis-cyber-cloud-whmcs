<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Language;

use AcronisCloud\Service\FactoryInterface;

class IsoCodeFactory implements FactoryInterface
{
    const NAME = 'language_iso_code';

    /**
     * @return IsoCode
     */
    public function createInstance()
    {
        $languages = require(ACRONIS_CLOUD_INCLUDES_DIR . '/languages.php');

        return new IsoCode($languages);
    }
}