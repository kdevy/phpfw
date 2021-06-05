<?php

if (!defined("DS")) {
    define("DS", DIRECTORY_SEPARATOR);
}

/**
 * Application core directory path.
 */

// root project directory.
define("BASE_DIR", dirname(__DIR__));

// php setting files directory.
define("CONFIG_DIR", BASE_DIR . DS . "config");

// module directory.
define("MODULE_DIR", BASE_DIR . DS . "modules");

// apache log files directory.
define("LOG_DIR", BASE_DIR . DS . "logs");

// application log files directory.
define("STATUS_DIR", BASE_DIR . DS . "status");

// document root directory.
define("PUBLIC_DIR", BASE_DIR . DS . "public");

// php source code directory.
define("CODE_DIR", BASE_DIR . DS . "src");

// composer package directory.
define("VENDOR_DIR", BASE_DIR . DS . "vendor");

/**
 * database connection settings.
 */
define("DB_CONFIG", [
    "default" => [
        "HOST" => "localhost",
        "PORT" => "3306",
        "NAME" => "world",
        "USER" => "root",
        "PASSWORD" => "",
        "CHARSET" => "utf8mb4",
    ]
]);

/**
 * Application directory name.
 */

 // ex) modules/{module name}/{ACTIONS_DIRNAME}/HogeAction.php
define("ACTIONS_DIRNAME", "actions");

 // ex) modules/{module name}/{TEMPLATES_DIRNAME}/hoge.html
define("TEMPLATES_DIRNAME", "templates");

/**
 * Other settings.
 */

// Assign context variables in render().
define("CONTEXT_KEYWORD", "___");

// Used in APIAction. ex) ?{HTTP_ACTION}={action name}
define("HTTP_ACTION", "action");

// Http error status code phrases.
define("HTTP_ERROR_CODE", [
    300 => 'Multiple Choices', 301 => 'Moved Permanently', 302 => 'Found', 303 => 'See Other', 304 => 'Not Modified', 305 => 'Use Proxy', 306 => 'Switch Proxy', 307 => 'Temporary Redirect',
    400 => 'Bad Request', 401 => 'Unauthorized', 402 => 'Payment Required', 403 => 'Forbidden', 404 => 'Not Found', 405 => 'Method Not Allowed', 406 => 'Not Acceptable', 407 => 'Proxy Authentication Required', 408 => 'Request Time-out', 409 => 'Conflict', 410 => 'Gone', 411 => 'Length Required', 412 => 'Precondition Failed', 413 => 'Request Entity Too Large', 414 => 'Request-URI Too Large', 415 => 'Unsupported Media Type', 416 => 'Requested range not satisfiable', 417 => 'Expectation Failed', 418 => 'I\'m a teapot', 422 => 'Unprocessable Entity', 423 => 'Locked', 424 => 'Failed Dependency', 425 => 'Unordered Collection', 426 => 'Upgrade Required', 428 => 'Precondition Required', 429 => 'Too Many Requests', 431 => 'Request Header Fields Too Large', 451 => 'Unavailable For Legal Reasons',
    500 => 'Internal Server Error', 501 => 'Not Implemented', 502 => 'Bad Gateway', 503 => 'Service Unavailable', 504 => 'Gateway Time-out', 505 => 'HTTP Version not supported', 506 => 'Variant Also Negotiates', 507 => 'Insufficient Storage', 508 => 'Loop Detected', 511 => 'Network Authentication Required',
]);

/**
 * Debug flag.
 */
define("DEBUG", true);

/**
 * logsave() level.
 */
define("LDEBUG", 1);
define("LINFO", 2);
define("LWARN", 3);
define("LERROR", 4);

define("LOG_NAMES",
    [
        LDEBUG => "DEBUG",
        LINFO => "INFO",
        LWARN => "WARN",
        LERROR => "ERROR",
    ]
);

if (DEBUG){
    define("LOG_LEVEL", LDEBUG);
} else {
    define("LOG_LEVEL", LINFO);
}

define("SESSION_SAVE_PATH", BASE_DIR . DS . "tmp" . DS . "session");