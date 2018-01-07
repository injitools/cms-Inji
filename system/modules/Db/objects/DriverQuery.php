<?php

namespace Inji\Db;

Interface DriverQuery {
    public function __construct($connection);

    public function setDbOption($name, $value);

    public function setTable($tableName);

    public function where($col, $value, $comparision, $concatenation);

    /**
     * @param string $tableName
     * @return DriverResult
     */
    public function select(string $tableName = '');

    public function insert(string $tableName, array $values);

    public function update(string $tableName, array $values);

    public function delete(string $tableName);
}