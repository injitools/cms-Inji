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

class Images extends \Migrations\Parser {

    public function parse() {
        if (is_null($this->data)) {
            return;
        }
        $value = $this->data;
        if (!is_array($value)) {
            $value = [$value];
        }
        $ids = [];
        $dir = pathinfo($this->object->walker->migtarionLog->source, PATHINFO_DIRNAME);
        $this->model->image_file_id = 0;
        foreach ($value as $key => $imagePath) {
            if (!$imagePath || !file_exists($dir . '/' . $imagePath)) {
                continue;
            }
            $notEq = true;
            $md5Cur = md5_file($dir . '/' . $imagePath);
            foreach ($this->model->images as $imageId => $image) {
                if (!$image->file) {
                    $image->delete();
                    continue;
                }
                $file = $image->file;
                $md5File = '';
                if ($file->md5) {
                    $md5File = $file->md5;
                } elseif (file_exists($file->getRealPath())) {
                    $md5File = $file->md5 = md5_file($file->getRealPath());
                    $file->save();
                }

                if ($file && file_exists($file->getRealPath()) && $md5Cur == $md5File) {
                    $notEq = false;
                    $ids[] = $imageId;
                    break;
                }
            }
            if ($notEq) {
                $file_id = \App::$primary->files->uploadFromUrl($dir . '/' . $imagePath, ['accept_group' => 'image', 'upload_code' => 'MigrationUpload']);
                if ($file_id) {
                    $image = new \Ecommerce\Item\Image([
                        'item_id' => $this->model->pk(),
                        'file_id' => $file_id
                    ]);
                    $image->save();
                    $ids[] = $image->id;
                }
            } else {
                $image->weight = $key;
            }
            if (isset($image) && !$this->model->image_file_id) {
                $this->model->image_file_id = $image->file_id;
            }
        }
        foreach ($this->model->images as $imageId => $image) {
            if (!in_array($imageId, $ids)) {
                $image->delete();
            }
        }
    }

}
