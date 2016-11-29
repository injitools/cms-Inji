<?php

/**
 * Libs controller
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class LibsController extends Controller {

    public function vendorAction() {
        $args = func_get_args();
        $path = $this->module->getPath($args);
        if ($path) {
            $this->StaticLoader->giveFile($path);
        }
    }

}
