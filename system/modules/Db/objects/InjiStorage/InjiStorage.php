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
        $items = $this->getItems($file, [], $where, $options);
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
        $items = $this->getItems($file, [], $where, $options);
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

    public function getItems($file, $cols, $where, $options) {
        $path = $this->getStoragePath($file, $options);
        $storage = Config::custom($path);
        if (empty($storage['items'])) {
            return [];
        }
        return $this->filterItems($storage['items'], $cols, $where);
    }

    public function filterItems($items, $cols, $where) {
        $colsMap = [];
        $count = false;
        if ($cols) {
            foreach ($cols as $col) {
                if (($asIndex = stripos($col, ' as ')) !== false) {
                    $origCol = substr($col, 0, $asIndex);
                    $newCol = substr($col, $asIndex + 4);
                } else {
                    $newCol = $origCol = $col;
                }
                preg_match('!count\((.*)\)!i', $origCol, $match);
                if (!empty($match[1])) {
                    $count = ['counted' => $match[1], 'as' => $newCol, 'result' => 0];
                }
            }
        }
        foreach ($items as $key => $item) {
            if (!$this->checkWhere($item, $where)) {
                unset($items[$key]);
                continue;
            }
            if ($count) {
                $count['result']++;
            }
        }
        if ($count) {
            return [[$count['as'] => $count['result']]];
        }
        return $items;
    }

    public function checkWhere($item = [], $where = '', $value = '', $operation = '=', $concatenation = 'AND') {
        if (!$where) {
            return true;
        }
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