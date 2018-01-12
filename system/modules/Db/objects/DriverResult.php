<?php


namespace Inji\Db;


Interface DriverResult {
    public function __construct($dbResult, $query);

    public function fetch(string $className = '');

    public function fetchAll(string $className = '', $keyCol = '');

}