<?php
/**
 * Created by IntelliJ IDEA.
 * User: inji
 * Date: 07.01.2018
 * Time: 17:28
 */

namespace Inji\Db;


Interface DriverResult {
    public function __construct($dbResult, $query);

    public function fetch(string $className);

    public function fetchAll(string $className, $keyCol = '');

}