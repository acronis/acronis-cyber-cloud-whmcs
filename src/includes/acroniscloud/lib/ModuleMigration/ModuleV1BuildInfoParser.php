<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\ModuleMigration;

use AcronisCloud\Service\BuildInfo\BuildInfoParser;

class ModuleV1BuildInfoParser extends BuildInfoParser
{
    const MAJOR = 'major_version';
    const MINOR = 'minor_version';
    const BUILD = 'build_number';
}
