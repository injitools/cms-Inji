<?php

/**
 * Mysql work class
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Db;

class Mysql extends \Inji\InjiObject {

    public $config = []; // настройки подключения выбраной базы
    public $connect = false; // ярлык соединения с MySQL
    public $encoding = 'utf-8'; // установленная кодировка
    public $db_name = 'test'; // выбраная в данный момент база
    public $table_prefix = 'inji_'; // префикс названий таблиц
    public $pdo = null;
    public $lastQuery = '';
    public $last_error = '';
    public $noConnectAbort = false;
    public $dbInstance;

    /**
     * Подключение к MySQL
     */
    public function init($connect_options) {
        extract($connect_options);
        if (isset($db_name)) {
            $this->db_name = $db_name;
        }
        if (isset($encoding)) {
            $this->encoding = $encoding;
        }
        if (isset($table_prefix)) {
            $this->table_prefix = $table_prefix;
        }
        if (isset($noConnectAbort)) {
            $this->noConnectAbort = $noConnectAbort;
        }

        $dsn = "mysql:host=$host;port=$port;dbname=$db_name;charset=$encoding";
        $dt = new \DateTime();
        $offset = $dt->format("P");
        $opt = array(
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '{$offset}'"
        );

        $this->pdo = new \PDO($dsn, $user, $pass, $opt);
        $error = $this->pdo->errorInfo();
        if ((int) $error[0]) {
            if ($this->noConnectAbort) {
                return false;
            } else {
                INJI_SYSTEM_ERROR($error[2], true);
            }
        } else {
            $this->connect = true;
            $query = new Mysql\Query($this);
            $query->query("SET SQL_BIG_SELECTS=1");
            $query->query("SET SESSION sql_mode = 'NO_ENGINE_SUBSTITUTION'");
            return true;
        }
    }

    public function getTableCols($table_name) {
        $query = new Mysql\Query($this);
        return $query->query("SHOW COLUMNS FROM `{$this->db_name}`.{$this->table_prefix}{$table_name}")->getArray('Field');
    }

    public function tableExist($tableName) {
        $query = new Mysql\Query($this);
        return (bool) $query->query("SHOW TABLES FROM `{$this->db_name}` LIKE '{$this->table_prefix}{$tableName}'")->getArray();
    }

    public function addCol($table = false, $name = false, $param = 'TEXT NOT NULL') {
        if (!$table || !$name) {
            return false;
        }
        if ($param == 'pk') {
            $param = "int(11) NOT NULL AUTO_INCREMENT, PRIMARY KEY (`{$name}`)";
        }
        $query = new Mysql\Query($this);
        return $query->query("ALTER TABLE `{$this->db_name}`.`{$this->table_prefix}{$table}` ADD `{$name}` {$param}");
    }

    public function delCol($table = false, $name = false) {
        if (!$table || !$name) {
            return false;
        }
        $query = new Mysql\Query($this);
        return $query->query("ALTER TABLE `{$this->db_name}`.`{$this->table_prefix}{$table}` DROP `{$name}`");
    }

    public function getTables() {
        $query = new Mysql\Query($this);
        $data = $query->query("SHOW TABLES")->getArray();
        $tables = [];
        foreach ($data as $info) {
            $tables[] = $info['Tables_in_' . $this->db_name];
        }
        return $tables;
    }

    public function deleteTable($tableName) {
        if (!$tableName) {
            return true;
        }
        $query = new Mysql\Query($this);
        return $query->query("DROP TABLE IF EXISTS {$tableName}")->fetch();
    }
}