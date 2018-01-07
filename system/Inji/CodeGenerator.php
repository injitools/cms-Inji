<?php

namespace Inji;
/**
 * Code generator
 *
 * @author Alexey Krupskiy <admin@inji.ru>
 * @link http://inji.ru/
 * @copyright 2015 Alexey Krupskiy
 * @license https://github.com/injitools/cms-Inji/blob/master/LICENSE
 */
class CodeGenerator {

    public static function genArray($data, $level = 0) {
        $return = '';
        if ($level == 0)
            $return = "[";
        foreach ($data as $key => $item) {
            $return .= "\n" . str_repeat(' ', ($level * 4 + 4)) . "'{$key}' => ";
            if (!is_array($item)) {
                if (is_bool($item)) {
                    $item = $item ? 'true' : 'false';
                    $return .= "{$item},";
                } elseif (is_null($item)) {
                    $return .= "null,";
                } elseif (is_int($item) || is_float($item)) {
                    $return .= "{$item},";
                } else {
                    $return .= "'" . addcslashes($item, "'") . "',";
                }
            } else {
                $return .= "[";
                $return .= rtrim(self::genArray($item, $level + 1), ',');
                $return .= "\n" . str_repeat(' ', ($level * 4 + 4)) . "],";
            }
        }
        if ($level == 0) {
            $return = rtrim($return, ',') . "\n];";
        }

        return $return;
    }

    public static function parseClass($path) {
        $code = file_get_contents($path);

        $parser = new PhpParser\Parser(new PhpParser\Lexer\Emulative);

        try {
            $stmts = $parser->parse($code);
            $class = new CodeGenerator\ClassGenerator();
            $class->name = $stmts[0]->name;
            $class->extends = implode(',', $stmts[0]->extends->parts);
            foreach ($stmts[0]->stmts as $stmt) {
                if (get_class($stmt) == 'PhpParser\Node\Stmt\ClassMethod') {
                    $class->addMethod($stmt->name);
                }
            }
            return $class;
            // $stmts is an array of statement nodes
        } catch (PhpParser\Error $e) {
            echo 'Parse Error: ', $e->getMessage();
            exit();
        }
    }

}
