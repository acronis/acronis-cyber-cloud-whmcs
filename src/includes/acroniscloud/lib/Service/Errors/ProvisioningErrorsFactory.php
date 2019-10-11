<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Errors;

use AcronisCloud\Service\FactoryInterface;

class ProvisioningErrorsFactory implements FactoryInterface
{
    const NAME = 'provisioning_errors';

    public function createInstance()
    {
        return new ProvisioningErrorsManager();
    }
}