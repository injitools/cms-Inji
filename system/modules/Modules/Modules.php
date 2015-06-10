<?php

/**
 * Modules class
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Modules extends Module {

    function createBlankModule($name, $codeName) {
        $codeName = ucfirst($codeName);
        Tools::createDir(App::$primary->path . '/modules/' . $codeName);
        ob_start();
        include $this->path . '/tpls/BlankModule.php';
        $moduleCode = ob_get_contents();
        ob_end_clean();
        file_put_contents(App::$primary->path . '/modules/' . $codeName . '/' . $codeName . '.php', $moduleCode);
        file_put_contents(App::$primary->path . '/modules/' . $codeName . '/info.php', CodeGenerator::genArray(['name' => $name]));
        file_put_contents(App::$primary->path . '/modules/' . $codeName . '/generatorHash.php', CodeGenerator::genArray([$codeName . '.php' => md5($moduleCode)]));
    }

    function parseColsForModel($cols = []) {
        $modelCols = [ 'labels' => [], 'cols' => [], 'relations' => []];
        foreach ($cols as $col) {
            $modelCols['labels'][$col['code']] = $col['label'];
            $colType = !empty($col['type']['primary']) ? $col['type']['primary'] : $col['type'];
            switch ($colType) {
                case 'relation':
                    $relationName = Tools::randomString();
                    $modelCols['cols'][$col['code']] = ['type' => 'select', 'source' => 'relation', 'relation' => $relationName, 'showCol' => 'name'];
                    $modelCols['relations'][$relationName] = [
                        'model' => $col['type']['aditional'],
                        'col' => $col['code']
                    ];
                    break;
                default :
                    $modelCols['cols'][$col['code']] = ['type' => $colType];
            }
        }
        return $modelCols;
    }

    function parseColsForTable($cols, $colPrefix, $tableName) {

        $colsExist = App::$cur->db->getTableCols($tableName);
        $tableCols = [];
        if (empty($colsExist[$colPrefix . 'id'])) {
            $tableCols[$colPrefix . 'id'] = 'pk';
        }
        foreach ($cols as $col) {
            if (!empty($colsExist[$colPrefix . $col['code']])) {
                continue;
            }
            $colType = !empty($col['type']['primary']) ? $col['type']['primary'] : $col['type'];
            switch ($colType) {
                case 'image':
                case 'number':
                case 'relation':
                    $tableCols[$colPrefix . $col['code']] = 'int(11) NOT NULL';
                    break;
                case 'decimal':
                    $tableCols[$colPrefix . $col['code']] = 'decimal(11,2) NOT NULL';
                    break;
                case 'dateTime':
                    $tableCols[$colPrefix . $col['code']] = 'timestamp NOT NULL';
                    break;
                case 'currentDateTime':
                    $tableCols[$colPrefix . $col['code']] = 'timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP';
                    break;
                case 'text':
                    $tableCols[$colPrefix . $col['code']] = 'varchar(255) NOT NULL';
                    break;
                case 'textarea':
                default:
                    $tableCols[$colPrefix . $col['code']] = 'text NOT NULL';
                    break;
            }
        }
        return $tableCols;
    }

    function generateModel($module, $name, $codeName, $options) {
        $codeName = ucfirst($codeName);
        $cols = [];
        $class = new CodeGenerator\ClassGenerator();
        $class->name = $codeName;
        $class->extends = '\Model';
        $modelCols = $this->parseColsForModel();
        if (!empty($options['cols'])) {
            $modelCols = $this->parseColsForModel($options['cols']);
            $tableName = strtolower($module) . '_' . strtolower($codeName);
            $tableCols = $this->parseColsForTable($options['cols'], strtolower($codeName) . '_', $tableName);
            if (App::$cur->db->tableExist($tableName)) {
                foreach ($tableCols as $colKey => $params) {
                    App::$cur->db->add_col($tableName, $colKey, $params);
                }
            } else {
                App::$cur->db->createTable($tableName, $tableCols);
            }
        }
        $class->addProperty('objectName', $name, true);
        $class->addProperty('cols', $modelCols['cols'], true);
        $class->addProperty('labels', $modelCols['labels'], true);
        $class->addMethod('relations', 'return ' . CodeGenerator::genArray($modelCols['relations']), [], true);
        $modelCode = "<?php \n\nnamespace {$module};\n\n" . $class->generate();

        $modulePath = Module::getModulePath($module);
        Tools::createDir($modulePath . '/models');
        file_put_contents($modulePath . '/models/' . $codeName . '.php', $modelCode);
        $config = Config::custom($modulePath . '/generatorHash.php');
        $config['models/' . $codeName . '.php'] = md5($modelCode);
        Config::save($modulePath . '/generatorHash.php', $config);
    }

    function install($module, $params = []) {

        $type = 'modules';

        $path = INJI_SYSTEM_DIR . '/modules/';
        $location = 'modules';

        $config = Config::app();
        $modules = !empty($config[$location]) ? array_flip($config[$location]) : [];
        if (isset($modules[$module])) {
            return true;
        }
        $info = Module::getInfo($module);

        $config[$location][] = $module;
        if (!empty($info['autoload'])) {
            $config['autoloadModules'][] = $module;
        }
        if (file_exists($path . $module . '/install_script.php')) {
            $installFunction = include $path . $module . '/install_script.php';
            $installFunction(1, $params);
        }
        Config::save('app', $config, null, App::$primary ? App::$primary : App::$cur);
    }

    function getSelectListModels($module = false) {
        $models = [];
        if ($module) {
            $modelsNames = $this->getModelsList($module);

            $info = Modules::getInfo($module);
            $moduleName = !empty($info['name']) ? $info['name'] : $module;
            foreach ($modelsNames as $modelName) {
                $fullModelName = $module . '\\' . $modelName;
                $models[$fullModelName] = $moduleName . ' - ' . ($fullModelName::$objectName ? $fullModelName::$objectName : $modelName);
            }
        }
        foreach (App::$primary->config['modules'] as $configModule) {
            if ($module == $configModule) {
                continue;
            }
            $modelsNames = $this->getModelsList($configModule);

            $info = Modules::getInfo($configModule);
            $moduleName = !empty($info['name']) ? $info['name'] : $configModule;
            foreach ($modelsNames as $modelName) {
                $fullModelName = $configModule . '\\' . $modelName;
                Router::loadClass($fullModelName);
                $models[$fullModelName] = $moduleName . ' - ' . ($fullModelName::$objectName ? $fullModelName::$objectName : $modelName);
            }
        }
        return $models;
    }

    function getModelsList($module) {
        $path = Module::getModulePath($module) . '/models';
        $models = [];
        if (file_exists($path)) {
            foreach (array_slice(scandir($path), 2) as $file) {
                if (is_dir($file)) {
                    continue;
                }
                $models[] = pathinfo($file, PATHINFO_FILENAME);
            }
        }
        return $models;
    }

    function createController($module, $controllerType) {
        $modulePath = Module::getModulePath($module);
        $path = $modulePath . '/' . $controllerType . '/' . $module . 'Controller.php';
        $class = new CodeGenerator\ClassGenerator();
        $class->name = $module . 'Controller';
        $class->extends = 'Controller';
        $controllerCode = "<?php\n\n" . $class->generate();
        Tools::createDir(pathinfo($path, PATHINFO_DIRNAME));
        file_put_contents($path, $controllerCode);
        $config = Config::custom($modulePath . '/generatorHash.php');
        $config[$controllerType . '/' . $module . 'Controller.php'] = md5($controllerCode);
        Config::save($modulePath . '/generatorHash.php', $config);
    }

    function addActionToController($module, $type, $controller, $url) {
        $modulePath = Module::getModulePath($module);
        $path = Modules::getModulePath($module) . '/' . $type . '/' . $controller . '.php';
        $class = CodeGenerator::parseClass($path);
        $class->addMethod($url . 'Action');
        $controllerCode = "<?php\n\n" . $class->generate();
        Tools::createDir(pathinfo($path, PATHINFO_DIRNAME));
        file_put_contents($path, $controllerCode);
        $config = Config::custom($modulePath . '/generatorHash.php');
        $config[$type . '/' . $module . 'Controller.php'] = md5($controllerCode);
        Config::save($modulePath . '/generatorHash.php', $config);
    }

}
