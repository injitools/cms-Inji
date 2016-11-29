<?php

/**
 * Parser Object ObjectLink
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Migrations\Parser\Object;

class ObjectLink extends \Migrations\Parser {

    public function parse() {
        $object = \App::$cur->migrations->getMigrationObject($this->param->value);
        $objectId = \App::$cur->migrations->findObject((string) $this->data, $object->model);
        $modelName = $object->model;
        $model = $modelName::get($objectId->object_id);
        $this->model->{$model->index()} = $model->pk();
    }

}
