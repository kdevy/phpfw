<?php

use \Nyholm\Psr7\Factory\Psr17Factory;
use \Nyholm\Psr7Server\ServerRequestCreator;
use \Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Framework\Exception\HttpError;

require_once(".." . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php");

date_default_timezone_set("Asia/Tokyo");

session_start();

/**
 * Start main application script.
 */
require_once(".." . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "config.php");
require_once(CODE_DIR . DS . "functions" . DS . "util.php");
require_once(CODE_DIR . DS . "functions" . DS . "debug.php");

$start_time = hrtime(true);

$psr17_factory = new Psr17Factory();
$emitter = new SapiEmitter();

$creator = new ServerRequestCreator(
    $psr17_factory,
    $psr17_factory,
    $psr17_factory,
    $psr17_factory
);

$server_request = $creator->fromGlobals();

list($module_name, $action_name) = parsePath($server_request->getUri()->getPath());

logsave("system:init", "-> Start application from : module = {$module_name}, action = {$action_name}.");

$action_object = loadAction([$module_name, $action_name], $server_request);

if (!isset($action_object)) {
    // call $modulename within common action.
    $action_object = loadAction([$module_name, "common"], $server_request);
}

if (!isset($action_object)) {
    // call common module action.
    $action_object = loadAction(["common", "common"], $server_request);
}

try {
    if (!isset($action_object)) {
        // bind not found page.
        throw new HttpError(404);
    }

    $action_object->initialize($server_request);
    $response = $action_object->dispatch($server_request);
}
catch(HttpError $e) {
    /**
     * index/HttpError{$e->code}Action.php　が存在する場合はそれを呼び出し、
     * ない場合はデフォルトのエラーHTMLを表示する。
     */
    $code = $e->getCode();
    $action_object = loadAction(["index", "HttpError{$code}"], $server_request);

    logsave("system:init", "Caught HttpError({$code}) exception, Render the error page.");

    if (!isset($action_object)) {
        $emitter->emit($psr17_factory->createResponse($code));
        // default error html
        echo "<!DOCTYPE html>
        <html lang='ja'>
        <head>
            <meta charset='UTF-8'>
            <meta http-equiv='X-UA-Compatible' content='IE=edge'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>" . HTTP_ERROR_CODE[$code] . " ({$code})</title>
            <style>
            body {margin: 20px 30px;}
            </style>
        </head>
        <body>
            <h2 style='font-family: serif;'>" . HTTP_ERROR_CODE[$code] . " ({$code})</h2>
        </body>
        </html>";
        exit();
    }

    $action_object->initialize($server_request);
    $response = $action_object->dispatch($server_request);
}

$emitter->emit($response);

logsave(
    "system:init",
    "<- Exit application status: MU = " . memory_get_usage(true) / 1024 . " kb"
        . ", MPU = " . memory_get_peak_usage(true) / 1024 . " kb"
        . " ,LAP = " . substr((hrtime(true) - $start_time) / (1000 * 1000), 0, 6) . " ms"
        . ", IP = " . $_SERVER["REMOTE_ADDR"] . PHP_EOL,
    LINFO
);
