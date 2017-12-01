<?php

/**
 * Files admin controller
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class FilesController extends adminController {

    public function managerForEditorAction() {
        $this->view->page(['page' => 'blank']);
    }

    public function clearMissingAction() {
        if (!empty($_POST['files'])) {
            $files = \Files\File::getList(['where' => ['id', $_POST['files'], 'IN']]);
            foreach ($files as $file) {
                $file->delete();
            }
        }
        if (!empty($_POST['untrackedfiles'])) {
            foreach ($_POST['untrackedfiles'] as $file) {
                if (strpos($file, '..') !== false || strpos($file, '/static/mediafiles') !== 0) {
                    continue;
                }
                unlink(\App::$primary->path . $file);
            }
        }
        $usedImages = [];
        $installedModules = \Module::getInstalled(App::$primary);
        foreach ($installedModules as $module) {
            foreach (\Module::getModels($module) as $modelPath => $modelName) {
                foreach ($modelName::$cols as $colName => $col) {
                    if ($col['type'] == 'image') {
                        $items = $modelName::getList(['where' => [$colName, 0, '!='], 'array' => true, 'key' => false, 'cols' => $modelName::colPrefix() . $colName]);
                        if ($items) {
                            foreach ($items as $item) {
                                $usedImages[$item[$modelName::colPrefix() . $colName]] = true;
                            }

                        }
                    }
                }
            }
        }
        $allImages = \Files\File::getList(['key' => 'path', 'array' => true, 'cols' => 'file_path']);
        $result = [];
        Tools::getDirContents(\App::$primary->path . '/static/mediafiles', $result, '/static/mediafiles');
        echo '<form method="post"><table>';
        foreach ($result as $file) {
            if (!isset($allImages[$file])) {
                echo '<tr>';
                echo "<td><input type='checkbox' name='untrackedfiles[]' value='{$file}' checked /></td>";
                echo "<td></td>";
                echo "<td></td>";
                echo "<td>untracked file</td>";
                echo "<td></td>";
                echo "<td><a href='{$file}' target='_blank'>{$file}</a></td>";
                echo '</tr>';
            }

        }

        $missingImages = \Files\File::getList(['where' => ['id', array_keys($usedImages), 'NOT IN'], 'key' => 'path', 'array' => true]);
        $texts = '';
        foreach (\Materials\Material::getList(['array' => true]) as $material) {
            $texts .= $material['material_preview'];
            $texts .= $material['material_text'];
        }
        if (!empty($installedModules['TextBlocks'])) {
            foreach (\TextBlocks\Block::getList(['array' => true]) as $block) {
                $texts .= $block['block_text'];
            }
        }
        $deleted = 0;
        foreach ($missingImages as $path => $file) {
            if (strpos($texts, $path)) {
                unset($missingImages[$path]);
                $deleted++;
            }
        }
        foreach ($missingImages as $path => $file) {
            echo '<tr>';
            echo "<td><input type='checkbox' name='files[]' value='{$file['file_id']}' " . ($file['file_upload_code'] == 'MigrationUpload' ? 'checked' : '') . " /></td>";
            echo "<td>{$file['file_id']}</td>";
            echo "<td>{$file['file_code']}</td>";
            echo "<td>{$file['file_upload_code']}</td>";
            echo "<td>{$file['file_name']}</td>";
            echo "<td><a href='{$path}' target='_blank'>{$path}</a></td>";
            echo '</tr>';
        }
        echo '</table><button>Удалить</button></form>';
    }

}
