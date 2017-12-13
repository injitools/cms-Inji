<?php

/**
 * Mode Import
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Exchange1c\Mode;

class Import extends \Exchange1c\Mode {

    public function process() {
        $fileName = $_GET['filename'];
        $path = $this->exchange->path;
        \App::$cur->daemon->task(function () use ($fileName, $path) {
            \App::$cur->Migrations->startMigration(1, strpos($fileName, 'import') !== false ? 1 : 2, $path . '/' . $fileName);
        });
        echo 'success';
        $this->end();

    }

}
