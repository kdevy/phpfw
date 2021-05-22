<?php

use \Mimey\MimeTypes;

/**
 * Output static file, send from /modules/{$module_name}/statics/{$static_file_path}
 */

$ru = explode("/", $_SERVER["REQUEST_URI"]);
$module_name = (isset($ru[1]) && $ru[1] !== "" ? $ru[1] : "index");

if ($module_name == "static") {
    if (count($ru) == 3) {
        $module_name = "index";
        $static_file_path = DIRECTORY_SEPARATOR . $ru[2];
    } else {
        $module_name = $ru[2];
        $static_file_path = DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, array_slice($ru, 3));
    }
    $static_file_path = dirname(__DIR__) . DIRECTORY_SEPARATOR . "modules" .
    DIRECTORY_SEPARATOR . $module_name . DIRECTORY_SEPARATOR . "statics" . $static_file_path;
    $ext = pathinfo($static_file_path, PATHINFO_EXTENSION);

    if (is_readable($static_file_path)) {
        $mimes = new MimeTypes();
        header("Content-Type: " . $mimes->getMimeType($ext));
        readfile($static_file_path);
    } else {
        http_response_code(404);
    }
    exit();
}
