<?php
/**
 * @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
 */

namespace AcronisCloud\Service\Logger;

use AcronisCloud\Service\Locator;
use Tests\Reflection;

class TestLoggerAwareTrait extends \PHPUnit_Framework_TestCase
{
    public function testGetLogger()
    {
        $instance = new \stdClass();
        $locator = Locator::getInstance();
        $locator->set(LoggerFactory::NAME, $instance);

        $loggerTrait = $this->getMockForTrait(LoggerAwareTrait::class);

        $reflection = new Reflection();

        $this->assertSame($instance, $reflection->invokeInaccessibleMethod($loggerTrait, 'getLogger'));
    }
}
