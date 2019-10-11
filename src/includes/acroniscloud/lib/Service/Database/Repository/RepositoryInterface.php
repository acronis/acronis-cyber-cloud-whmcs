<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Database\Repository;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    /**
     * @return Collection
     */
    public function all();

    /**
     * @param $id
     * @return Model
     */
    public function find($id);

    /**
     * @param array $data
     * @return integer
     * @throws Exception
     */
    public function create(array $data);

    /**
     * @param array $data
     * @param integer $id
     * @return void
     * @throws Exception
     */
    public function update(array $data, $id);

    /**
     * @param integer $id
     * @return void
     * @throws Exception
     */
    public function delete($id);
}