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
    public $query = null;

    public function __construct($dbResult, $query) {
        $this->pdoResult = $dbResult;
        $this->query = $query;
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
        while ($object = $this->fetch($class)) {
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
        $rawItem = $this->pdoResult->fetch(\PDO::FETCH_ASSOC);
        if (!$rawItem) {
            return false;
        }
        $item = [];
        if ($this->query->colPrefix) {
            foreach ($rawItem as $key => $value) {
                $item[substr($key, strlen($this->query->colPrefix))] = $value;
            }
        } else {
            $item = $rawItem;
        }
        if ($className) {
            return $className::create($item);
        } else {
            return $item;
        }
    }

    public function fetchAll(string $className = '', $keyCol = '') {
        if (!$className) {
            return $this->getArray($keyCol);
        }
        return $this->getObjects($className, $keyCol);
    }
}