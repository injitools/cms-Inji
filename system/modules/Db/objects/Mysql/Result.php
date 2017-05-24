<?php

/**
 * Result class for mysql driver
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Db\Mysql;

class Result extends \Object {

    public $pdoResult = null;

    public function getArray($keyCol = '') {
        if (!$keyCol) {
            return $this->pdoResult->fetchAll(\PDO::FETCH_ASSOC);
        }
        $array = [];
        while ($row = $this->pdoResult->fetch(\PDO::FETCH_ASSOC)) {
            $array[$row[$keyCol]] = $row;
        }
        return $array;
    }

    public function getObjects($class, $keyCol = '') {

        $array = [];
        while ($object = $this->pdoResult->fetchObject($class)) {
            if ($keyCol) {
                $array[$object->$keyCol] = $object;
            } else {
                $array[] = $object;
            }
        }

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