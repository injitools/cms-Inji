<?php

/**
 * Query class for mysql driver
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Db\Mysql;

class Query extends \Object {

    public $curInstance = null;
    public $where = [];
    public $whereString = '';
    public $cols = [];
    public $order = NULL;
    public $join = [];
    public $group = [];
    public $limit = '';
    public $error = '';
    public $query = '';
    public $table = '';
    public $operation = '';
    public $indexes = [];
    public $params = [];

    function __construct($instance = null) {
        if (!$instance) {
            $this->curInstance = \App::$cur->db->connection;
        } else {
            $this->curInstance = $instance;
        }
    }

    function insert($table, $data) {
        $this->operation = 'INSERT';
        $this->table = $table;
        $this->cols = $data;
        $this->query();
        return $this->curInstance->pdo->lastInsertId();
    }

    public function select($table) {
        $this->operation = 'SELECT';
        $this->table = $table;
        return $this->query();
    }

    function update($table, $data) {
        $this->operation = 'UPDATE';
        $this->table = $table;
        $this->cols = $data;
        $result = $this->query();
        return $result->pdoResult->rowCount();
    }

    function delete($table) {
        $this->operation = 'DELETE';
        $this->table = $table;
        $result = $this->query();
        return $result->pdoResult->rowCount();
    }

    function createTable($table_name, $cols, $indexes = []) {
        $this->operation = 'CREATE TABLE';
        $this->table = $table_name;
        $this->cols = $cols;
        $this->indexes = $indexes;
        return $this->query();
    }

    public function cols($cols) {
        if (is_array($cols)) {
            $this->cols = array_merge($this->cols, array_values($cols));
        } else {
            $this->cols[] = $cols;
        }
    }

    public function join($table, $where = false, $type = 'LEFT', $alias = '') {
        $this->join[] = [$table, $where, $type, $alias];
    }

    function where($where = '', $value = '', $operation = false, $concatenation = 'AND') {
        if (!is_array($where)) {
            $this->where[] = [$where, $value, $operation, $concatenation];
        } else {
            $this->where[] = $where;
        }
    }

    function group($colname) {
        $this->group[] = $colname;
    }

    public function order($order, $type = 'ASC') {


        if (!is_array($order)) {
            $this->order[] = "`{$order}` {$type}";
        } else {
            foreach ($order as $item)
                if (!is_array($item)) {
                    call_user_func_array(array($this, 'order'), $order);
                    break;
                } else
                    $this->order($item);
        }
    }

    public function limit($start = 0, $len = 0) {
        $start = intval($start);
        $len = intval($len);
        $this->limit = "LIMIT {$start}";
        if ($len !== 0)
            $this->limit .= ",{$len}";
    }

    function buildJoin($table, $where = false, $type = 'LEFT', $alias = '') {
        $join = '';
        if (is_array($table)) {
            $join .= call_user_func_array([$this, 'buildJoin'], $table);
        } else {
            $join .= " {$type} JOIN `{$this->table_prefix}{$table}`";
            if ($alias)
                $join .= " AS `{$alias}`";
            if ($where)
                $join .= " ON {$where}";
        }
        return $join;
    }

    function buildWhere($where = '', $value = '', $operation = false, $concatenation = 'AND') {
        $params = [];
        if (!is_array($where)) {
            if (!$operation)
                $operation = '=';

            if ($concatenation === false)
                $concatenation = 'AND';
            elseif ($concatenation === true)
                $concatenation = '';

            if ($this->whereString == NULL)
                $this->whereString = ' WHERE ';

            if (stristr($operation, 'IN') || stristr($operation, 'NOT IN')) {
                if (!preg_match('!\(!', $value) && !preg_match('![^0-9,\.\(\) ]!', $value))
                    $value = "({$value})";
                elseif (preg_match('!\(!', $value) && preg_match('![^0-9,\.\(\) ]!', $value))
                    $value = "\"{$value}\"";
            }
            elseif (!in_array($value, array('CURRENT_TIMESTAMP'))) {
                $this->params[] = $value;
                $value = "?";
            }

            if (substr($this->whereString, -1, 1) == '(' || substr($this->whereString, -2, 2) == 'E ')
                $this->whereString .= " `{$where}` {$operation} {$value} ";
            else
                $this->whereString .= "{$concatenation} `{$where}` {$operation} {$value} ";
        }
        else {
            $i = -1;
            while (isset($where[++$i])) {
                $item = $where[$i];
                if (isset($where[$i + 1]) && !isset($where[$i - 1]) && is_array($where[$i])) {
                    if ($this->whereString != NULL && substr($this->whereString, -1, 1) != '(' && $this->whereString != 'WHERE ') {
                        if (!isset($item[3]))
                            $concatenation = 'AND';
                        else
                            $concatenation = $item[3];

                        $this->whereString .= "{$concatenation} ";
                    }

                    if ($this->whereString != NULL)
                        $this->whereString .= '(';
                    else
                        $this->whereString = 'WHERE (';
                }

                if (!is_array($item)) {
                    call_user_func_array(array($this, 'buildWhere'), $where);
                    break;
                } else {
                    if ($this->whereString != NULL && substr($this->whereString, -1, 1) != '(')
                        if (!isset($item[3]))
                            $concatenation = 'AND';
                        else
                            $concatenation = $item[3];
                    elseif (substr($this->whereString, -1, 1) != '(')
                        $this->whereString = 'WHERE ';

                    $this->buildWhere($item);
                }
                if (!isset($where[$i + 1]) && isset($where[$i - 1]))
                    $this->whereString .= ') ';
            }
        }
    }

    function buildQuery() {
        $params = [];
        $query = $this->operation;
        $this->operation = strtoupper($this->operation);

        switch ($this->operation) {
            case 'SELECT':
                
                $query .= ' ' . (!$this->cols ? '*' : ((is_array($this->cols) ? implode(',', $this->cols) : $this->cols)));
            case 'DELETE':
                $query .= ' FROM';
                break;
            case 'INSERT':
                $query .= ' INTO';
                break;
        }
        $query .= " `{$this->curInstance->db_name}`.`{$this->curInstance->table_prefix}{$this->table}`";
        if ($this->join) {
            $query .= $this->buildJoin($this->join);
        }
        switch ($this->operation) {
            case 'INSERT':
                $this->params = array_merge($this->params, array_values($this->cols));
                $query .= ' (`' . implode('`,`', array_keys($this->cols)) . '`) VALUES (' . rtrim(str_repeat('?,', count($this->cols)), ',') . ')';
                break;
            case 'CREATE TABLE':
                $query .= " (";
                foreach ($this->cols as $col_name => $param) {
                    if ($param == 'pk') {
                        $param = "int(11) NOT NULL AUTO_INCREMENT, PRIMARY KEY (`{$col_name}`)";
                    }
                    $query .= " `{$col_name}` {$param},";
                }
                $query = rtrim($query, ',');
                $query .= ' ' . implode(',', $this->indexes);
                $query .= ") ENGINE = INNODB CHARACTER SET utf8 COLLATE utf8_general_ci";
                break;
            case 'UPDATE':
                foreach ($this->cols as $key => $item) {
                    if (!in_array($item, array('CURRENT_TIMESTAMP'))) {
                        $this->params[] = $item;
                        $updates[] = "`{$key}` = ?";
                    } else {
                        $updates[] = "`{$key}` = {$item}";
                    }
                }
                $update = implode(',', $updates);
                $query .=" SET {$update}";
            case 'SELECT':
            case 'DELETE':
                $this->buildWhere($this->where);
                if ($this->whereString) {
                    $query .= ' ' . $this->whereString;
                }
                break;
        }
        if ($this->group) {
            $query .= ' GROUP BY `' . implode('`,`', $this->group) . '`';
        }
        if ($this->order) {
            $query .= ' ORDER BY ' . implode(',', $this->order);
        }
        if ($this->limit) {
            $query .= ' ' . $this->limit;
        }
        return ['query' => $query, 'params' => $this->params];
    }

    function query($query = []) {
        if (!$query) {
            $this->params = [];
            $query = $this->buildQuery();
        }

        if (is_string($query)) {
            $query = ['query' => $query, 'params' => $this->params];
        }
        $prepare = $this->curInstance->pdo->prepare($query['query']);
        $prepare->execute($query['params']);
        $result = new Result();
        $result->pdoResult = $prepare;
        $this->curInstance->dbInstance->curQuery = null;

        return $result;
    }

}