<?php

namespace Inji\Db;

use Inji\Config;

class InjiStorage {
    public $connect = true;
    /**
     * @var \Inji\Db
     */
    public $dbInstance;

    public function addItem($file, $values, $options) {
        $path = $this->getStoragePath($file, $options);
        $storage = Config::custom($path);
        if (empty($storage['schema']['autoincrement'])) {
            $storage['schema']['autoincrement'] = 0;
        }
        $storage['schema']['autoincrement']++;
        $values['id'] = $storage['schema']['autoincrement'];
        $storage['items'][$storage['schema']['autoincrement']] = $values;
        Config::save($path, $storage);
        return $storage['schema']['autoincrement'];
    }

    public function deleteItems($file, $where, $options) {
        $items = $this->getItems($file, $where, $options);
        if ($items) {
            $path = $this->getStoragePath($file, $options);
            $storage = Config::custom($path);
            foreach ($items as $key => $item) {
                unset($storage['items'][$key]);
            }
            Config::save($path, $storage);
        }
        return count($items);
    }

    public function updateItems($file, $where, $values, $options) {
        $items = $this->getItems($file, $where, $options);
        if ($items) {
            foreach ($items as &$item) {
                $item = array_replace_recursive($item, $values);
            }
            $path = $this->getStoragePath($file, $options);
            $storage = Config::custom($path);

            $storage['items'] = array_replace_recursive($storage['items'], $items);
            Config::save($path, $storage);
        }
        return count($items);
    }

    public function getItems($file, $where, $options) {
        $path = $this->getStoragePath($file, $options);
        $storage = Config::custom($path);
        if (empty($storage['items'])) {
            return [];
        }
        return $this->filterItems($storage['items'], $where);
    }

    public function filterItems($items, $where) {
        foreach ($items as $key => $item) {
            if (!$this->checkWhere($item, $where)) {
                unset($items[$key]);
            }
        }
        return $items;
    }

    public function checkWhere($item = [], $where = '', $value = '', $operation = '=', $concatenation = 'AND') {
        if (is_array($where)) {
            if (is_array($where[0])) {
                $result = true;
                foreach ($where as $key => $whereItem) {
                    $concatenation = empty($whereItem[3]) ? 'AND' : strtoupper($whereItem[3]);
                    switch ($concatenation) {
                        case 'AND':
                            $result = $result && call_user_func_array([$this, 'checkWhere'], [$item, $whereItem]);
                            break;
                        case 'OR':
                            $result = $result || call_user_func_array([$this, 'checkWhere'], [$item, $whereItem]);
                            break;
                    }
                }

                return $result;
            } else {
                return call_user_func_array([$this, 'checkWhere'], array_merge([$item], $where));
            }
        }
        if (!isset($item[$where]) && !$value) {
            return true;
        }
        if (!isset($item[$where]) && $value) {
            return false;
        }
        if ($item[$where] == $value) {
            return true;
        }
        return false;
    }

    public function getStoragePath($file, $options) {
        $root = !empty($options['share']) ? INJI_PROGRAM_DIR : $this->dbInstance->app->path;
        return $root . '/InjiStorage/' . $file . '.php';
    }
}