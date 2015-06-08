<?php

class Materials extends Module {

    function viewsList() {
        $return = [];
        if (!empty(App::$cur->system)) {
            $conf = App::$cur->view->getParentConfig();
        } else {
            $conf = App::$cur->view->getConfig();
        }
        if (!empty($conf['files']['modules']['Materials'])) {

            foreach ($conf['files']['modules']['Materials'] as $file) {
                $return[$file['file']] = $file['name'];
            }
        } else {
            $return ['default'] = 'Внутренняя страница';
        }
        return $return;
    }

    function templatesList() {
        $return = ['current' => 'Текущая тема'];

        $conf = App::$primary->view->getParentConfig();

        if (!empty($conf['files']['aditionTemplateFiels'])) {
            foreach ($conf['files']['aditionTemplateFiels'] as $file) {
                $return[$file['file']] = '- ' . $file['name'];
            }
        }
        return $return;
    }

}

?>