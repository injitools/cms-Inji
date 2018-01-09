<?php

namespace Inji\Model;

use Inji\App;
use Inji\Tools;

class Builder {
    /**
     * @var string|\Inji\Db
     */
    public $connection = 'default';
    public $modelName = '';
    public $whereArray = [];
    public $dbOptions = [];
    public $count = false;
    public $colPrefix = '';
    public $order = [];
    public $start = 0;
    public $limit = 0;
    /**
     * @var \Inji\App
     */
    public $app;

    public function __construct(string $modelName, ?App $app = null) {
        $this->modelName = $modelName;
        if (is_null($app)) {
            $this->app = App::$primary;
        } else {
            $this->app = $app;
        }
        $this->colPrefix($modelName::colPrefix());
    }

    public function connection($connection = 'default') {
        $this->connection = $connection;
        return $this;
    }

    /**
     * @return \Inji\Db
     */
    public function getConnection() {
        return is_string($this->connection) ? $this->app->db($this->connection) : $this->connection;
    }

    public function model(string $modelName) {
        $this->modelName = $modelName;
        $this->colPrefix($modelName::colPrefix());
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

    public function order($order, $type = 'ASC') {
        if (is_array($order)) {
            $this->order[] = $order;
        } else {
            $this->order[] = [$order, $type];
        }
    }

    public function count($col = '*') {
        $this->count = $col;
    }

    public function start($start = 0) {
        $this->start = $start;
    }

    public function limit($limit = 0) {
        $this->limit = $limit;
    }

    public function colPrefix($colPrefix) {
        $this->colPrefix = $colPrefix;
    }

    /**
     * @return false|\Inji\Db\DriverQuery
     */
    public function createQuery() {
        $query = $this->getConnection()->newQuery();

        if (!$query) {
            return false;
        }
        $query->colPrefix($this->colPrefix);
        foreach ($this->dbOptions as $dbOption => $value) {
            $query->setDbOption($dbOption, $value);
        }
        if ($this->whereArray) {
            $query->where($this->whereArray);
        }
        $query->setTable($this->modelName::table());
        if ($this->order) {
            $query->order($this->order);
        }
        $query->start($this->start);
        if ($this->limit) {
            $query->limit($this->limit);
        }
        if ($this->count) {
            $query->cols('COUNT(' . $this->count . ') as count');
        }
        return $query;
    }

    /**
     * @param array $options
     * @return bool|\Inji\Model
     */
    public function get($options = []) {
        $query = $this->createQuery();
        if (!$query) {
            return false;
        }
        $result = $query->select();
        if (!$result) {
            return false;
        }
        if ($this->count) {
            $count = $result->fetch();
            return $count ? $count['count'] : 0;
        }
        return $result->fetch(empty($options['array']) ? $this->modelName : '');
    }

    /**
     * @param array $options
     * @return bool|\Inji\Model[]
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

    public function setDbOptions($options) {
        foreach ($options as $optionName => $value) {
            $this->setDbOption($optionName, $value);
        }
        return $this;
    }

    public function setDbOption($optionName, $value) {
        $this->dbOptions[$optionName] = $value;
        return $this;
    }
}