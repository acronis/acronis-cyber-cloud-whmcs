<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\ModuleMigration;

use AcronisCloud\Service\BuildInfo\BuildInfoParser;

class ModuleV1BuildInfoParser extends BuildInfoParser
{
    const MAJOR = 'major_version';
    const MINOR = 'minor_version';
    const BUILD = 'build_number';
}
