<?php

/**
 * Tools object
 * 
 * Toolkit with most needed functions
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class Tools extends Model {

    /**
     * Return random string
     * 
     * @param int $length
     * @param string $characters
     * @return string
     */
    public static function randomString($length = 20, $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ') {
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    /**
     * Clean and return user query params
     * 
     * @param string $uri
     * @return array
     */
    public static function uriParse($uri) {
        $answerPos = strpos($uri, '?');
        $params = array_slice(explode('/', substr($uri, 0, $answerPos ? $answerPos : strlen($uri))), 1);

        foreach ($params as $key => $param) {
            if ($param != '') {
                $params[$key] = urldecode($param);
            } else {
                unset($params[$key]);
            }
        }
        return $params;
    }

    /**
     * Recursive create dir
     * 
     * @param string $path
     * @return boolean
     */
    public static function createDir($path) {
        if (file_exists($path))
            return true;

        $path = explode('/', $path);
        $cur = '';
        foreach ($path as $item) {
            $cur .= $item . '/';
            if (!file_exists($cur)) {
                mkdir($cur);
            }
        }
        return true;
    }

    /**
     * Resize image in path
     * 
     * @param string $img_path
     * @param int $max_width
     * @param int $max_height
     * @param string|false $crop
     * @param string $pos
     * @return string
     */
    public static function resizeImage($img_path, $max_width = 1000, $max_height = 1000, $crop = false, $pos = 'center') {
        ini_set("gd.jpeg_ignore_warning", 1);
        list($img_width, $img_height, $img_type, $img_tag) = getimagesize($img_path);
        switch ($img_type) {
            case 1:
                $img_type = 'gif';
                break;
            case 3:
                $img_type = 'png';
                break;
            case 2:
            default:
                $img_type = 'jpeg';
                break;
        }
        $imagecreatefromX = "imagecreatefrom{$img_type}";
        $src_res = $imagecreatefromX($img_path);
        if (!$src_res) {
            return false;
        }

        if ($img_width / $max_width > $img_height / $max_height) {
                    $separator = $img_width / $max_width;
        } else {
                    $separator = $img_height / $max_height;
        }

        if ($crop === true || $crop == 'q') {
            if ($img_width > $img_height) {
                $imgX = floor(($img_width - $img_height) / 2);
                $imgY = 0;
                $img_width = $img_height;
                $new_width = $max_width;
                $new_height = $max_height;
            } else {
                $imgX = 0;
                $imgY = floor(($img_height - $img_width) / 2);
                $img_height = $img_width;
                $new_width = $max_width;
                $new_height = $max_height;
            }
            if ($pos == 'top') {
                $imgY = 0;
            }
        } elseif ($crop == 'c') {
//Вычисляем некий коэффициент масштабирования
            $k1 = $img_width / $max_width;
            $k2 = $img_height / $max_height;
            $k = $k1 > $k2 ? $k2 : $k1;
            $ow = $img_width;
            $oh = $img_height;
//Вычисляем размеры области для нового изображения
            $img_width = intval($max_width * $k);
            $img_height = intval($max_height * $k);
            $new_width = $max_width;
            $new_height = $max_height;
//Находим начальные координаты (центрируем новое изображение)
            $imgX = (int) (($ow / 2) - ($img_width / 2));
            if ($pos == 'center') {
                $imgY = (int) (($oh / 2) - ($img_height / 2));
            } else {
                $imgY = 0;
            }
        } else {
            $imgX = 0;
            $imgY = 0;
            $new_width = floor($img_width / $separator);
            $new_height = floor($img_height / $separator);
        }

        $new_res = imagecreatetruecolor($new_width, $new_height);
        imageAlphaBlending($new_res, false);
        imagesavealpha($new_res, true);
        imagecopyresampled($new_res, $src_res, 0, 0, $imgX, $imgY, $new_width, $new_height, $img_width, $img_height);

        if ($img_type == 'jpeg') {
            imageinterlace($new_res, 1); // чересстрочное формирование изображение
            imagejpeg($new_res, $img_path, 85);
        } else {
            $imageX = "image{$img_type}";
            $imageX($new_res, $img_path);
        }

        imagedestroy($new_res);
        imagedestroy($src_res);
        return $img_type;
    }

    /**
     * Send mail
     * 
     * @param string $from
     * @param string $to
     * @param string $subject
     * @param string $text
     * @param string $charset
     * @param string $ctype
     * @return boolean
     */
    public static function sendMail($from, $to, $subject, $text, $charset = 'utf-8', $ctype = 'text/html') {
        $msg = compact('from', 'to', 'subject', 'text', 'charset', 'ctype');
        $msg = Inji::$inst->event('sendMail', $msg);
        if (is_array($msg)) {
            $headers = "From: {$msg['from']}\r\n";
            $headers .= "Content-type: {$msg['ctype']}; charset={$msg['charset']}\r\n";
            $headers .= "Mime-Version: 1.0\r\n";
            return mail($msg['to'], $msg['subject'], $msg['text'], $headers);
        }
        return $msg;
    }

    /**
     * Redirect user from any place of code
     * 
     * Also add message to message query for view
     * 
     * @param string $href
     * @param string $text
     * @param string $status
     */
    public static function redirect($href = null, $text = false, $status = 'info') {
        if ($href === null) {
            $href = $_SERVER['REQUEST_URI'];
        }
        if ($text !== false) {
            Msg::add($text, $status);
        }
        if (!headers_sent()) {
            header("Location: {$href}");
        } else {
            echo '\'"><script>window.location="' . $href . '";</script>';
        }
        exit("Перенаправление на: <a href = '{$href}'>{$href}</a>");
    }

    /**
     * Функция возвращает окончание для множественного числа слова на основании числа и массива окончаний
     * @param  $number Integer Число на основе которого нужно сформировать окончание
     * @param  $endingsArray  Array Массив слов или окончаний для чисел (1, 4, 5),
     *         например array('яблоко', 'яблока', 'яблок')
     * @return String
     */
    public static function getNumEnding($number, $endingArray) {
        $number = $number % 100;
        if ($number >= 11 && $number <= 19) {
            $ending = $endingArray[2];
        } else {
            $i = $number % 10;
            switch ($i) {
                case (1): $ending = $endingArray[0];
                    break;
                case (2):
                case (3):
                case (4): $ending = $endingArray[1];
                    break;
                default: $ending = $endingArray[2];
            }
        }
        return $ending;
    }

    /**
     * Clean request path
     * 
     * @param string $path
     * @return string
     */
    public static function parsePath($path) {
        $path = str_replace('\\', '/', $path);
        $pathArray = explode('/', $path);
        $cleanPathArray = [];
        do {
            $changes = 0;
            foreach ($pathArray as $pathItem) {
                if (trim($pathItem) === '' || $pathItem == '.') {
                    $changes++;
                    continue;
                }
                if ($pathItem == '..') {
                    array_pop($cleanPathArray);
                    $changes++;
                    continue;
                }
                $cleanPathArray[] = $pathItem;
            }
            $pathArray = $cleanPathArray;
            $cleanPathArray = [];
        } while ($changes);
        return (strpos($path, '/') === 0 ? '/' : '') . implode('/', $pathArray);
    }

    /**
     * Show date in rus
     * 
     * @param string $date
     * @return string
     */
    public static function toRusDate($date) {
        $yy = (int) substr($date, 0, 4);
        $mm = (int) substr($date, 5, 2);
        $dd = (int) substr($date, 8, 2);

        $hours = substr($date, 11, 5);

        $month = array('января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря');
        if (empty($month[$mm - 1])) {
            return 'Не указано';
        }
        return ($dd > 0 ? $dd . " " : '') . $month[$mm - 1] . " " . $yy . " " . $hours;
    }

    /**
     * Set header
     * 
     * @param string $code
     * @param boolean $exit
     */
    public static function header($code, $exit = false) {
        if (!headers_sent()) {
            switch ($code) {
                case '404':
                    header('HTTP/1.1 404 Not Found');
                    break;
                default :
                    header($code);
            }
        }
        if ($exit) {
            exit;
        }
    }

    /**
     * Return exist path from array
     * 
     * If no exist path in array - return default
     * 
     * @param array $paths
     * @param string|false $default
     * @return string|false
     */
    public static function pathsResolve($paths = [], $default = false) {
        foreach ($paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }
        return $default;
    }

    /**
     * Convert acronyms to bites
     * 
     * @param string $val
     * @return int
     */
    public static function toBytes($val) {
        $val = trim($val);
        $last = strtolower($val[strlen($val) - 1]);
        switch ($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }

        return $val;
    }

    /**
     * Recursive copy directories and files
     * 
     * @param string $from
     * @param string $to
     */
    public static function copyFiles($from, $to) {
        $from = rtrim($from, '/');
        $to = rtrim($to, '/');
        self::createDir($to);
        $files = scandir($from);
        foreach ($files as $file) {
            if (in_array($file, ['.', '..'])) {
                continue;
            }
            if (is_dir($from . '/' . $file)) {
                self::copyFiles($from . '/' . $file, $to . '/' . $file);
            } else {
                copy($from . '/' . $file, $to . '/' . $file);
            }
        }
    }

    /**
     * Translit function
     * 
     * @param string $str
     * @return string
     */
    public static function translit($str) {
        $rus = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
        $lat = array('A', 'B', 'V', 'G', 'D', 'E', 'E', 'Gh', 'Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'C', 'Ch', 'Sh', 'Sch', 'Y', 'Y', 'Y', 'E', 'Yu', 'Ya', 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'gh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya');
        return str_replace($rus, $lat, $str);
    }

    /**
     * get youtube video ID from URL
     *
     * @author http://stackoverflow.com/a/6556662
     * @param string $url
     * @return string Youtube video id or FALSE if none found. 
     */
    public static function youtubeIdFromUrl($url) {
        $pattern = '%^# Match any youtube URL
        (?:https?://)?  # Optional scheme. Either http or https
        (?:www\.)?      # Optional www subdomain
        (?:             # Group host alternatives
          youtu\.be/    # Either youtu.be,
        | youtube\.com  # or youtube.com
          (?:           # Group path alternatives
            /embed/     # Either /embed/
          | /v/         # or /v/
          | /watch\?v=  # or /watch\?v=
          )             # End path alternatives.
        )               # End host alternatives.
        ([\w-]{10,12})  # Allow 10-12 for 11 char youtube id.
        $%x'
        ;
        $result = preg_match($pattern, $url, $matches);
        if (false !== $result) {
            return $matches[1];
        }
        return false;
    }

    /**
     * check array is associative
     *
     * @author http://stackoverflow.com/a/173479
     * @param array $arr
     * @return boolean
     */
    public static function isAssoc(&$arr) {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

}
