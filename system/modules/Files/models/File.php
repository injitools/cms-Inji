<?php

/**
 * File
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Inji\Files;
/**
 * @property int $id
 * @property string $code
 * @property int $type_id
 * @property int $folder_id
 * @property string $upload_code
 * @property string $path
 * @property string $name
 * @property string $about
 * @property string $original_name
 * @property string $md5
 * @property string $date_create
 *
 * @property-read \Files\Type $type
 * @method  \Files\Type type($options)
 * @property-read \Files\Folder $folder
 * method  \Files\Folder folder($options)
 */
class File extends \Inji\Model {

    public static $cols = [
        'code' => ['type' => 'text'],
        'type_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'type'],
        'folder_id' => ['type' => 'select', 'source' => 'relation', 'relation' => 'foler'],
        'upload_code' => ['type' => 'text'],
        'path' => ['type' => 'textarea'],
        'name' => ['type' => 'text'],
        'about' => ['type' => 'html'],
        'original_name' => ['type' => 'text'],
        'md5' => ['type' => 'text'],
        'date_create' => ['type' => 'dateTime'],
    ];

    public function beforeSave() {
        $path = $this->getRealPath();
        if (!$this->md5 && $this->path && file_exists($path)) {
            $this->md5 = md5_file($path);
        }
    }

    public function beforeDelete() {
        $path = $this->getRealPath();
        if (file_exists($path)) {
            unlink($path);
        }
    }

    public function getRealPath() {
        $sitePath = \Inji\App::$primary->path;
        return "{$sitePath}{$this->path}";
    }

    public static function relations() {
        return [
            'type' => [
                'model' => 'Inji\Files\Type',
                'col' => 'type_id'
            ],
            'folder' => [
                'model' => 'Inji\Files\Folder',
                'col' => 'folder_id'
            ],
        ];
    }

}
