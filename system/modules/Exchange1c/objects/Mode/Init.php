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

class Init extends \Exchange1c\Mode {

    public function process() {
        echo "zip=no\n";
        echo 'file_limit=' . \Tools::toBytes(ini_get('post_max_size'));
        $this->end();

        //clean files
        if (!empty(\App::$cur->exchange1c->config['maxSaveFilesInterval'])) {
            $query = \App::$cur->db->newQuery();
            $query->operation = 'select';
            $query->table = \Exchange1c\Exchange\File::table();
            $query->cols = \Exchange1c\Exchange\File::colPrefix() . 'id';
            $queryArr = $query->buildQuery();
            $queryArr['query'] .= ' where `' . \Exchange1c\Exchange\File::colPrefix() . 'deleted` = 0 AND  `' . \Exchange1c\Exchange\File::colPrefix() . 'date_create` < NOW() - INTERVAL ' . \App::$cur->exchange1c->config['maxSaveFilesInterval'];
            try {
                $ids = array_keys($query->query($queryArr)->getArray(\Exchange1c\Exchange\File::colPrefix() . 'id'));
            } catch (\PDOException $exc) {
                if ($exc->getCode() == '42S02') {
                    \Exchange1c\Exchange\File::createTable();
                } elseif ($exc->getCode() == '42S22') {
                    $cols = \Exchange1c\Exchange\File::cols();
                    foreach (\Exchange1c\Exchange\File::$cols as $colName => $params) {
                        if (!isset($cols[\Exchange1c\Exchange\File::colPrefix() . $colName])) {
                            \Exchange1c\Exchange\File::createCol($colName);
                        }
                    }
                }
                $ids = array_keys($query->query($queryArr)->getArray(\Exchange1c\Exchange\File::colPrefix() . 'id'));
            }
            foreach (array_chunk($ids, 500) as $idGroup) {
                $dfiles = \Exchange1c\Exchange\File::getList(['where' => ['id', $idGroup, 'IN']]);
                foreach ($dfiles as $dfile) {
                    $dfile->deleteFile();
                    unset($dfile);
                }
                unset($dfiles);
            }
        }
    }

}
