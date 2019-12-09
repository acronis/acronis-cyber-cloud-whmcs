<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\BuildInfo;

class BuildInfoFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testCreateInstance()
    {
        $factory = $this->createFactory();
        $this->assertInstanceOf(BuildInfoParser::class, $factory->createInstance());
    }

    protected function createFactory()
    {
        return new BuildInfoFactory();
    }
}
