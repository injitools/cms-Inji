<?php

/**
 * Parser Object Relation
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Migrations\Parser\ObjectItem;

class Relation extends \Migrations\Parser {

    public function parse() {

        $options = $this->param->options ? json_decode($this->param->options, true) : [];
        $modelName = $this->object->object->model;
        $relation = $modelName::getRelation($this->param->value);
        $object = \App::$cur->migrations->getMigrationObject($this->walker->migration, $relation['model'], 'model');
        if (!$object) {
            $object = new \Migrations\Migration\Object([
                'model' => $relation['model'],
                'code' => $this->param->code,
                'name' => $this->param->code,
                'migration_id' => $this->object->object->migration_id
            ]);
            $object->save();
        }
        if (!empty($relation['type']) && $relation['type'] == 'many') {
            $ids = [];

            if ($this->data) {
                foreach ($this->data as $code => &$item) {
                    if (is_array($this->data) && !\Tools::isAssoc($this->data)) {
                        foreach ($this->data as &$item) {
                            $objectParser = new \Migrations\Parser\ObjectItem();
                            $objectParser->object = $object;
                            $objectParser->parentObject = $this->object;
                            $objectParser->parentModel = $this->model;
                            $objectParser->walker = $this->object->walker;
                            $objectParser->parentParam = $this;
                            $objectParser->data = &$item;
                            if (!$this->model->pk()) {
                                $this->model->save();
                            }
                            if (defined('mdebug')) {
                                echo " -> object " . $object->id;
                            }
                            $ids = array_merge($ids, $objectParser->parse([$relation['col'] => $this->model->pk()]));
                        }
                    } else {
                        $objectParser = new \Migrations\Parser\ObjectItem();
                        $objectParser->object = $object;
                        $objectParser->parentObject = $this->object;
                        $objectParser->parentModel = $this->model;
                        $objectParser->walker = $this->object->walker;
                        $objectParser->parentParam = $this;
                        $objectParser->data = &$item;
                        if (!$this->model->pk()) {
                            $this->model->save();
                        }
                        if (defined('mdebug')) {
                            echo " -> object " . $object->id;
                        }
                        $ids = array_merge($ids, $objectParser->parse([$relation['col'] => $this->model->pk()]));
                    }
                }
            }
            if (!empty($options['clearMissing']) && $this->model->pk()) {
                $where = [];
                $where[] = [$relation['col'], $this->model->pk()];
                if ($ids) {
                    $where[] = ['id', implode(',', $ids), 'NOT IN'];
                }
                $modelName = $relation['model'];
                $objects = $modelName::getList(['where' => $where]);
                foreach ($objects as $delObject) {
                    $objectId = \App::$cur->migrations->findParse($delObject->id, get_class($delObject));
                    if ($objectId) {
                        unset(\App::$cur->migrations->ids['objectIds'][get_class($delObject)][$delObject->id]);
                        unset(\App::$cur->migrations->ids['parseIds'][get_class($delObject)][$objectId->parse_id]);
                        $objectId->delete();
                    }
                    $delObject->delete();
                }
            }
        } else {
            $objectParser = new \Migrations\Parser\ObjectItem();
            $objectParser->object = $object;
            $objectParser->parentObject = $this->object;
            $objectParser->parentModel = $this->model;
            $objectParser->parentParam = $this;
            $objectParser->data = &$this->data;
            $ids = [];
            if (is_array($this->data) && !\Tools::isAssoc($this->data)) {
                foreach ($this->data as &$data) {
                    if (defined('mdebug')) {
                        echo " -> object " . $object->id;
                    }
                    $model = $objectParser->setModel($data);
                    if ($model && $model->id) {
                        $ids[] = $model->id;
                    }
                }
            } else {
                if (defined('mdebug')) {
                    echo " -> object " . $object->id;
                }
                $model = $objectParser->setModel($this->data);
                if ($model && $model->id) {
                    $ids[] = $model->id;
                }
            }
            if ($ids) {
                $this->model->{$relation['col']} = $ids[0];
            }
        }
    }

}
