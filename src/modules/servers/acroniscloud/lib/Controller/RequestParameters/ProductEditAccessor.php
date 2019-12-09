<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace WHMCS\Module\Server\AcronisCloud\Controller\RequestParameters;

use AcronisCloud\Util\Arr;
use AcronisCloud\Util\MemoizeTrait;
use WHMCS\Module\Server\AcronisCloud\Subscription\RequestParameters\ProductOptions;

class ProductEditAccessor
{
    use MemoizeTrait;

    const PARAMETER_PID = 'pid';
    const PARAMETER_SERVER_TYPE = 'servertype';

    /** @var array */
    private $parameters;

    /**
     * Accessor constructor.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return int|null
     */
    public function getProductId()
    {
        return Arr::get($this->parameters, static::PARAMETER_PID);
    }

    /**
     * @return int|null
     */
    public function getServerType()
    {
        return Arr::get($this->parameters, static::PARAMETER_SERVER_TYPE);
    }

    /**
     * @return ProductOptions
     */
    public function getProductOptions()
    {
        return $this->memoize(function () {
            return new ProductOptions($this->parameters);
        });
    }
}