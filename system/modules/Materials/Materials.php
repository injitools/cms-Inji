<?php

/**
 * Materials module
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Materials extends Module {

    public function viewsList() {
        $return = [
            'inherit' => 'Как у родителя',
            'default' => 'Стандартная страница',
            'materialWithCategorys' => 'Страница со списком категорий',
        ];
        $conf = App::$primary->view->template->config;

        if (!empty($conf['files']['modules']['Materials'])) {

            foreach ($conf['files']['modules']['Materials'] as $file) {
                if (!empty($file['type']) && $file['type'] == 'Material') {
                    $return[$file['file']] = $file['name'];
                }
            }
        }
        return $return;
    }

    public function templatesList() {
        $return = [
            'inherit' => 'Как у родителя',
            'current' => 'Текущая тема'
        ];

        $conf = App::$primary->view->template->config;

        if (!empty($conf['files']['aditionTemplateFiels'])) {
            foreach ($conf['files']['aditionTemplateFiels'] as $file) {
                $return[$file['file']] = '- ' . $file['name'];
            }
        }
        return $return;
    }

    public function viewsCategoryList() {
        $return = [
            'inherit' => 'Как у родителя',
            'category' => 'Стандартная категория',
        ];
        $conf = App::$primary->view->template->config;

        if (!empty($conf['files']['modules']['Materials'])) {

            foreach ($conf['files']['modules']['Materials'] as $file) {
                if ($file['type'] == 'Category') {
                    $return[$file['file']] = $file['name'];
                }
            }
        }
        return $return;
    }

    public function templatesCategoryList() {
        $return = [
            'inherit' => 'Как у родителя',
            'current' => 'Текущая тема'
        ];

        $conf = App::$primary->view->template->config;

        if (!empty($conf['files']['aditionTemplateFiels'])) {
            foreach ($conf['files']['aditionTemplateFiels'] as $file) {
                $return[$file['file']] = '- ' . $file['name'];
            }
        }
        return $return;
    }

    public function sitemap() {
        $map = [];
        $zeroMaterials = \Materials\Material::getList(['where' => ['category_id', 0]]);
        foreach ($zeroMaterials as $mat) {
            $map[] = [
                'name' => $mat->name,
                'url' => [
                    'loc' => (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . INJI_DOMAIN_NAME . ($mat->getHref())
                ],
            ];
        }

        $categorys = \Materials\Category::getList(['where' => ['parent_id', 0]]);
        $scan = function($category, $scan) {
            $map = [];

            foreach ($category->items as $mat) {
                $map[] = [
                    'name' => $mat->name,
                    'url' => [
                        'loc' => (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . INJI_DOMAIN_NAME . ($mat->getHref())
                    ],
                ];
            }
            foreach ($category->childs as $child) {
                $map = array_merge($map, $scan($child, $scan));
            }
            return $map;
        };
        foreach ($categorys as $category) {
            $map = array_merge($map, $scan($category, $scan));
        }
        return $map;
    }

}
