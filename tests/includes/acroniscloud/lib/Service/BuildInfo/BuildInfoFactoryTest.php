<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
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
