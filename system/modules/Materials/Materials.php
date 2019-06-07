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
            'main_page' => 'Главная страница',
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
                    'loc' => (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . App::$cur->getDomain() . ($mat->getHref())
                ],
            ];
        }

        $categorys = \Materials\Category::getList(['where' => ['parent_id', 0]]);
        $scan = function ($category, $scan) {
            $map = [];

            foreach ($category->items as $mat) {
                $map[] = [
                    'name' => $mat->name,
                    'url' => [
                        'loc' => (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . App::$cur->getDomain() . ($mat->getHref())
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

    function search($search) {
        $query = 'select material_id from ' . App::$cur->db->table_prefix . \Materials\Material::table() . ' where MATCH (material_name,material_text,material_keywords,material_description) AGAINST (?)';
        $ids = array_keys(App::$cur->db->query(['query' => $query, 'params' => [$search]])->getArray('material_id'));
        $count = count($ids);
        $pages = new \Ui\Pages($_GET, ['count' => $count]);
        //items
        //items
        $items = \Materials\Material::getList([
            'where' => ['id', $ids, 'IN'],
            'start' => $pages->params['start'],
            'limit' => $pages->params['limit']
        ]);
        return ['count' => $count, 'items' => $items, 'pages' => $pages];
    }

    public function siteSearch($search) {
        $result = $this->search($search);
        $searchResult = [];
        foreach ($result['items'] as $item) {
            $details = '<div>';
            $shortdes = mb_substr(strip_tags($item->text), 0, 300);
            $shortdes = mb_substr($shortdes, 0, mb_strrpos($shortdes, ' '));
            $details .= $shortdes;
            if (mb_strlen($item->description) > $shortdes) {
                $details .= '...';
            }
            $details .= '</div>';
            $searchResult[] = [
                'title' => $item->name(),
                'details' => $details,
                'href' => '/materials/view/' . $item->id
            ];
        }
        return ['name' => 'Материалы', 'count' => $result['count'], 'result' => $searchResult, 'detailSearch' => ' / materials / detailSearch ? search = ' . $search];
    }
}
