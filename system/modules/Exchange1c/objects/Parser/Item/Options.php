<?php

/**
 * Item images parser
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Exchange1c\Parser\Item;

class Options extends \Migrations\Parser {

    static $options;

    public function parse() {
        if (!Options::$options) {
            Options::$options = \Ecommerce\Item\Option::getList();
        }
        $options = [];
        $modelName = 'Ecommerce\Item\Option';
        foreach ($this->data['ЗначенияСвойства'] as $opt) {
            $optionId = \App::$cur->migrations->ids['parseIds']['Ecommerce\Item\Option'][$opt['Ид']]->object_id;
            if ($optionId && !isset(Options::$options[$optionId])) {
                Options::$options = \Ecommerce\Item\Option::getList();
            }
            if (isset(Options::$options[$optionId]) && Options::$options[$optionId]->type == 'select') {
                if (empty($options[$optionId])) {
                    $options[$optionId] = [];
                } else {
                    if (!Options::$options[$optionId]->advance) {
                        Options::$options[$optionId]->advance = ['multi' => true];
                        Options::$options[$optionId]->save();
                    }
                }
                if ($opt['Значение'] && isset(\App::$cur->migrations->ids['parseIds']['Ecommerce\Item\Option\Item'][$opt['Значение']])) {
                    $options[$optionId][] = \App::$cur->migrations->ids['parseIds']['Ecommerce\Item\Option\Item'][$opt['Значение']]->object_id;
                }
            } else {
                $options[$optionId] = $opt['Значение'];
            }
        }
        $itemParams = \Ecommerce\Item\Param::getList(['where' => ['item_id', $this->model->id]]);
        foreach ($itemParams as $itemParam) {
            if ($itemParam->item_option_id && !isset(Options::$options[$itemParam->item_option_id])) {
                Options::$options = \Ecommerce\Item\Option::getList();
            }
            if (isset(Options::$options[$itemParam->item_option_id]) && Options::$options[$itemParam->item_option_id]->type == 'select') {
                if (empty($options[$itemParam->item_option_id]) || !in_array($itemParam->value, $options[$itemParam->item_option_id])) {
                    $itemParam->delete();
                } else {
                    unset($options[$itemParam->item_option_id][array_search($itemParam->value, $options[$itemParam->item_option_id])]);
                }
            } else {
                if (empty($options[$itemParam->item_option_id])) {
                    $itemParam->delete();
                } else {
                    $itemParam->value = $options[$itemParam->item_option_id];
                    $itemParam->save();
                    unset($options[$itemParam->item_option_id]);
                }
            }
        }
        foreach ($options as $optionId => $values) {
            if (is_array($values)) {
                foreach ($values as $value) {
                    $itemParam = new \Ecommerce\Item\Param([
                        'item_option_id' => $optionId,
                        'item_id' => $this->model->id,
                        'value' => $value
                    ]);
                    $itemParam->save();
                }
            } else {
                $itemParam = new \Ecommerce\Item\Param([
                    'item_option_id' => $optionId,
                    'item_id' => $this->model->id,
                    'value' => $values
                ]);
                $itemParam->save();
            }
        }
    }

}
