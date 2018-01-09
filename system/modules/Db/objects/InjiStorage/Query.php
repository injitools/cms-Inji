<?php
/**
 * Created by IntelliJ IDEA.
 * User: inji
 * Date: 07.01.2018
 * Time: 16:39
 */

namespace Inji\Db\InjiStorage;

use \Inji\Tools;

class Query implements \Inji\Db\DriverQuery {
    /**
     * @var \Inji\Db\InjiStorage
     */
    public $connection;
    public $whereArray = [];
    public $table = [];
    public $dbOptions = [];
    public $cols = [];
    public $colPrefix = '';

    public function __construct($connection) {
        $this->connection = $connection;
    }

    public function setDbOption($name, $value) {
        $this->dbOptions[$name] = $value;
    }

    public function cols($cols) {
        if (is_array($cols)) {
            $this->cols = array_merge($this->cols, array_values($cols));
        } else {
            $this->cols[] = $cols;
        }
    }

    public function colPrefix($colPrefix) {
        $this->colPrefix = $colPrefix;
    }

    public function limit($limit) {
        // TODO: Implement limit() method.
    }

    public function order($col, $direction = 'ASC') {
        // TODO: Implement order() method.
    }

    public function start($start) {
        // TODO: Implement start() method.
    }

    public function where($col, $value = true, $comparision = '=', $concatenation = 'AND') {
        if (is_array($col) && !Tools::isAssoc($col)) {
            $this->whereArray[] = $col;
        } else {
            $this->whereArray[] = [$col, $value, $comparision, $concatenation];
        }
        return $this;
    }

    public function setTable($tableName) {
        $this->table = $tableName;
    }

    public function select($tableName = null) {
        if (!$tableName) {
            $tableName = $this->table;
        }
        return new Result($this->connection->getItems($tableName, $this->cols, $this->whereArray, $this->dbOptions), $this);
    }

    public function insert(string $tableName, array $values) {
        if (!$tableName) {
            $tableName = $this->table;
        }
        return $this->connection->addItem($tableName, $values, $this->dbOptions);
    }

    public function update(string $tableName, array $values) {
        if (!$tableName) {
            $tableName = $this->table;
        }
        return $this->connection->updateItems($tableName, $this->whereArray, $values, $this->dbOptions);
    }

    public function delete(string $tableName = '') {
        if (!$tableName) {
            $tableName = $this->table;
        }
        return $this->connection->deleteItems($tableName, $this->whereArray, $this->dbOptions);
    }

}