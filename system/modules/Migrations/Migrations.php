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
    public $usedIds = [];
    public $migrationObjects = [];

    public function startMigration($migrationId, $mapId, $filePath) {
        $log = new \Migrations\Log();
        $log->migration_id = $migrationId;
        $log->migration_map_id = $mapId;
        $log->source = $filePath;
        $log->save();

        $reader = new Migrations\Reader\Xml();
        App::$cur->log->forceView(true);
        $logKey = App::$cur->log->start('load xml');
        if (!$reader->loadData($filePath)) {
            $event = new Migrations\Log\Event();
            $event->log_id = $log->id;
            $event->type = 'load_data_error';
            $event->save();
            return false;
        }
        App::$cur->log->end($logKey);
        $walker = new \Migrations\Walker();
        $walker->migration = \Migrations\Migration::get($migrationId);
        $walker->map = \Migrations\Migration\Map::get($mapId);
        $logKey = App::$cur->log->start('parse xml');
        $walker->data = $reader->getArray();
        App::$cur->log->end($logKey);
        $walker->migtarionLog = $log;
        $walker->walk();
        $log->result = 'success';
        $log->save();
        $this->saveLastAccess();
    }

    public function loadParseIds($type) {
        $this->ids['parseIds'][$type] = \Migrations\Id::getList(['where' => ['type', $type], 'key' => 'parse_id', 'array' => true]);
    }

    public function loadObjectIds($type) {
        $this->ids['objectIds'][$type] = \Migrations\Id::getList(['where' => ['type', $type], 'key' => 'object_id', 'array' => true]);
    }

    public function findObject($parseId, $type) {
        if (empty($this->ids['parseIds'][$type])) {
            $this->loadParseIds($type);
            ksort($this->ids['parseIds'][$type]);
        }
        if (!empty($this->ids['parseIds'][$type][$parseId])) {
            $this->usedIds['parseIds'][$type][$parseId] = new \Migrations\Id($this->ids['parseIds'][$type][$parseId]);
            $this->usedIds['parseIds'][$type][$parseId]->last_access = Date('Y-m-d H:i:s');
            return $this->usedIds['parseIds'][$type][$parseId];
        }
    }

    public function findParse($objectId, $type) {
        if (empty($this->ids['objectIds'][$type])) {
            $this->loadObjectIds($type);
            ksort($this->ids['objectIds'][$type]);
        }
        if (!empty($this->ids['objectIds'][$type][$objectId])) {
            $this->usedIds['objectIds'][$type][$objectId] = new \Migrations\Id($this->ids['objectIds'][$type][$objectId]);
            $this->usedIds['objectIds'][$type][$objectId]->last_access = Date('Y-m-d H:i:s');
            return $this->usedIds['objectIds'][$type][$objectId];
        }
    }

    /**
     * @param \Migrations\Migration $migration
     * @param int|string $objectId
     * @param null|string $col
     * @return null|\Migrations\Migration\Object
     */
    public function getMigrationObject($migration, $objectId, $col = null) {
        if ($col === null && isset($migration->objects[$objectId])) {
            return $migration->objects[$objectId];
        }
        if ($col !== null && isset($migration->objects(['key' => $col])[$objectId])) {
            return $migration->objects(['key' => $col])[$objectId];
        }
        return null;
    }

    public function saveLastAccess() {
        foreach ($this->usedIds as $group => $types) {
            foreach ($types as $type => $ids) {
                foreach ($ids as $key => $id) {
                    if ($id->_changedParams) {
                        $id->save();
                    }
                }
            }
        }
    }

}
