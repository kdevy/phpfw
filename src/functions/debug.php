<?php

/**
 * print_r ラッパー関数 ブラウザ表示用
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
    ob_start();
    if (count($args) == 0) {
        var_dump($value);
    } else {
        var_dump(array_merge([$value], $args));
    }
    echo ob_get_clean();
    echo "</pre>";
}

/**
 * status/ 配下にログ出力を行う
 *
 * @param string $location
 * @param mixed $value
 * @param mixed ...$args
 * @return void
 */
function logsave(int $level, string $location, $value, ...$args): void
{
    $filename = null;

    if (LOG_LEVEL > $level) {
        return;
    }

    if (strpos($location, ":") !== false) {
        $exp = explode(":", $location);
        $location = $exp[1];
        $filename = STATUS_DIR . DS . $exp[0] . "_" . date("Y-m") . ".log";
    } else {
        $filename = STATUS_DIR . DS . date("Y-m-d") . ".log";
    }
    $output = "[" . date("Y-m-d H:i:s") . "][" . str_pad($location, 8) . "][" . LOG_NAMES[$level] . "] ";
    $file = fopen($filename, "a");

    if (count($args) == 0) {
        if (!is_array($value) && !is_object($value)) {
            $output .= $value . PHP_EOL;
        } else {
            $output .= str_replace(
                ["\n", "\r", "\r\n"],
                "",
                var_export($value, true) . PHP_EOL
            ) . PHP_EOL;
        }
    } else {
        $output .= str_replace(
            ["\n", "\r", "\r\n"],
            "",
            var_export(array_merge([$value], $args), true)
        ) . PHP_EOL;
    }

    fwrite($file, $output);
}