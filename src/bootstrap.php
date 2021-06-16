<?php

/**
 * Kdevy framework - My original php framework.
 *
 * Copyright © 2021 kdevy. All Rights Reserved.
 */

/**
 * リクエストURLに基づいたアプリケーションの実行とそのレスポンスの出力を行う。
 */

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Framework\Exception\HttpError;
use Framework\Route;

date_default_timezone_set("Asia/Tokyo");

// Import packages.
require_once(".." . DIRECTORY_SEPARATOR . "vendor" . DIRECTORY_SEPARATOR . "autoload.php");
// Import application settings.
require_once(".." . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "config.php");
// Import utility functions.
require_once(CODE_DIR . DS . "functions" . DS . "util.php");
// Import debug functions.
require_once(CODE_DIR . DS . "functions" . DS . "debug.php");

// Set session settings.
session_save_path(SESSION_SAVE_PATH);
ini_set("session.use_cookies", "On");
ini_set("session.use_only_cookies", "On");
ini_set("session.use_strict_mode", "On");
ini_set("session.cookie_httponly", "On");
ini_set("session.sid_length", "48");
ini_set("session.sid_bits_per_character", "5");

// Switch settings for development or production.
if (DEBUG) {
    ini_set("session.cookie_lifetime", 60 * 60 * 24 * 7);
} else {
    ini_set("session.cookie_lifetime", 60 * 30);
    ini_set("session.cookie_secure", "On");
}

session_start();

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

$route = Route::create($server_request->getUri()->getPath());

logsave("system:init", "-> Start application from " . $route->getPathName() . " .");

$action_object = loadAction($route, $server_request);

if (!isset($action_object)) {
    // call $modulename within common action.
    $route->setPath($route->getModuleName(), "common");
    $action_object = loadAction($route, $server_request);
}

if (!isset($action_object)) {
    // call common module action.
    $route->setPath("common", "common");
    $action_object = loadAction($route, $server_request);
}

try {
    if (!isset($action_object)) {
        // bind not found page.
        throw new HttpError(404);
    }

    $action_object->initialize($server_request);
    $response = $action_object->dispatch($server_request);
} catch (HttpError $e) {
    /**
     * index/HttpError{$e->code}Action.php　が存在する場合はそれを呼び出し、
     * ない場合はデフォルトのエラーHTMLを表示する。
     */
    $code = $e->getCode();
    $route->setPath("index", "http-error{$code}");
    $action_object = loadAction($route, $server_request);

    logsave("system:init", "Caught HttpError({$code}) exception, Render the error page.");

    if (!isset($action_object)) {
        $emitter->emit($psr17_factory->createResponse($code));
        // Default error html.
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
