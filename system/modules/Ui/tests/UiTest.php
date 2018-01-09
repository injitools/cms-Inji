<?php

class UiTest extends \PHPUnit\Framework\TestCase {
    /**
     * @covers Modules::install
     */
    public function setUp() {
        \Inji\App::$cur->Modules->install('Ui');
    }

    /**
     * @covers Ui::init
     */
    public function testInit() {
        \Inji\App::$cur->Ui;
        $this->assertEquals(true, true);
    }
}