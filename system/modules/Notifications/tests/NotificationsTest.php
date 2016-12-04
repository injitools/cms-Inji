<?php

class NotificationsTest extends PHPUnit_Framework_TestCase {

    public function setUp() {
        \App::$cur->Modules->install('Users');
        \App::$cur->Modules->install('Notifications');
    }

    /**
     * @covers Model
     * @covers Notifications::getChanel
     */
    public function testGetChanel() {
        \Notifications\Chanel::deleteList();
        $chanel = App::$cur->Notifications->getChanel('test');
        $this->verifyChanel($chanel);
    }

    /**
     * @covers Model
     * @covers Notifications::getCurDevice
     * @covers Notifications::setDeviceKey
     * @covers Notifications::getDeviceKey
     */
    public function testGetCurDevice() {
        \Notifications\Subscriber\Device::deleteList();
        if (isset($_COOKIE['notification-device'])) {
            unset($_COOKIE['notification-device']);
        }
        if (isset($_SESSION['notification-device'])) {
            unset($_SESSION['notification-device']);
        }

        $device = App::$cur->Notifications->getCurDevice();
        $this->assertFalse($device);
        $device = App::$cur->Notifications->getCurDevice(true);
        $this->verifyDevice($device);
        $device = App::$cur->Notifications->getCurDevice();
        $this->verifyDevice($device);
        $device->delete();
        $device = App::$cur->Notifications->getCurDevice();
        $this->assertFalse($device);
    }

    /**
     * @covers Model
     * @covers Notifications::getCurSubscriber
     * @covers Notifications::getCurDevice
     * @covers Notifications::setDeviceKey
     * @covers Notifications::getDeviceKey
     */
    public function testGetCurSubscriber() {
        \Notifications\Subscriber::deleteList();
        \Notifications\Subscriber\Device::deleteList();
        if (isset($_COOKIE['notification-device'])) {
            unset($_COOKIE['notification-device']);
        }
        if (isset($_SESSION['notification-device'])) {
            unset($_SESSION['notification-device']);
        }

        $subscriber = App::$cur->Notifications->getCurSubscriber();
        $this->assertFalse($subscriber);
        $subscriber = App::$cur->Notifications->getCurSubscriber(true);
        $this->verifySubscriber($subscriber);
        $subscriber = App::$cur->Notifications->getCurSubscriber();
        $this->verifySubscriber($subscriber);
    }

    /**
     * @covers Model
     * @covers Server\Result
     * @covers Notifications::subscribe
     * @covers Notifications::getCurSubscriber
     * @covers Notifications::getCurDevice
     * @covers Notifications::setDeviceKey
     * @covers Notifications::getDeviceKey
     */
    public function testSubscribe() {
        \Notifications\Subscriber::deleteList();
        \Notifications\Subscriber\Device::deleteList();
        \Notifications\Chanel::deleteList();
        if (isset($_COOKIE['notification-device'])) {
            unset($_COOKIE['notification-device']);
        }
        if (isset($_SESSION['notification-device'])) {
            unset($_SESSION['notification-device']);
        }
        \Inji::$inst->exitOnStop = false;

        ob_start();
        $result = App::$cur->Notifications->subscribe('test');
        $content = ob_get_contents();
        ob_end_clean();
        $content = json_decode($content, true);
        $this->assertEquals('Вы были подписаны на уведомления', $content['successMsg']);
        $this->assertTrue($result);

        ob_start();
        $result = App::$cur->Notifications->subscribe('test');
        $content = ob_get_contents();
        ob_end_clean();
        $content = json_decode($content, true);
        $this->assertEquals('Вы уже подписаны', $content['successMsg']);
        $this->assertTrue($result);
    }

    private function verifyChanel($chanel) {
        $this->assertTrue(is_object($chanel));
        $this->assertEquals('Notifications\Chanel', get_class($chanel));
        $this->assertEquals('test', $chanel->name);
        $this->assertTrue((int) $chanel->id > 0);
    }

    private function verifySubscriber($subscriber) {
        $this->assertTrue(is_object($subscriber));
        $this->assertEquals('Notifications\Subscriber', get_class($subscriber));
        $this->assertTrue((int) $subscriber->id > 0);
    }

    private function verifyDevice($device) {
        $this->assertTrue(is_object($device));
        $this->assertEquals('Notifications\Subscriber\Device', get_class($device));
        $this->assertEquals($device->key, App::$cur->Notifications->getDevicekey());
        $this->assertTrue((int) $device->id > 0);
    }
}