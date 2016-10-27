<?php

/**
 * Data tree walker
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Migrations;

class Walker {

  public $migration = null;
  public $map = null;
  public $data = null;
  public $mapPath = null;
  public $mapPathParent = null;
  public $curPath = '/';
  public $migtarionLog = null;

  //walk map pathes on cur path
  public function walk() {
    $walked = [];
    //walk know pathes
    foreach ($this->map->paths(['where' => ['path', $this->curPath]]) as $path) {
      if (isset($this->data[$path->item])) {
        if ($path->type == 'container') { //create walker for container
          $walker = new Walker();
          $walker->migration = $this->migration;
          $walker->map = $this->map;
          $walker->data = &$this->data[$path->item];
          $walker->curPath = $this->curPath . $path->item . '/';
          $walker->mapPath = $path;
          $walker->mapPathParent = $this->mapPath;
          $walker->migtarionLog = $this->migtarionLog;
          $walker->walk();
        } elseif ($path->type == 'object') { //start parse path data
          $this->startObjectParse($path->object_id, $this->data[$path->item]);
        }
      }
      $walked[$path->item] = true;
    }
    //check unparsed paths
    foreach ($this->data as $key => &$data) {
      //skip parsed and attribtes
      if ($key == '@attributes' || !empty($walked[$key])) {
        continue;
      }
      //search object for parse
      $object = Migration\Object::get([
                  ['code', $key],
                  ['migration_id', $this->migration->id]
      ]);
      if ($object) { //parse as object
        $this->startObjectParse($object, $data);
      } else { //create new map path for configure unknown path
        $this->mapPath = new Migration\Map\Path();
        $this->mapPath->parent_id = $this->mapPathParent ? $this->mapPathParent->id : 0;
        $this->mapPath->path = $this->curPath;
        $this->mapPath->item = $key;
        $this->mapPath->migration_map_id = $this->map->id;
        $this->mapPath->save();
      }
    }
  }

  private function startObjectParse($object_id, &$data) {
    $objectParser = new Parser\Object();
    $objectParser->object = is_object($object_id) ? $object_id : \App::$cur->migrations->getMigrationObject($object_id);
    $objectParser->data = $data;
    $objectParser->walker = $this;
    $ids = $objectParser->parse();

    if ($objectParser->object->clear && json_decode($objectParser->object->clear, true)) {

      $where = json_decode($objectParser->object->clear, true);
      if (!$where) {
        $where = [];
      } else {
        $where = [[$where]];
      }
      if ($ids) {
        $where[] = ['id', implode(',', $ids), 'NOT IN'];
      }
      if (empty(\App::$cur->migrations->ids['objectIds'])) {
        \App::$cur->migrations->loadObjectIds($objectParser->object->model);
      }
      if (!empty(\App::$cur->migrations->ids['objectIds'][$objectParser->object->model])) {
        $where[] = ['id', implode(',', array_keys(\App::$cur->migrations->ids['objectIds'][$objectParser->object->model])), 'IN'];
      }
      $modelName = $objectParser->object->model;
      $objects = $modelName::getList(['where' => $where]);
      foreach ($objects as $object) {
        $objectId = \App::$cur->migrations->findParse($object->id, $objectParser->object->model);
        if ($objectId) {
          $objectId->delete();
          unset(\App::$cur->migrations->ids['objectIds'][$objectParser->object->model][$object->id]);
          unset(\App::$cur->migrations->ids['parseIds'][$objectParser->object->model][$objectId->parse_id]);
        }
        $object->delete();
      }
    }
  }

}
