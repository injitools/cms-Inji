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
        echo '<form method="post"><table>';
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
            echo "<td><input type='checkbox' name='files[]' value='{$file['file_id']}' checked /></td>";
            echo "<td>{$file['file_id']}</td>";
            echo "<td>{$file['file_code']}</td>";
            echo "<td>{$file['file_upload_code']}</td>";
            echo "<td>{$file['file_name']}</td>";
            echo '</tr>';
        }
        echo '</table><button>Удалить</button></form>';
    }

}
