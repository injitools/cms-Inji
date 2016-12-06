<?php

/**
 * Parser Object Value
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Migrations\Parser\Object;

class Value extends \Migrations\Parser {

    public function parse() {
        $options = $this->param->options ? json_decode($this->param->options, true) : [];
        $modelName = get_class($this->model);
        $cols = $modelName::$cols;
        $value = $this->data;
        if (is_array($value)) {
            $value = '';
        }
        if (!empty($cols[$this->param->value])) {
            $col = $cols[$this->param->value];
            if ($col['type'] == 'dynamicType') {
                switch ($col['typeSource']) {
                    case 'selfMethod':
                        $type = $this->model->{$col['selfMethod']}();
                        if (is_array($type)) {
                            if (strpos($type['relation'], ':')) {
                                $relationPath = explode(':', $type['relation']);
                                $relationName = array_pop($relationPath);
                                $item = $this->model;
                                foreach ($relationPath as $path) {
                                    $item = $item->$path;
                                }
                                $itemModel = get_class($item);
                                $relation = $itemModel::getRelation($relationName);
                                $sourceModel = $relation['model'];
                            } else {
                                $relation = $modelName::getRelation($type['relation']);
                                $sourceModel = $relation['model'];
                            }
                            $objectId = \App::$cur->migrations->findObject((string) $value, $sourceModel);
                            if ($objectId) {
                                $value = $objectId->object_id;
                            }
                        }
                        break;
                }
            } else {
                $type = $col['type'];
            }
        } else {
            $type = 'text';
        }

        if (!empty($options['valueReplace'])) {
            $values = $this->param->values(['key' => 'original']);
            if (empty($values[$value])) {
                $valueObject = new \Migrations\Migration\Object\Param\Value();
                $valueObject->param_id = $this->param->id;
                $valueObject->original = $value;
                $valueObject->save();
            } else {
                $valueObject = $values[$value];
            }
            $value = $valueObject->replace;
        }
        switch ($type) {
            case 'image':
                $notEq = true;
                $dir = pathinfo($this->object->walker->migtarionLog->source, PATHINFO_DIRNAME);
                if ($this->model->{$this->param->value}) {
                    $file = \Files\File::get($this->model->{$this->param->value});
                    if ($file && $value && file_exists($dir . '/' . $value) && file_exists(\App::$primary->path . $file->path) && md5_file($dir . '/' . $value) == md5_file(\App::$primary->path . $file->path)) {
                        $notEq = false;
                    }
                    if ($file && $notEq) {
                        $file->delete();
                        $this->model->{$this->param->value} = 0;
                    }
                }
                if ($notEq) {
                    $this->model->{$this->param->value} = \App::$primary->files->uploadFromUrl($dir . '/' . $value, ['accept_group' => 'image', 'upload_code' => 'MigrationUpload']);
                }
                break;
            default:
                if (is_array($value)) {
                    $value = implode(' ', $value);
                }
                $this->model->{$this->param->value} = $value;
        }
    }

}
