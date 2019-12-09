<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
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