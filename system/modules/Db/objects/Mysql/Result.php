<?php

/**
 * Result class for mysql driver
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Db\Mysql;

use Inji\Db\DriverResult;

class Result implements DriverResult {

    public $pdoResult = null;

    public function __construct($dbResult) {
        $this->pdoResult = $dbResult;
    }

    public function getArray($keyCol = '') {
        $key = \Inji\App::$cur->log->start('parse result');
        if (!$keyCol) {
            return $this->pdoResult->fetchAll(\PDO::FETCH_ASSOC);
        }
        $array = [];
        while ($row = $this->pdoResult->fetch(\PDO::FETCH_ASSOC)) {
            if (isset($row[$keyCol])) {
                $array[$row[$keyCol]] = $row;
            } else {
                $array[] = $row;
            }
        }
        \Inji\App::$cur->log->end($key);
        return $array;
    }

    public function getObjects($class, $keyCol = '') {
        $key = \Inji\App::$cur->log->start('parse result');
        $array = [];
        while ($object = $this->pdoResult->fetchObject($class)) {
            if ($keyCol) {
                $array[$object->$keyCol] = $object;
            } else {
                $array[] = $object;
            }
        }
        \Inji\App::$cur->log->end($key);
        return $array;
    }

    public function fetch($className = '') {
        if ($className) {
            return $this->pdoResult->fetchObject($className);
        } else {
            return $this->pdoResult->fetch(\PDO::FETCH_ASSOC);
        }
    }
}