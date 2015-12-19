<?php

/**
 * Libs module
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Libs extends Module
{
    function loadLib($libName, $options = [])
    {

        $className = 'Libs\\' . ucfirst($libName);
        if (class_exists($className)) {
            if (!empty($className::$composerPacks)) {
                foreach ($className::$composerPacks as $packageName => $version) {
                    ComposerCmd::requirePackage($packageName, $version);
                }
            }
            if (!empty($className::$requiredLibs)) {
                foreach ($className::$requiredLibs as $rLib) {
                    $this->loadLib($rLib);
                }
            }
            if (!empty($className::$files['css']) && (!isset($options['loadCss']) || $options['loadCss'] )) {
                foreach ($className::$files['css'] as $file) {
                    if (strpos($file, '/') === 0 || strpos($file, 'http') === 0) {
                        App::$cur->view->customAsset('css', $file, $libName);
                    } else {
                        App::$cur->view->customAsset('css', '/static/libs/vendor/' . ucfirst($libName) . '/' . $file, $libName);
                    }
                }
            }
            if (!empty($className::$files['js'])) {
                foreach ($className::$files['js'] as $file) {
                    if (strpos($file, '/') === 0 || strpos($file, 'http') === 0) {
                        App::$cur->view->customAsset('js', $file, $libName);
                    } else {
                        App::$cur->view->customAsset('js', '/static/libs/vendor/' . ucfirst($libName) . '/' . $file, $libName);
                    }
                }
            }
        }
    }

    function staticCalled($file, $dir)
    {
        $libPath = preg_replace('!^libs/!', '', $file);
        $libName = substr($libPath, 0, strpos($libPath, '/'));
        $className = 'Libs\\' . ucfirst($libName);
        if (class_exists($className)) {
            if (!empty($className::$programDirs)) {
                $fileDir = substr($libPath, strlen($libName) + 1, strpos($libPath, '/', strlen($libName) + 1) - strlen($libName) - 1);
                foreach ($className::$programDirs as $programDir) {
                    if ($programDir == $fileDir) {
                        include $dir . $file;
                        exit();
                    }
                }
            }
        }
        return $dir . $file;
    }

    function getPath($args)
    {
        if (!empty($args[0])) {
            $libName = 'Libs\\' . ucfirst($args[0]);
            if (class_exists($libName)) {
                $file = implode('/', array_slice($args, 1));
                foreach ($libName::$staticDirs as $dir) {
                    if (strpos($file, $dir) === 0) {
                        return \App::$primary->path . '/vendor/' . $file;
                    }
                }
            }
        }
        return false;
    }

}
