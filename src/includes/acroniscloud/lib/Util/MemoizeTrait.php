<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Util;

/**
 * Trait MemoizeTrait
 * Use MemoizeTrait in cases you want to fetch some object identified by $objectKey only one time and then return it
 * from memory
 * $this->memoize(function() use ($anyData) { return $this->veryExpensiveFetchRequest($objectKey); }, objectKey,
 * [force]); If you want to be able to fallback to fetching object every time, pass last parameter as true
 *
 * @package WHMCS\Module\Server\AcronisCloud\Utils
 */
trait MemoizeTrait
{
    /** @var array */
    private $memoizeStore = [];

    /**
     * @param callable $method
     * @param string $objectId
     * @param bool $force
     * @return mixed
     */
    protected function memoize(callable $method, $objectId = '', $force = false)
    {
        $callerName = Func::getCallerName();
        $objectId = $this->buildMemoizeObjectKey($callerName, $objectId);

        if (!$force && $this->hasMemoizeStoredObject($objectId)) {
            return $this->getMemoizeStoredObject($objectId);
        }

        $object = $method();
        $this->storeMemoizeObject($objectId, $object);

        return $object;
    }

    /**
     * @param string $objectId
     * @param mixed $value
     */
    private function storeMemoizeObject($objectId, $value)
    {
        $this->memoizeStore[$objectId] = $value;
    }

    /**
     * @param string $objectId
     * @return mixed
     */
    private function getMemoizeStoredObject($objectId)
    {
        return $this->memoizeStore[$objectId];
    }

    /**
     * @param string $objectId
     * @return bool
     */
    private function hasMemoizeStoredObject($objectId)
    {
        return array_key_exists($objectId, $this->memoizeStore);
    }

    /**
     * @param string $method
     * @param string $objectId
     * @return string
     */
    private function buildMemoizeObjectKey($method, $objectId)
    {
        return implode('|', [$method, $objectId]);
    }
}