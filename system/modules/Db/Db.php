<?php

class Db extends Module {

    public $connection = null;
    public $connect = false;
    public $dbConfig = [];
    public $curQuery = null;
    public $className = '';
    public $QueryClassName = '';
    public $ResultClassName = '';
    public $migrationsVersions = [];

    public function init($param = null) {
        if (!$param) {
            $param = isset($this->config['default']) ? $this->config['default'] : 'local';
        }
        if (!is_array($param)) {
            if (!($dbOption = Db\Options::get($param, 'connect_alias', ['array' => true]))) {
                return false;
            }

            $db = $dbOption;
        } else {
            $db = $param;
        }
        $className = 'Db\\' . $db['driver'];
        $this->connection = new $className();
        $this->connection->init($db);
        $this->connection->dbInstance = $this;
        $this->connect = $this->connection->connect;
        $this->dbConfig = $db;

        $this->className = 'Db\\' . $this->dbConfig['driver'];
        $this->QueryClassName = 'Db\\' . $this->dbConfig['driver'] . '\\Query';
        $this->ResultClassName = 'Db\\' . $this->dbConfig['driver'] . '\\Result';
    }

    public function loadMigrationsVersion($code) {
        if (!$this->connect) {
            return false;
        }
        $version = Db\Migration::getList(['where' => ['code', $code], 'order' => ['date_create', 'desc'], 'limit' => 1, 'key' => false]);
        if ($version) {
            $this->migrationsVersions[$code] = $version[0]->version;
        }
        return true;
    }

    public function compareMigrations($code, $migrations) {
        if (!isset($this->migrationsVersions[$code])) {
            $this->loadMigrationsVersion($code);
        }
        if (!isset($this->migrationsVersions[$code]) || !isset($migrations[$this->migrationsVersions[$code]])) {
            return $migrations;
        }
        $startVersion = $this->migrationsVersions[$code];
        end($migrations);
        if ($startVersion == key($migrations)) {
            return [];
        }
        $pos = 0;
        foreach ($migrations as $migrationVersion => $migration) {
            if ($startVersion == $migrationVersion) {
                return array_slice($migration, $pos, null, true);
            }
            $pos++;
        }
        return [];
    }

    public function makeMigration($code, $version, $migration) {
        if (!isset($migration['up'])) {
            return false;
        }
        if (is_callable($migration['up'])) {
            $migration['up']();
        }
        $this->migrationsVersions[$code] = $version;
        $migrationVersion = new Db\Migration(['code' => $code, 'version' => $version]);
        $migrationVersion->save();
        return true;
    }

    public function __call($name, $params) {
        if (!is_object($this->connection)) {
            return false;
        }
        if (method_exists($this->className, $name)) {
            return call_user_func_array(array($this->connection, $name), $params);
        }
        if (method_exists($this->QueryClassName, $name)) {
            if (!is_object($this->curQuery)) {
                $this->curQuery = new $this->QueryClassName($this->connection);
            }
            return call_user_func_array(array($this->curQuery, $name), $params);
        }

        return false;
    }

    public function newQuery() {
        if ($this->QueryClassName) {
            return new $this->QueryClassName($this->connection);
        }
        return false;
    }

    public function __get($name) {
        if (isset($this->connection->$name)) {
            return $this->connection->$name;
        }
        if (!is_object($this->curQuery)) {
            $this->curQuery = $this->newQuery();
        }
        if (isset($this->curQuery->$name)) {
            return $this->curQuery->$name;
        }
    }

    public function __set($name, $value) {
        if (isset($this->connection->$name)) {
            return $this->connection->$name = $value;
        }
        if (!is_object($this->curQuery)) {
            $this->curQuery = $this->newQuery();
        }
        if (isset($this->curQuery->$name)) {
            return $this->curQuery->$name = $value;
        }
    }
}