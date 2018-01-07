<?php

class ModelTest extends \PHPUnit\Framework\TestCase {


    public function testLocalInsertStorage() {
        $time = time();
        $config = \Inji\Db\Options::new(['connect_name' => $time]);
        $config->connectionName = 'injiStorage';
        $config->dbOptions['share'] = true;
        $config->save();

        $builder = \Inji\Db\Options::sharedStorage();
        $builder->where('id', $config->pk());
        $config = $builder->get();

        $this->assertEquals($time, $config->connect_name);

        return $config;
    }

    /**
     * @depends testLocalInsertStorage
     */
    public function testLocalStorageUpdate($config) {

        $id = $config->id;

        $time = $config->connect_name + 1;
        $config->connect_name = $time;
        $config->save();

        $builder = \Inji\Db\Options::sharedStorage();
        $builder->where('id', $config->pk());
        $config = $builder->get();

        $this->assertEquals($id, $config->id);
        $this->assertEquals($time, $config->connect_name);
        return $config;
    }

    /**
     * @depends testLocalInsertStorage
     */
    public function testLocalStorageGetList($config) {
        $time = $config->connect_name + 1;
        $config->connect_name = $time;
        $config->save();

        $builder = \Inji\Db\Options::sharedStorage();
        $configs = $builder->getList();

        $this->assertTrue(isset($configs[$config->id]));
    }

    /**
     * @depends testLocalStorageUpdate
     */
    public function testLocalStorageDelete($config) {
        $config->delete();

        $builder = \Inji\Db\Options::connection('injiStorage');
        $builder->setDbOption('share', true);
        $builder->where('id', $config->pk());
        $config = $builder->get();

        $this->assertEmpty($config);
    }
}