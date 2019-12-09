<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Repository;

use BadMethodCallException;

class AbstractRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $stub = $this->getMockForAbstractClass(AbstractRepository::class);

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Not implemented.');

        $stub->create([], 1);
    }

    public function testUpdate()
    {
        $stub = $this->getMockForAbstractClass(AbstractRepository::class);

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Not implemented.');

        $stub->update([], 1);
    }

    public function testDelete()
    {
        $stub = $this->getMockForAbstractClass(AbstractRepository::class);

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Not implemented.');

        $stub->delete(1);
    }

    public function testAll()
    {
        $stub = $this->getMockForAbstractClass(AbstractRepository::class);

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Not implemented.');

        $stub->all();
    }

    public function testFind()
    {
        $stub = $this->getMockForAbstractClass(AbstractRepository::class);

        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Not implemented.');

        $stub->find(1);
    }
}
