<?php

/**
 * Data Migrations class
 *
 * Migration from file, to file, from web, to web
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

//define('mdebug',true);
class Migrations extends \Module {

    public $ids = [];
    public $migrationObjects = [];

    public function startMigration($migrationId, $mapId, $filePath) {
        $log = new \Migrations\Log();
        $log->migration_id = $migrationId;
        $log->migration_map_id = $mapId;
        $log->source = $filePath;
        $log->save();

        $reader = new Migrations\Reader\Xml();
        if (!$reader->loadData($filePath)) {
            $event = new Migrations\Log\Event();
            $event->log_id = $log->id;
            $event->type = 'load_data_error';
            $event->save();
            return false;
        }
        $walker = new \Migrations\Walker();
        $walker->migration = Migrations\Migration::get($migrationId);
        $walker->map = Migrations\Migration\Map::get($mapId);
        $walker->data = $reader->getArray();
        $walker->migtarionLog = $log;
        $walker->walk();
        $log->result = 'success';
        $log->save();
        $this->saveLastAccess();
    }

    public function loadParseIds($type) {
        $this->ids['parseIds'][$type] = \Migrations\Id::getList(['where' => ['type', $type], 'key' => 'parse_id']);
    }

    public function loadObjectIds($type) {
        $this->ids['objectIds'][$type] = \Migrations\Id::getList(['where' => ['type', $type], 'key' => 'object_id']);
    }

    public function findObject($parseId, $type) {
        if (empty($this->ids['parseIds'][$type])) {
            $this->loadParseIds($type);
            ksort($this->ids['parseIds'][$type]);
        }
        if (!empty($this->ids['parseIds'][$type][$parseId])) {
            $this->ids['parseIds'][$type][$parseId]->last_access = Date('Y-m-d H:i:s');
            return $this->ids['parseIds'][$type][$parseId];
        }
    }

    public function findParse($objectId, $type) {
        if (empty($this->ids['objectIds'][$type])) {
            $this->loadObjectIds($type);
            ksort($this->ids['objectIds'][$type]);
        }
        if (!empty($this->ids['objectIds'][$type][$objectId])) {
            $this->ids['objectIds'][$type][$objectId]->last_access = Date('Y-m-d H:i:s');
            return $this->ids['objectIds'][$type][$objectId];
        }
    }

    public function getMigrationObject($objectId) {
        if (empty($this->migrationObjects)) {
            $this->migrationObjects = \Migrations\Migration\Object::getList();
        }
        if (!empty($this->migrationObjects[$objectId])) {
            return $this->migrationObjects[$objectId];
        }
    }

    public function saveLastAccess() {
        foreach ($this->ids as $type => $ids) {
            foreach ($ids as $id) {
                if ($id->_changedParams) {
                    $id->save();
                }
            }
        }
    }

}
