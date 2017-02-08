<?php

/**
 * Modules module
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Modules extends Module {

    public function createBlankModule($name, $codeName) {
        $codeName = ucfirst($codeName);
        Tools::createDir(App::$primary->path . '/modules/' . $codeName);
        ob_start();
        include $this->path . '/tpls/BlankModule.php';
        $moduleCode = ob_get_contents();
        ob_end_clean();
        file_put_contents(App::$primary->path . '/modules/' . $codeName . '/' . $codeName . '.php', $moduleCode);
        file_put_contents(App::$primary->path . '/modules/' . $codeName . '/info.php', "<?php\nreturn " . CodeGenerator::genArray(['name' => $name]));
        file_put_contents(App::$primary->path . '/modules/' . $codeName . '/generatorHash.php', "<?php\nreturn " . CodeGenerator::genArray([$codeName . '.php' => md5($moduleCode)]));
    }

    public function parseColsForModel($cols = []) {
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
                default:
                    $modelCols['cols'][$col['code']] = ['type' => $colType];
            }
        }
        return $modelCols;
    }

    public function parseColsForTable($cols, $colPrefix, $tableName) {

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

    public function generateModel($module, $name, $codeName, $options) {
        $codeName = ucfirst($codeName);
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

    public function install($module, $params = []) {
        $installed = Module::getInstalled(App::$primary);
        if (in_array($module, $installed)) {
            return true;
        }
        $info = Module::getInfo($module);
        if (!empty($info['requires'])) {
            foreach ($info['requires'] as $requireModuleName) {
                $this->install($requireModuleName);
            }
        }

        $config = Config::app();
        
        $location = 'modules';

        $config[$location][] = $module;
        if (!empty($info['autoload'])) {
            $config['autoloadModules'][] = $module;
        }
        if (!empty($info['menu'])) {
            foreach ($info['menu'] as $appType => $items) {
                $this->addInMenu($items, $appType);
            }
        }
        Config::save('app', $config, null, App::$primary);

        $paths = Module::getModulePaths($module);
        foreach ($paths as $path) {
            if (file_exists($path . '/install_script.php')) {
                $installFunction = include $path . '/install_script.php';
                $installFunction(1, $params);
            }
        }
    }

    public function unInstall($module, $params = []) {
        $installed = Module::getInstalled(App::$primary);
        if (!in_array($module, $installed)) {
            return true;
        }
        $info = Module::getInfo($module);

        $config = Config::app();

        $type = 'modules';

        $path = INJI_SYSTEM_DIR . '/modules/';
        $location = 'modules';

        foreach ($config[$location] as $key => $moduleName) {
            if ($moduleName == $module) {
                unset($config[$location][$key]);
                break;
            }
        }
        if (!empty($config['autoload'])) {
            foreach ($config['autoload'] as $key => $moduleName) {
                if ($moduleName == $module) {
                    unset($config['autoload'][$key]);
                    break;
                }
            }
        }
        Config::save('app', $config, null, App::$primary);
        if (file_exists($path . $module . '/uninstall_script.php')) {
            $installFunction = include $path . $module . '/uninstall_script.php';
            $installFunction(1, $params);
        }
    }

    public function addInMenu($items, $appType, $parent = 0) {
        foreach ($items as $item) {
            $menuItem = new \Menu\Item();
            $menuItem->name = $item['name'];
            $menuItem->href = $item['href'];
            $menuItem->Menu_id = 1;
            $menuItem->parent_id = $parent;
            $menuItem->save(['appType' => $appType]);
            if (!empty($item['childs'])) {
                $this->addInMenu($item['childs'], $appType, $menuItem->pk());
            }
        }
    }

    public function getSelectListModels($module = '') {
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

    public function getModelsList($module, $dir = '') {
        $modulePath = Module::getModulePath($module);
        $path = rtrim($modulePath . '/models/' . $dir, '/');
        $models = [];
        if (file_exists($path)) {
            foreach (array_slice(scandir($path), 2) as $file) {
                $modelLastName = pathinfo($file, PATHINFO_FILENAME);
                if (is_dir($path . '/' . $file)) {
                    $models = array_merge($models, $this->getModelsList($module, $dir . '/' . $modelLastName));
                }
                $nameSpace = trim(preg_replace('!/' . $modelLastName . '$!', '', $dir), '/');
                $models[] = trim(str_replace('/', '\\', $nameSpace) . '\\' . $modelLastName, '\\');
            }
        }
        return $models;
    }

    public function createController($module, $controllerType) {
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

    public function addActionToController($module, $type, $controller, $url) {
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