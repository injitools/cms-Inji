<?php

/**
 * Geography module
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2016 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Geography extends Module {

    public $geographyDbDir = '/tmp/Geography';

    public function init() {
        if (!empty(App::$primary->config['site']['domain'])) {
            $domain = App::$primary->config['site']['domain'];
        } else {
            $domain = implode('.', array_slice(explode('.', idn_to_utf8(INJI_DOMAIN_NAME)), -2));
        }
        $alias = str_replace($domain, '', idn_to_utf8(INJI_DOMAIN_NAME));
        $city = null;
        if ($alias) {
            $alias = str_replace('.', '', $alias);
            $city = Geography\City::get($alias, 'alias');
        }
        if (!$city) {
            if (!file_exists(App::$primary->path . $this->geographyDbDir . '/SxGeoCity.dat')) {
                $this->updateDb();
            }
            if (file_exists(App::$primary->path . $this->geographyDbDir . '/SxGeoCity.dat')) {
                $SxGeo = new Geography\SxGeo(App::$primary->path . $this->geographyDbDir . '/SxGeoCity.dat');
                $cityIp = $SxGeo->getCity($_SERVER['REMOTE_ADDR']);
                if (!empty($cityIp['city']['name_ru'])) {
                    $city = Geography\City::get($cityIp['city']['name_ru'], 'name');
                }
            }
        }
        if (!empty($_COOKIE['curcity'])) {
            $city = \Geography\City::get((int) $_COOKIE['curcity']);
        }
        if (!$city) {
            $city = Geography\City::get(1, 'default');
        }
        if (!empty($this->config['aliasRedirect']) && $city && $city->alias && !$city->default && !$alias && Module::$cur->moduleName !== 'Exchange1c') {
            Tools::redirect('//' . $city->alias . '.' . $domain . $_SERVER['REQUEST_URI']);
        }
        Geography\City::$cur = $city;
    }

    public function updateDb() {
        // Обновление файла базы данных Sypex Geo
        // Настройки
        $url = 'https://sypexgeo.net/files/SxGeoCity_utf8.zip';  // Путь к скачиваемому файлу
        $dat_file_dir = App::$primary->path . $this->geographyDbDir; // Каталог в который сохранять dat-файл
        $last_updated_file = $dat_file_dir . '/SxGeo.upd'; // Файл в котором хранится дата последнего обновления
        $info = false; // Вывод сообщений о работе, true заменить на false после установки в cron

// Конец настроек

        set_time_limit(600);
//error_reporting(E_ALL);
        //header('Content-type: text/plain; charset=utf8');

        $t = microtime(1);
        Tools::createDir($dat_file_dir);
        $types = array(
            'Country' => 'SxGeo.dat',
            'City' => 'SxGeoCity.dat',
            'Max' => 'SxGeoMax.dat',
        );
// Скачиваем архив
        preg_match("/(Country|City|Max)/", pathinfo($url, PATHINFO_BASENAME), $m);
        $type = $m[1];
        $dat_file = $types[$type];
        if ($info) echo "Скачиваем архив с сервера\n";

        $fp = fopen($dat_file_dir . '/SxGeoTmp.zip', 'wb');
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_FILE => $fp,
            CURLOPT_HTTPHEADER => file_exists($last_updated_file) ? array("If-Modified-Since: " . file_get_contents($last_updated_file)) : array(),
        ));
        if (!curl_exec($ch)) {
            if ($info) echo 'Ошибка при скачивании архива';
            return;
        }
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        fclose($fp);
        if ($code == 304) {
            @unlink($dat_file_dir . '/SxGeoTmp.zip');
            if ($info) echo "Архив не обновился, с момента предыдущего скачивания\n";
            return;
        }

        if ($info) echo "Архив с сервера скачан\n";
// Распаковываем архив
        $fp = fopen('zip://' . $dat_file_dir . '/SxGeoTmp.zip#' . $dat_file, 'rb');
        $fw = fopen($dat_file_dir . '/' . $dat_file, 'wb');
        if (!$fp) {
            if ($info)
                echo "Не получается открыть\n";
            return;
        }
        if ($info) echo "Распаковываем архив\n";
        stream_copy_to_stream($fp, $fw);
        fclose($fp);
        fclose($fw);
        if (filesize($dat_file) == 0) {
            if ($info) echo 'Ошибка при распаковке архива';
        }
        @unlink($dat_file_dir . '/SxGeoTmp.zip');
        file_put_contents($last_updated_file, gmdate('D, d M Y H:i:s') . ' GMT');
        if ($info) echo "Перемещен файл в {$dat_file_dir}/{$dat_file}\n";

    }

}
