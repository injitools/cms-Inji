<?php

namespace Inji\Model;

use Inji\App;
use Inji\Tools;

class Builder {
    public $connection = 'default';
    public $modelName = '';
    public $whereArray = [];
    public $dbOptions = [];
    /**
     * @var \Inji\App
     */
    public $app;

    public function __construct(string $modelName, App $app = null) {
        $this->modelName = $modelName;
        if (is_null($app)) {
            $this->app = App::$primary;
        } else {
            $this->app = $app;
        }
    }

    public function connection($connection = 'default') {
        $this->connection = $connection;
        return $this;
    }

    public function getConnection() {
        return is_string($this->connection) ? $this->app->db($this->connection) : $this->connection;
    }

    public function model(string $modelName) {
        $this->modelName = $modelName;
        return $this;
    }

    public function where($col, $value = true, $comparision = '=', $concatenation = 'AND') {
        if (is_array($col) && !Tools::isAssoc($col)) {
            $this->whereArray[] = $col;
        } else {
            $this->whereArray[] = [$col, $value, $comparision, $concatenation];
        }
        return $this;
    }

    /**
     * @return \Inji\Db\DriverQuery
     */
    public function createQuery() {
        /**
         * @var \Inji\Db\DriverQuery
         */
        $query = $this->getConnection()->newQuery();
        foreach ($this->dbOptions as $dbOption => $value) {
            $query->setDbOption($dbOption, $value);
        }
        if ($this->whereArray) {
            $query->where($this->whereArray);
        }
        $query->setTable($this->modelName::table());
        return $query;
    }

    /**
     * @param array $options
     * @return bool||\Inji\Model
     */
    public function get($options = []) {
        $query = $this->createQuery();
        $result = $query->select();
        if (!$result) {
            return false;
        }
        return $result->fetch(empty($options['array']) ? $this->modelName : '');
    }

    /**
     * @param array $options
     * @return bool||\Inji\Model
     */
    public function getList($options = []) {
        $query = $this->createQuery();
        $result = $query->select();
        if (!$result) {
            return false;
        }
        return $result->fetchAll(empty($options['array']) ? $this->modelName : '', $this->modelName::index());
    }

    public function insert($values) {
        $query = $this->createQuery();
        return $query->insert('', $values);
    }

    public function update($values) {
        $query = $this->createQuery();
        return $query->update('', $values);
    }

    public function delete() {
        $query = $this->createQuery();
        return $query->delete('');
    }

    public function setDbOption($optionName, $value) {
        $this->dbOptions[$optionName] = $value;
        return $this;
    }
}