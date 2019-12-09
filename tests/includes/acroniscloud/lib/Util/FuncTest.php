<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Util;

function testFnGetCallerNameDeep1($deep)
{
    return Func::getCallerName($deep);
}

function testFnGetCallerNameDeep2($deep)
{
    return testFnGetCallerNameDeep1($deep);
}

function testFnGetCallerNameDeep3($deep)
{
    return testFnGetCallerNameDeep2($deep);
}

class FuncTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \RuntimeException
     */
    public function testGetCallerNameException()
    {
        Func::getCallerName(512);
    }

    public function testGetCallerNameDefaultDeep()
    {
        $fn = function () {
            return Func::getCallerName();
        };

        $this->assertEquals(__METHOD__, $fn());
    }

    /**
     * @dataProvider getMethodCallers
     */
    public function testGetCallerNameForMethod($fnName, $deep)
    {
        $this->assertEquals(__CLASS__ . '::' . $fnName, $this->$fnName($deep));
    }

    /**
     * @dataProvider getFunctionCallers
     */
    public function testGetCallerNameForFunction($fnName, $deep)
    {
        $this->assertEquals($fnName, $fnName($deep));
    }

    public function getMethodCallers()
    {
        return [
            ['getCallerNameDeep1', 1],
            ['getCallerNameDeep2', 2],
            ['getCallerNameDeep3', 3],
        ];
    }

    public function getFunctionCallers()
    {
        return [
            [__NAMESPACE__ . '\testFnGetCallerNameDeep1', 1],
            [__NAMESPACE__ . '\testFnGetCallerNameDeep2', 2],
            [__NAMESPACE__ . '\testFnGetCallerNameDeep3', 3],
        ];
    }

    private function getCallerNameDeep3($deep)
    {
        return $this->getCallerNameDeep2($deep);
    }

    private function getCallerNameDeep2($deep)
    {
        return $this->getCallerNameDeep1($deep);
    }

    private function getCallerNameDeep1($deep)
    {
        return Func::getCallerName($deep);
    }
}
