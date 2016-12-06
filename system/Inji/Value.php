<?php

/**
 * Value
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Value {

    public $valueKey = '';
    public $model = null;
    public $type = 'string';

    public function __construct($model, $key) {
        $this->model = $model;
        $this->valueKey = $key;
    }

    public function forView($options = []) {
        $modelName = get_class($this->model);
        $colInfo = $modelName::getColInfo($this->valueKey);
        $type = !empty($colInfo['colParams']['type']) ? $colInfo['colParams']['type'] : 'string';
        switch ($type) {
            case 'dateTime':
            //fall
            case 'date':
                $yy = (int) substr($this->model->{$this->valueKey}, 0, 4);
                $mm = (int) substr($this->model->{$this->valueKey}, 5, 2);
                $dd = (int) substr($this->model->{$this->valueKey}, 8, 2);

                $hours = substr($this->model->{$this->valueKey}, 11, 5);

                $month = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
                $yearPosrfix = isset($options['yearPostfix']) ? $options['yearPostfix'] : " г.";
                return ($dd > 0 ? $dd . " " : '') . $month[$mm - 1] . " " . $yy . $yearPosrfix . (empty($options['notime']) ? " " . $hours : '');
            case 'select':
                switch ($colInfo['colParams']['source']) {
                    case 'model':
                        $sourceValue = false;
                        if ($this->model->{$this->valueKey}) {
                            $sourceValue = $colInfo['colParams']['model']::get($this->model->{$this->valueKey});
                        }
                        return $sourceValue ? $sourceValue->name() : 'Не задано';
                    case 'array':
                        return !empty($colInfo['colParams']['sourceArray'][$this->model->{$this->valueKey}]) ? $colInfo['colParams']['sourceArray'][$this->model->{$this->valueKey}] : 'Не задано';
                    case 'method':
                        if (!empty($colInfo['colParams']['params'])) {
                            $values = call_user_func_array([App::$cur->$colInfo['colParams']['module'], $colInfo['colParams']['method']], $colInfo['colParams']['params']);
                        } else {
                            $values = $colInfo['colParams']['module']->$colInfo['colParams']['method']();
                        }
                        return !empty($values[$this->model->{$this->valueKey}]) ? $values[$this->model->{$this->valueKey}] : 'Не задано';
                    case 'relation':
                        $relations = $colInfo['modelName']::relations();
                        $relValue = $relations[$colInfo['colParams']['relation']]['model']::get($this->model->{$this->valueKey});
                        return $relValue ? $relValue->name() : 'Не задано';
                }
                break;
            case 'image':
                $file = Files\File::get($this->model->{$this->valueKey});
                if ($file) {
                    return '<img src="' . $file->path . '?resize=60x120" />';
                }
                return '<img src="/static/system/images/no-image.png?resize=60x120" />';
            case 'bool':
                return $this->model->{$this->valueKey} ? 'Да' : 'Нет';
            default:
                return $this->model->{$this->valueKey};
        }
    }
}