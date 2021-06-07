<?php

/**
 * Kdevy framework - My original php framework.
 *
 * Copyright © 2021 kdevy. All Rights Reserved.
 */

use Framework\Action\ActionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Nyholm\Psr7\Factory\Psr17Factory;

/**
 * コンテキストデータを割り当て済みのファイルコンテンツを、Responseインスタンスとして返す
 *
 * @param string $filepath
 * @param array $contexts
 * @return ResponseInterface
 */
function render(ActionInterface $action, array $contexts = [], ?ResponseInterface $response = null): ResponseInterface
{
    $psr17_factory = new Psr17Factory();
    $content = "";
    $filepath = $action->getPath();
    /**
     * TODO: 共通コンテキストの渡し方を考える。
     */
    $contexts["MODULE_NAME"] = $filepath[0];
    $contexts["ACTION_NAME"] = $filepath[1];

    $contents = getAssignedFileContents($filepath, $contexts);
    if (!isset($contents)) {
        $contents = "";
        logsave("system:render", "Failed to render, the file does not exist ({$filepath}).", LDEBUG);
    } else {
        logsave("system:render", "Render from ({$filepath[0]}/{$filepath[1]}).", LDEBUG);
    }

    return createContentsResponse($contents, $response);
}

/**
 * 文字列でレスポンスオブジェクトを生成
 *
 * @param string|null $content
 * @param null|ResponseInterface $response
 * @return ResponseInterface
 */
function createContentsResponse(?string $contents, ResponseInterface $response = null): ResponseInterface
{
    if ($contents === null || trim($contents) === "") {
        $contents = "";
        logsave("system:createContentsResponse", "Empty contents was passed.", LDEBUG);
    }
    $psr17_factory = new Psr17Factory();
    $respose_body = $psr17_factory->createStream($contents);

    if (!isset($response)) {
        $response = $psr17_factory->createResponse(200);
    }
    $response = $response->withBody($respose_body);
    return $response;
}

/**
 * コンテキストを割り当てたファイルコンテンツを取得
 *
 * 存在しないファイル場合はnullを返す
 *
 * @param string|array $path
 * @param array $contexts
 * @return null|string
 */
function getAssignedFileContents($path, array $contexts = []): ?string
{
    $path = getTmplAbsPath($path);

    if (file_exists($path)) {
        return assignContexts(file_get_contents($path), $contexts);
    }
    logsave("system:getAssignedFileContents", "File does not exsits ({$path}).", LDEBUG);
    return null;
}

/**
 * コンテキストデータをテキスト内の埋め込みキーワードに割り当てる
 *
 * 割り当てられなかった埋め込みキーワードは空文字に置き換えられる。
 *
 * @param mixed $text
 * @return string
 */
function assignContexts($text, $contexts): string
{
    if ($text === null || $text === false || trim($text) === "") {
        return "";
    }

    $match_strs = [];
    preg_match_all("/\\" . CONTEXT_KEYWORD . ".+?\\" . CONTEXT_KEYWORD . "/", $text, $match_strs);

    foreach ($match_strs[0] as $match_str) {
        $keyword = str_replace(CONTEXT_KEYWORD, "", $match_str);

        if ($keyword == "") {
            continue;
        }
        if (array_key_exists($keyword, $contexts)) {
            $text = str_replace([$match_str], $contexts[$keyword], $text);
        } else {
            $text = str_replace([$match_str], "", $text);
        }
    }
    return $text;
}

/**
 * パス文字列を解析してモジュール名とアクション名を返す
 *
 * @param string|array $path
 * @return array|null
 */
function parsePath($path): array
{
    if (is_array($path)) {
        return $path;
    }
    if (empty($path)) {
        return null;
    }
    $module_name = null;
    $action_name = null;

    if (strpos($path, "?") !== false) {
        $path = explode("/", explode("?", $path)[0]);
    } else {
        $path = explode("/", $path);
    }

    if (count($path) == 2) {
        $module_name = "index";
        $action_name = (isset($path[1]) && $path[1] !== "" ? $path[1] : "index");
    } else {
        $module_name = $path[1];
        $action_name = (isset($path[2]) && $path[2] !== "" ? $path[2] : "index");;
    }
    return [$module_name, $action_name];
}

/**
 * テンプレートファイルの絶対パスを返す
 * 絶対パス、"/module_name/action_name"、["module_name", "action_name"]を渡すことが可能
 *
 * 返すパスが実際に存在するかはチェックしない
 *
 * @param string|array $path
 * @return string
 */
function getTmplAbsPath($path): string
{
    // abs path
    if (is_string($path) && file_exists($path)) {
        return $path;
    }
    // /{module name}/{action name} or [{module name}, {action name}]
    else {
        $path = parsePath($path);
        return MODULE_DIR . DS . $path[0] . DS . TEMPLATES_DIRNAME . DS . strtolower($path[1]) . ".html";
    }
}

/**
 * アクションファイルの絶対パスを返す
 * 絶対パス、"/module_name/action_name"、["module_name", "action_name"]を渡すことが可能
 *
 * 返すパスが実際に存在するかはチェックしない
 *
 * @param string|array $path
 * @return string
 */
function getActionAbsPath($path): string
{
    // abs path
    if (is_string($path) && file_exists($path)) {
        return $path;
    }
    // /{module name}/{action name} or [{module name}, {action name}]
    else {
        list($module_name, $action_name) = parsePath($path);
        $action_class = camelize($action_name) . "Action";
        return MODULE_DIR . DS . $module_name . DS . ACTIONS_DIRNAME . DS . $action_class . ".php";
    }
}

/**
 * パスを元に app/module/ 配下のアクションクラスをインスタンス化して返す
 * 読み込めなかった場合はnullを返す
 *
 * TODO: これってfactoryだし、Action::createとかに持っていったほうが良いのでは。
 *
 * @param string|array $path
 * @param ServerRequestInterface $request
 * @return ActionInterface|null
 */
function loadAction($path, ServerRequestInterface $request): ?ActionInterface
{
    list($module_name, $action_name) = parsePath($path);
    $action_file_path = getActionAbsPath([$module_name, $action_name]);

    $action_class = camelize($action_name) . "Action";

    logsave("system:loadAction", "Load class from ({$module_name}/{$action_class}).", LDEBUG);
    if (!file_exists($action_file_path)) {
        logsave("system:loadAction", "Not file exists ($action_file_path).", LDEBUG);
        return null;
    }
    require_once $action_file_path;

    if (!class_exists($action_class)) {
        logsave("system:loadAction", "Not class exists ($action_class).", LDEBUG);
        return null;
    }
    $action_object = new $action_class();
    $action_object->setPath($module_name, $action_name);
    $action_object->setRequest($request);

    return $action_object;
}

/**
 * 文字列をキャメルケースにする
 *
 * "my name is john" convert to "MyNameIsJohn"
 * "my-name-is-john" convert to "MyNameIsJohn"
 * "my_name_is_john" convert to "MyNameIsJohn"
 *
 * @param string $str
 * @return string
 */
function camelize(string $str): string
{
    return str_replace([' ', '-', '_'], '', ucwords($str, ' -_'));
}
