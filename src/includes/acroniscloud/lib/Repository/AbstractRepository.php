<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Repository;

use AcronisCloud\Service\Database\Repository\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use BadMethodCallException;
use Exception;

abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * @return Collection
     */
    public function all()
    {
        throw new BadMethodCallException('Not implemented.');
    }

    /**
     * @param $id
     * @return Model
     * @throws Exception
     */
    public function find($id)
    {
        throw new BadMethodCallException('Not implemented.');
    }

    /**
     * @param array $data
     * @return integer
     * @throws Exception
     */
    public function create(array $data)
    {
        throw new BadMethodCallException('Not implemented.');
    }

    /**
     * @param array $data
     * @param integer $id
     * @return void
     * @throws Exception
     */
    public function update(array $data, $id)
    {
        throw new BadMethodCallException('Not implemented.');
    }

    /**
     * @param integer $id
     * @return void
     * @throws Exception
     */
    public function delete($id)
    {
        throw new BadMethodCallException('Not implemented.');
    }
}