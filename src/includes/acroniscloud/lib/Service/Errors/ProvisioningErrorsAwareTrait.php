<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Errors;

use AcronisCloud\Service\Locator;

trait ProvisioningErrorsAwareTrait
{
    /**
     * @return ProvisioningErrorsManager
     */
    protected function getProvisioningErrorsManager()
    {
        return Locator::getInstance()->get(ProvisioningErrorsFactory::NAME);
    }
}