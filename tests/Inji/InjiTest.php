<?php

class InjiTest extends \PHPUnit\Framework\TestCase {

    /**
     * @covers Inji::listen
     * @covers Inji::event
     * @covers Inji::unlisten
     */
    public function testNotSavingEvent() {
        \Inji::$inst->listen('testEvent', 'testCallback', function() {
            return true;
        });
        $this->assertEquals(true, Inji::$inst->event('testEvent'));
        \Inji::$inst->unlisten('testEvent', 'testCallback');
    }

    /**
     * @covers Inji::listen
     * @covers Inji::event
     * @covers Inji::unlisten
     */
    public function testSavingEvent() {
        \Inji::$inst->listen('testEvent2', 'testCallback', 'InjiTestcallback', true);
        $this->assertEquals(true, Inji::$inst->event('testEvent2'));
        \Inji::$inst->unlisten('testEvent2', 'testCallback', true);
    }

    /**
     * @covers Inji::listen
     * @covers Inji::event
     */
    public function testArrayWithCallback() {
        $callbackOptions = ['callback' => 'InjiTestcallbackArray', 'data' => 'data'];
        \Inji::$inst->listen('testEvent3', 'testCallback', $callbackOptions);
        $this->assertEquals($callbackOptions, Inji::$inst->event('testEvent3')['callbackOptions']);
    }
}

function InjiTestcallback() {
    return true;
}

function InjiTestcallbackArray($event, $callbackOptions) {
    return ['event' => $event, 'callbackOptions' => $callbackOptions];
}
