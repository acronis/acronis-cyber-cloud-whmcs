<?php
/**
 * @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
 */

namespace AcronisCloud\Service\Logger\Processor;

class MessageProcessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getRecords
     */
    public function testProcessor($message, $context, $expected)
    {
        $processor = new MessageProcessor();
        $record = $processor([
            'message' => $message,
            'context' => $context,
        ]);
        $actual = $record['message'];

        $this->assertEquals($expected, $actual);
    }

    public function getRecords()
    {
        $object = new \stdClass();
        $object->a = 1;
        $object->b = 2;

        return [
            'no message' => [
                '',
                [],
                '',
            ],
            'no placeholders' => [
                'no placeholders',
                [],
                'no placeholders',
            ],
            'numbers' => [
                '{0}, {1}, {2}, {3}, {4}, {5}',
                [-1, 0, 1, -1.1, 0.1, 1.1],
                '-1, 0, 1, -1.1, 0.1, 1.1',
            ],
            'boolean and null' => [
                '{0}, {1}, {2}',
                [true, false, null],
                'true, false, null',
            ],
            'strings' => [
                '{0}, {1}, {2}, {3}, {4}, {5}, {6}',
                ['', 'string', '123', '123.123', 'false', 'null', '0'],
                ', string, 123, 123.123, false, null, 0',
            ],
            'datetime' => [
                '{0}',
                [new \DateTime('2019-03-05T12:50:30+00:00')],
                '2019-03-05T12:50:30+00:00',
            ],
            'object' => [
                '{0}',
                [$object],
                '{"a":1,"b":2}',
            ],
        ];
    }
}