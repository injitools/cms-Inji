<?php

/**
 * Exchange File
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Exchange1c\Exchange;
/**
 * Class File
 * @property int $id
 * @property string $name
 * @property int $exchange_id
 * @property int $size
 * @property string $status
 * @property bool $deleted
 * @property int $date_create
 * @property \Exchange1c\Exchange $exchange
 */
class File extends \Model {

    public static $logging = false;
    public static $cols = [
        'name' => ['type' => 'text'],
        'exchange_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'exchange'],
        'size' => ['type' => 'number'],
        'status' => ['type' => 'text'],
        'deleted' => ['type' => 'bool'],
        'date_create' => ['type' => 'dateTime'],
    ];
    public static $dataManagers = [
        'manager' => [
            'cols' => [
                'name', 'size', 'status', 'deleted', 'date_create'
            ],
        ]
    ];

    public function deleteFile() {
        if ($this->exchange && file_exists($this->exchange->path . '/' . $this->name)) {
            unlink($this->exchange->path . '/' . $this->name);
        }
        $this->deleted = 1;
        $this->save();
    }

    public static function relations() {
        return [
            'exchange' => [
                'model' => 'Exchange1c\Exchange',
                'col' => 'exchange_id'
            ]
        ];
    }
}