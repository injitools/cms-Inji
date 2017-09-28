<?php
/**
 * INJI
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2017 Alexey Krupskiy
 * @license https://github.com/injitools/Inji/blob/master/LICENSE
 */
return [
    'find' => function ($search, $params) {
        if (empty($params['fieldId']) || !is_string($params['fieldId']) || !$field = \Ecommerce\Delivery\Field::get($params['fieldId'])) {
            return [];
        }
        $suggess = [];
        foreach ($field->fieldItems(['where' => ['value', $search . '%', 'LIKE'], 'limit' => 10]) as $item) {
            $suggess[$item->id] = $item->value;
        };
        return $suggess;
    },
    'getValueText' => function ($value, $params) {
        $value = \Ecommerce\Delivery\Field\Item::get($value);
        if ($value) {
            return $value->value;
        }
        return '';
    }
];