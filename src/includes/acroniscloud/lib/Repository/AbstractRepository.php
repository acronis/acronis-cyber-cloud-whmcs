<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Repository;

use AcronisCloud\Model\AbstractModel;
use AcronisCloud\Service\Database\Repository\RepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use BadMethodCallException;
use Exception;

abstract class AbstractRepository implements RepositoryInterface
{
    /**
     * @var int
     */
    private static $lastInsertId = null;

    /**
     * Required due to a bug where the saving of log entries in the WHMCS DB causes the inserts to have the log's insert id (https://pmc.acronis.com/browse/WHMCS-899)
     *
     * What happens:
     *
     *   1. New model (ex. template) sends an insert query when we use ->save().
     *   2. The model's insert query gets logged, calling \AcronisCloud\Service\Logger\Whmcs\ModuleLogHandler::write()
     *   3. the write() function calls logModuleCall (internal to WHMCS function) that logs into the WHMCS db the log entry, using the same db connection
     *   4. The template model insert is finished and tries to fetch the last insert's id, which is the id of the log entry
     *   5. The template model (in memory) is created, having for id the value of the log entry's id
     *
     * In the db, the template has correct id, but in memory it has invalid id which causes inserts/updates on relations to fail
     *
     * @param int $id
     */
    public static function setLastInsertId($id)
    {
        static::$lastInsertId = intval($id);
    }

    /**
     * Required to prevent insert index pollution from logs (\AcronisCloud\Service\Logger\Whmcs\ModuleLogHandler::write)
     *
     * @param AbstractModel $model
     */
    protected function enforceInsertId(AbstractModel $model)
    {
        if (self::$lastInsertId) {
            $model->setId(self::$lastInsertId);
            self::$lastInsertId = null;
        }
    }

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