<?php

use Understand\Processor\UnderstandProcessor;

class UnderstandProcessorTest extends PHPUnit_Framework_TestCase
{
    public function testProcessorResults()
    {
        $fields = ['one' => function() {
            return 1;
        }, 'two' => function() {
            return 2;
        }];

        $processor = new UnderstandProcessor($fields);
        $record = [
            'message' => 'test'
        ];

        $results = $processor($record);

        $this->assertEquals($record['message'], $results['message']);
        $this->assertEquals(1, $results['one']);
        $this->assertEquals(2, $results['two']);
    }
}