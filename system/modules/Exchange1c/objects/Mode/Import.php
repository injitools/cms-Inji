<?php

/**
 * Item name
 *
 * Info
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Exchange1c\Mode;

class Import extends \Exchange1c\Mode
{
    function process()
    {
        \App::$cur->Migrations->startMigration(1, strpos($_GET['filename'], 'import') !== false ? 1 : 2, $this->exchange->path . '/' . $_GET['filename']);
        echo 'success';
        $this->end();
    }

}
