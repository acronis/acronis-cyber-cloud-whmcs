<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Model;

use Illuminate\Database\Eloquent\Model;

abstract class AbstractModel extends Model
{
    const COLUMN_ID = 'id';

    /**
     * Valid types for column casting
     */
    const TYPE_INTEGER = 'integer';
    const TYPE_ARRAY = 'array';

    /**
     * Required for Eloquent
     *
     * @var string
     */
    protected $table;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = static::TABLE;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->getAttributeValue(static::COLUMN_ID);
    }

    /**
     * @param $id
     */
    public function setId($id)
    {
        $this->setAttribute(static::COLUMN_ID, intval($id));
    }
}