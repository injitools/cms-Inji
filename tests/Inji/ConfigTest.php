<?php

use  Inji\Config;

class ConfigTest extends \PHPUnit\Framework\TestCase {

    public function testSystem() {
        $config = Config::system(true);
        $this->assertNotEmpty($config);

        $config = Config::system();
        $this->assertNotEmpty($config);

        $time = time();

        $config['test'] = $time;
        Config::save('system', $config);

        $config = Config::system(true);
        $this->assertEquals($time, $config['test']);

        $config = \Inji\Config::system(true, 'notExist');
        $this->assertEmpty($config);
    }

    public function testCustom() {
        $temp = INJI_PROGRAM_DIR . DIRECTORY_SEPARATOR . 'testConf.php';
        $time = time();

        Config::save($temp, ['test' => $time]);
        $config = Config::custom($temp, true);
        $this->assertEquals(['test' => $time], $config);

        $config = Config::custom($temp);
        $this->assertEquals(["test" => $time], $config);

        $config = Config::custom('notExist');
        $this->assertEmpty($config);
    }

    public function testApp() {
        $time = time();
        Config::save('app', ['test' => $time]);
        $config = Config::app(false, true);
        $this->assertEquals(["test" => $time], $config);

        $config = Config::app();
        $this->assertEquals(["test" => $time], $config);

        $config = Config::app(new \Inji\App(['path' => 'notExits']));
        $this->assertEmpty($config);
    }

    public function testShare() {
        $time = time();
        Config::save('share', ['test' => $time]);

        $config = Config::share('', true);
        $this->assertEquals(["test" => $time], $config);

        $config = Config::share();
        $this->assertEquals(["test" => $time], $config);

        Config::save('share', ['test' => $time], 'TestModule');

        $config = Config::share('TestModule', true);
        $this->assertEquals(["test" => $time], $config);

        $config = Config::share('TestModule');
        $this->assertEquals(["test" => $time], $config);

        $config = Config::share('notExist');
        $this->assertEmpty($config);
    }

    public function testModule() {
        $time = time();
        Config::save('module', ['test' => $time], 'TestModule');

        $config = Config::module('TestModule', null, true);
        $this->assertEquals(["test" => $time], $config);

        $config = Config::module('TestModule');
        $this->assertEquals(["test" => $time], $config);

        $config = Config::module('notExist');
        $this->assertEmpty($config);
    }
}