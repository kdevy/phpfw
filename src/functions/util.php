<?php

/**
 * Kdevy framework - My original php framework.
 *
 * Copyright © 2021 kdevy. All Rights Reserved.
 */

use Framework\Action\ActionInterface;
use Framework\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Nyholm\Psr7\Factory\Psr17Factory;

/**
 * コンテキストデータを割り当て済みのファイルコンテンツを、Responseインスタンスとして返す
 *
 * @param ActionInterface $action
 * @param array $contexts
 * @return ResponseInterface
 */
function render(ActionInterface $action, array $contexts = [], ?ResponseInterface $response = null): ResponseInterface
{
    $psr17_factory = new Psr17Factory();
    $content = "";

    /**
     * TODO: 共通コンテキストの渡し方を考える。
     */
    $contexts["MODULE_NAME"] = $action->route->getModuleName();
    $contexts["ACTION_NAME"] = $action->route->getActionName();

    $contents = getAssignedFileContents($action->route->getTemplateAbsPath(), $contexts);
    if (!isset($contents)) {
        $contents = "";
        logsave("system:render", "Failed to render, the file does not exist ("
            . $action->route->getPathName() . ").", LDEBUG);
    } else {
        logsave("system:render", "Render template from ("
            . $action->route->getModuleName() . "/" . $action->route->getTemplateName() . ".html).", LDEBUG);
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
 * @param string $path
 * @param array $contexts
 * @return null|string
 */
function getAssignedFileContents(string $path, array $contexts = []): ?string
{
    return assignContexts(getFileContents($path), $contexts);
}

/**
 * セーフティなfile_get_contents
 *
 * @var string $path
 * @return string|null
 */
function getFileContents(string $path): ?string
{
    if (file_exists($path)) {
        return file_get_contents($path);
    }
    logsave("system:getFileContents", "File does not exsits ({$path}).", LDEBUG);
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
function assignContexts($text, $contexts): ?string
{
    if ($text === null || $text === false || trim($text) === "") {
        return null;
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
 * パスを元に app/module/ 配下のアクションクラスをインスタンス化して返す
 * 読み込めなかった場合はnullを返す
 *
 * TODO: これってfactoryだし、Action::createとかに持っていったほうが良いのでは。
 *
 * @param Route $route
 * @param ServerRequestInterface $request
 * @return ActionInterface|null
 */
function loadAction(Route $route, ServerRequestInterface $request): ?ActionInterface
{
    $action_class = $route->getActionClassName();
    $action_file_path = $route->getActionAbsPath();

    logsave("system:loadAction", "Load class from (/" . $route->getModuleName() . "/" . $action_class . ").", LDEBUG);
    if (!file_exists($action_file_path)) {
        logsave("system:loadAction", "Not file exists ($action_file_path).", LDEBUG);
        return null;
    }
    require_once $action_file_path;

    if (!class_exists($action_class)) {
        logsave("system:loadAction", "Not class exists ($action_class).", LDEBUG);
        return null;
    }
    $action_object = new $action_class($route);
    $action_object->setRequest($request);

    return $action_object;
}

/**
 * 文字列をキャメルケースにする
 *
 * "my-name-is-john" convert to "MyNameIsJohn"
 * "my_name_is_john" convert to "MyNameIsJohn"
 *
 * @param string $str
 * @return string
 */
function camelize(string $str): string
{
    return str_replace(['-', '_'], '', ucwords($str, ' -_'));
}

/**
 * @param string $str
 * @param string $separator
 * @return string
 */
function decamelize(string $str, string $separator = "-"): string
{
    $words = preg_split("/((?<=[a-z])(?=[A-Z])|(?=[A-Z][a-z]))/", $str);
    if ($words[0] == "") {
        array_shift($words);
    }
    return strtolower(implode($separator, $words));
}
