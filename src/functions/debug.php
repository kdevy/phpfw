<?php

/**
 * Kdevy framework - My original php framework.
 *
 * Copyright © 2021 kdevy. All Rights Reserved.
 */

/**
 * print_r ラッパー関数 ブラウザ表示用
 * TODO: いずれ削除
 *
 * @param mixed $value
 * @param mixed ...$args
 * @return void
 */
function pr($value, ...$args): void
{
    echo "<pre style=\"background:#ccc;padding:2px;border:dotted 1px gray;\">";
    echo "<div><span style=\"display:inline-block;padding:2px;cursor:pointer;\"
    onclick=\"this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode);\">×</span></div>";
    if (count($args) == 0) {
        print_r($value);
    } else {
        print_r(array_merge([$value], $args));
    }
    echo "</pre>";
}

/**
 * print_r ラッパー関数（エスケープ済） ブラウザ表示用
 * TODO: いずれ削除
 *
 * @param mixed $value
 * @param mixed ...$args
 * @return void
 */
function hpr($value, ...$args): void
{
    if (count($args) == 0) {
        pr(htmlspecialchars($value));
    } else {
        pr(htmlspecialchars(print_r(array_merge([$value], $args), true)));
    }
}

/**
 * var_dump ラッパー関数 ブラウザ表示用
 * TODO: いずれ削除
 *
 * @param mixed $value
 * @param mixed ...$args
 * @return void
 */
function vd($value, ...$args): void
{
    echo "<pre>";
    echo "<div><span style=\"display:inline-block;padding:2px;cursor:pointer;\"
    onclick=\"this.parentNode.parentNode.parentNode.removeChild(this.parentNode.parentNode);\">×</span></div>";
    if (count($args) == 0) {
        var_dump($value);
    } else {
        var_dump(array_merge([$value], $args));
    }
    echo "</pre>";
}

/**
 * var_dump()の実行結果を文字列として返す
 *
 * @param mixed $value
 * @return string
 */
function var_dump_string($value): string
{
    ob_start();
    var_dump($value);
    return ob_get_clean();
}

/**
 * status/ 配下にログ出力を行う
 *
 * @param string $location
 * @param mixed $value
 * @return void
 */
function logsave(string $location, $value, int $level = LINFO): void
{
    $filename = null;

    if (LOG_LEVEL > $level) {
        return;
    }

    if (strpos($location, ":") !== false) {
        $exp = explode(":", $location);
        $location = $exp[1];
        $filename = STATUS_DIR . DS . $exp[0] . ".log";
    } else {
        $filename = STATUS_DIR . DS . "default.log";
    }
    $output = "[" . date("Y-m-d H:i:s") . "][" . str_pad($location, 8) . "][" . LOG_NAMES[$level] . "] ";
    $file = fopen($filename, "a");

    // exception
    if ($value instanceof Exception) {
        $output .= str_replace(["\n", "\r", "\r\n"], " ", sprintf(
            "%s: %s in %s(%s) Stack trace: %s",
            $value::class,
            $value->getMessage(),
            $value->getFile(),
            $value->getLine(),
            $value->getTraceAsString()
        ));
    }
    // string
    elseif (!is_array($value) && !is_object($value)) {
        $output .= $value . PHP_EOL;
    }
    // array or object
    else {
        $output .= str_replace(
            ["\n", "\r", "\r\n"],
            "",
            var_export($value, true) . PHP_EOL
        ) . PHP_EOL;
    }

    fwrite($file, $output);
}
