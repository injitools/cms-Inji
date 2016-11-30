<?php

class UiTest extends PHPUnit\Framework\TestCase {
    /**
     * @covers Modules::install
     */
    function setUp() {
        \App::$cur->Modules->install('Ui');
    }

    /**
     * @covers Ui::init
     */
    public function testInit() {
        \App::$cur->Ui;
        $this->assertEquals(true, true);
    }
}