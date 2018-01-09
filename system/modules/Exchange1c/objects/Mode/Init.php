<?php

/**
 * Mode Init
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */

namespace Exchange1c\Mode;

use Exchange1c\Exchange;

class Init extends \Exchange1c\Mode {

    public function process() {
        if ($this->log->type == 'catalog') {
            echo "zip=yes\n";
        } elseif ($this->log->type == 'sale') {
            echo "zip=no\n";
        }
        echo 'file_limit=' . \Inji\Tools::toBytes(ini_get('upload_max_filesize')) . "\n";
        if (!empty($_GET["version"])) {
            echo "sessid=" . $this->log->exchange->session . "\n";
            echo "version=2.08";
        }
        $this->end();

        //clean files
        Exchange::get(0, 'cleared');
        if (!empty(\App::$cur->exchange1c->config['maxSaveFilesInterval'])) {
            $query = \App::$cur->db->newQuery();
            $query->operation = 'select';
            $query->table = \Exchange1c\Exchange::table();
            $query->cols = \Exchange1c\Exchange::index() . ',' . \Exchange1c\Exchange::colPrefix() . 'path';
            $queryArr = $query->buildQuery();
            $queryArr['query'] .= ' where `' . \Exchange1c\Exchange::colPrefix() . 'cleared` = 0 AND  `' . \Exchange1c\Exchange::colPrefix() . 'date_create` < NOW() - INTERVAL ' . \App::$cur->exchange1c->config['maxSaveFilesInterval'];

            $exc = $query->query($queryArr)->getArray();
            foreach ($exc as $exchangeArr) {
                \Inji\Tools::delDir($exchangeArr[\Exchange1c\Exchange::colPrefix() . 'path']);
                $query = \App::$cur->db->newQuery();
                $query->where([\Exchange1c\Exchange::index(), $exchangeArr[\Exchange1c\Exchange::index()]]);
                $query->update(\Exchange1c\Exchange::table(), [\Exchange1c\Exchange::colPrefix() . 'cleared' => 1]);
            }
        }
    }

}
