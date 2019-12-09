<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\BuildInfo;

use AcronisCloud\Service\FactoryInterface;

class BuildInfoFactory implements FactoryInterface
{
    const NAME = 'build_info';

    /**
     * @return BuildInfoParser
     */
    public function createInstance()
    {
        $versionFile = ACRONIS_CLOUD_INCLUDES_DIR . '/Vers.ion';

        return new BuildInfoParser($versionFile);
    }
}