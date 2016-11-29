<?php

/**
 * Folder
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Files;

class Folder extends \Model {

    public static $cols = [
        'dir' => ['type' => 'text'],
        'name' => ['type' => 'text'],
        'alias' => ['type' => 'text'],
        'public' => ['type' => 'bool'],
        'date_create' => ['type' => 'dateTime'],
    ];

    public function beforeDelete() {
        foreach ($this->files as $file) {
            $file->delete();
        }
    }

    public static function relations() {
        return [
            'files' => [
                'type' => 'many',
                'model' => 'Files\File',
                'col' => 'folder_id'
            ]
        ];
    }

}
