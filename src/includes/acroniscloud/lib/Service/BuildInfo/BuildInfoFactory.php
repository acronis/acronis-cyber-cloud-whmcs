<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
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