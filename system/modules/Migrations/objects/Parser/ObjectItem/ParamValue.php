<?php

/**
 * Parser Object Value
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Migrations\Parser\ObjectItem;

class ParamValue extends \Migrations\Parser {

    public function parse() {
        $options = $this->param->options ? json_decode($this->param->options, true) : [];
        $modelName = get_class($this->model);
        $cols = $modelName::$cols;
        $value = $this->data;
        if (is_array($value)) {
            $value = '';
        }
        $paramValue = \Ecommerce\Item\Param::get([[['item_id', $this->model->id], ['item_option_id', $this->param->value]]]);
        if (!$paramValue) {
            $paramValue = new \Ecommerce\Item\Param(['item_id' => $this->model->id, 'item_option_id' => $this->param->value]);
        }
        $paramValue->value = $value;
        $paramValue->save();
    }
}
