<?php

/**
 * Migration map editor
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class MapController extends Controller {

    public function indexAction() {
        $this->view->setTitle('Карты миграции данных');

        if (!empty($_POST['type'])) {
            foreach ($_POST['type'] as $pathId => $objectType) {
                $mapPath = Migrations\Migration\Map\Path::get($pathId);
                if (is_numeric($objectType)) {
                    $mapPath->object_id = $objectType;
                    $mapPath->type = 'object';
                } else {
                    if ($objectType == 'object') {
                        $object = new Migrations\Migration\Object();
                        $object->model = !empty($_POST['typeOptions'][$pathId]) ? $_POST['typeOptions'][$pathId] : '';
                        $object->migration_id = $mapPath->map->migration_id;
                        $object->code = $object->name = $mapPath->item;
                        $object->save();
                        $mapPath->type = 'object';
                        $mapPath->object_id = $object->id;
                    } else {
                        $mapPath->type = $objectType;
                    }
                }

                $mapPath->save();
            }
        }
        if (!empty($_POST['param'])) {
            foreach ($_POST['param'] as $paramId => $type) {
                $param = \Migrations\Migration\Object\Param::get($paramId);

                if ($type == 'newObject') {
                    $object = new Migrations\Migration\Object();
                    $object->model = !empty($_POST['paramOptions'][$paramId]) ? $_POST['paramOptions'][$paramId] : '';
                    $object->migration_id = $param->object->migration_id;
                    $object->code = $object->name = $param->code;
                    $object->save();
                    $param->type = 'object';
                    $param->value = $object->id;
                } else {
                    $param->type = $type;
                    $param->value = !empty($_POST['paramOptions'][$paramId]) ? $_POST['paramOptions'][$paramId] : '';
                }
                $param->save();
            }
        }

        $models = $this->modules->getSelectListModels();
        $map = \Migrations\Migration\Map::get($_GET['item_pk']);
        $objects = $map->migration->objects(['forSelect' => true]);
        $this->view->page(['data' => compact('map', 'models', 'objects')]);
    }

}
