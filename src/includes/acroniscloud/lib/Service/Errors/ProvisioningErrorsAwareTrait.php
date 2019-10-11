<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
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