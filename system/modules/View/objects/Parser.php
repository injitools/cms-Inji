<?php

namespace Inji\View;


class Parser {
    public function parseSource($source) {
        $tags = static::findTags($source);
        foreach ($tags as $rawTag) {
            $tag = explode(':', $rawTag);
            $html = call_user_func_array([$this, strtolower($tag[0]) . 'Tag'], array_slice($tag, 1));
            $source = str_replace('{' . $rawTag . '}', $html, $source);
        }
        return $source;
    }

    public function findTags($source) {
        if (!$source) {
            return [];
        }

        preg_match_all("|{([^}]+)}|", $source, $result);
        return $result[1];
    }

    public function cutTag($source, $rawTag) {
        $pos = strpos($source, $rawTag) - 1;
        echo substr($source, 0, $pos);
        return substr($source, ($pos + strlen($rawTag) + 2));
    }
}