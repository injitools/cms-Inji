<?php
/**
 * Created by IntelliJ IDEA.
 * User: inji
 * Date: 07.01.2018
 * Time: 16:39
 */

namespace Inji\Db\InjiStorage;


use Inji\Db\DriverResult;
use Inji\Model;

class Result implements DriverResult {
    public $result = [];
    public $curKey = 0;
    /**
     * @var Query
     */
    public $query;

    public function __construct($dbResult, $query) {
        $this->result = $dbResult;
        $this->query = $query;
        reset($dbResult);
        $this->curKey = key($dbResult);
    }

    public function fetch(string $className = '') {
        if (!$this->result) {
            return false;
        }
        $item = $this->result[$this->curKey];
        next($this->result);
        $this->curKey = key($this->result);
        if ($className) {
            /**
             * @var Model
             */
            $item = new $className($item);
            $item->connectionName = $this->query->connection->dbInstance;
            $item->dbOptions = $this->query->dbOptions;
            $item->app = $this->query->connection->dbInstance->app;
            return $item;
        }
        return $item;
    }

    public function fetchAll(string $className = '', $keyCol = '') {
        if (!$this->result) {
            return [];
        }
        $items = [];
        foreach ($this->result as $item) {
            if ($className) {
                /**
                 * @var Model
                 */
                $item = new $className($item);
                $item->connectionName = $this->query->connection->dbInstance;
                $item->dbOptions = $this->query->dbOptions;
                $item->app = $this->query->connection->dbInstance->app;
            }
            if ($keyCol) {
                $key = $className ? $item->$keyCol : $item[$keyCol];
                $items[$key] = $item;
            } else {
                $items[] = $item;
            }
        }
        return $items;
    }
}