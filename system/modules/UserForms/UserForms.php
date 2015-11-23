<?php

class UserForms extends \Module
{
    function init()
    {
        \App::$cur->view->customAsset('js', '/static/moduleAsset/UserForms/js/formCatcher.js');
        if (!empty($_POST['UserForms'])) {
            foreach ($_POST['UserForms'] as $form_id => $inputs) {
                $form = \UserForms\Form::get((int) $form_id);
                if (!$form)
                    continue;
                $formRecive = new \UserForms\Recive();
                $formRecive->user_id = (int) \Users\User::$cur->id;
                $formRecive->form_id = (int) $form_id;
                $data = [];
                $error = false;
                foreach ($form->inputs as $input) {
                    if (isset($inputs['input' . $input->id])) {
                        $data['input' . $input->id] = htmlspecialchars($inputs['input' . $input->id]);
                    } elseif ($input->required) {
                        $error = true;
                        Msg::add('Вы не заполнили поле: ' . $input->label);
                    } else {
                        $data['input' . $input->id] = '';
                    }
                }
                if (!$error) {
                    $formRecive->data = json_encode($data);
                    $formRecive->save();
                }
            }

            if (!$error && !empty(App::$cur->config['site']['email'])) {
                $text = '';
                foreach ($form->inputs as $input) {
                    if (isset($inputs['input' . $input->id])) {
                        $text .="<b>{$input->label}:</b> " . htmlspecialchars($inputs['input' . $input->id]) . "<br />";
                    }
                }
                if ($text) {
                    $text = 'Дата получения по серверному времени: ' . date('Y-m-d H:i:s') . '<br />Заполненые поля:<br />' . $text;
                    Tools::sendMail('noreply@' . idn_to_utf8(INJI_DOMAIN_NAME), App::$cur->config['site']['email'], $form->name, $text);
                }
            }
            Tools::redirect($_SERVER['REQUEST_URI'], 'Ваша форма была успешно отправлена', 'success');
        }
    }

    function formData($item)
    {
        $inputs = json_decode($item->data, true);
        $text = '';
        foreach ($item->form->inputs as $input) {
            if (isset($inputs['input' . $input->id])) {
                $text .= "{$input->label}: " . htmlspecialchars($inputs['input' . $input->id]) . "<br />";
            }
        }
        return $text;
    }

}
