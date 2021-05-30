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
        "NAME" => "kdevy",
        "USER" => "root",
        "PASSWORD" => "",
        "CHARSET" => "utf8mb4",
    ]
]);

/**
 * Application directory name.
 */

define("ACTIONS_DIRNAME", "actions");
define("TEMPLATES_DIRNAME", "templates");
define("STATICS_DIRNAME", "statics");


define("CONTEXT_KEYWORD", "___");
define("HTTP_ACTION", "action");

define("HTTP_PHRASES", [
    100 => 'Continue', 101 => 'Switching Protocols', 102 => 'Processing',
    200 => 'OK', 201 => 'Created', 202 => 'Accepted', 203 => 'Non-Authoritative Information', 204 => 'No Content', 205 => 'Reset Content', 206 => 'Partial Content', 207 => 'Multi-status', 208 => 'Already Reported',
    300 => 'Multiple Choices', 301 => 'Moved Permanently', 302 => 'Found', 303 => 'See Other', 304 => 'Not Modified', 305 => 'Use Proxy', 306 => 'Switch Proxy', 307 => 'Temporary Redirect',
    400 => 'Bad Request', 401 => 'Unauthorized', 402 => 'Payment Required', 403 => 'Forbidden', 404 => 'Not Found', 405 => 'Method Not Allowed', 406 => 'Not Acceptable', 407 => 'Proxy Authentication Required', 408 => 'Request Time-out', 409 => 'Conflict', 410 => 'Gone', 411 => 'Length Required', 412 => 'Precondition Failed', 413 => 'Request Entity Too Large', 414 => 'Request-URI Too Large', 415 => 'Unsupported Media Type', 416 => 'Requested range not satisfiable', 417 => 'Expectation Failed', 418 => 'I\'m a teapot', 422 => 'Unprocessable Entity', 423 => 'Locked', 424 => 'Failed Dependency', 425 => 'Unordered Collection', 426 => 'Upgrade Required', 428 => 'Precondition Required', 429 => 'Too Many Requests', 431 => 'Request Header Fields Too Large', 451 => 'Unavailable For Legal Reasons',
    500 => 'Internal Server Error', 501 => 'Not Implemented', 502 => 'Bad Gateway', 503 => 'Service Unavailable', 504 => 'Gateway Time-out', 505 => 'HTTP Version not supported', 506 => 'Variant Also Negotiates', 507 => 'Insufficient Storage', 508 => 'Loop Detected', 511 => 'Network Authentication Required',
]);

/**
 * Debug environment.
 */
define("DEBUG", true);
