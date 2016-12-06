<?php

/**
 * Recaptcha module
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Recaptcha extends Module {

    public function init() {
        App::$cur->view->customAsset('js', 'https://www.google.com/recaptcha/api.js');
    }

    public function show() {
        if (!$this->config['sitekey']) {
            echo 'SiteKey not set for reCAPTCHA';
        } else {
            echo "<div class='g-recaptcha' data-sitekey='{$this->config['sitekey']}'></div>";
        }
    }

    public function check($gResponse) {
        $data = [];
        $data['secret'] = $this->config['secret'];
        $data['response'] = $gResponse;
        $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify?' . http_build_query($data));
        if ($response) {
            return json_decode($response);
        }
        return false;
    }

}
