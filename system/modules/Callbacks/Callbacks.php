<?php

/**
 * Callbacks module
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Callbacks extends Module {

    public function init() {
        $callbacksData = filter_input(INPUT_POST, 'Callbacks', FILTER_REQUIRE_ARRAY);
        if (!empty($callbacksData)) {
            $callback = new \Callbacks\Callback();
            $error = false;
            if (empty($callbacksData['text'])) {
                $error = true;
                \Inji\Msg::add('Вы не написали текст отзыва');
            } else {
                $callback->text = nl2br(htmlspecialchars($callbacksData['text']));
            }
            if (empty($callbacksData['name'])) {
                $error = true;
                \Inji\Msg::add('Вы не указали свое имя');
            } else {
                $callback->name = htmlspecialchars($callbacksData['name']);
            }
            if (empty($callbacksData['phone'])) {
                $error = true;
                \Inji\Msg::add('Вы не указали свой номер телефона');
            } else {
                $callback->phone = htmlspecialchars($callbacksData['phone']);
            }
            $files = filter_var($_FILES['Callbacks'], FILTER_REQUIRE_ARRAY);
            if (!empty($files['tmp_name']['photo'])) {
                $callback->image_file_id = App::$cur->files->upload([
                    'name' => $files['name']['photo'],
                    'tmp_name' => $files['tmp_name']['photo'],
                ]);
            }
            $callback->mail = htmlspecialchars($callbacksData['mail']);
            $callback->type_id = (int) $callbacksData['type'];
            if (!$error) {
                $callback->save();
                if (!empty(App::$cur->config['site']['email'])) {
                    $subject = 'Новый отзыв';
                    $text = 'Вы можете его посмотреть по этому адресу: <a href = "http://' . idn_to_utf8(INJI_DOMAIN_NAME) . '/admin/callbacks">http://' . idn_to_utf8(INJI_DOMAIN_NAME) . '/admin/callbacks</a>';
                    Tools::sendMail('noreply@' . INJI_DOMAIN_NAME, App::$cur->config['site']['email'], $subject, $text);
                }
                Tools::redirect('/', 'Ваш отзыв был получен и появится после обработки администратором', 'success');
            }
        }
    }

    public function viewsCategoryList($inherit = true) {
        $return = [];
        if ($inherit) {
            $return['inherit'] = 'Как у родителя';
        }
        $return['index'] = 'Обычная странциа';
        $conf = App::$primary->view->template->config;
        if (!empty($conf['files']['modules']['Callbacks'])) {
            foreach ($conf['files']['modules']['Callbacks'] as $file) {
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

}
