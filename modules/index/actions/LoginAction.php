<?php

/**
 * Kdevy framework - My original php framework.
 *
 * Copyright © 2021 kdevy. All Rights Reserved.
 */

use Framework\Action\TemplateAction;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class LoginAction extends TemplateAction
{
    private array $messages = [];

    /**
     * @param ServerRequestInterface $request
     * @return void
     */
    public function initialize(ServerRequestInterface $request): void
    {
        $this->render->useTemplate("/common/default");
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function get(ServerRequestInterface $request): ResponseInterface
    {
        return $this->render->createResponse($this->getContexts($request));
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function post(ServerRequestInterface $request): ResponseInterface
    {
        $data = filter_input_array(INPUT_POST);
        if (empty($data["username"])) {
            $this->messages["username"][] = "ユーザ名を入力して下さい。";
        }
        if (empty($this->messages["username"]) && (strlen($data["username"]) < 5 || strlen($data["username"]) > 20)) {
            $this->messages["username"][] = "ユーザ名は５文字以上２０文字以下でなければいけません。";
        }
        if (empty($this->messages["username"]) && !preg_match("/^[a-zA-Z0-9#$%&+-_]+$/", $data["username"])) {
            $this->messages["username"][] = "ユーザ名は半角英数字、記号（#$%&+-_）のみ使用可能です。";
        }
        if (empty($data["password"])) {
            $this->messages["password"][] = "パスワードを入力して下さい。";
        }
        if (empty($this->messages["password"]) && (strlen($data["password"]) < 8 || strlen($data["password"]) > 20)) {
            $this->messages["password"][] = "パスワードは８文字以上２０文字以下でなければいけません。";
        }
        if (empty($this->messages["password"]) && !preg_match("/^[a-zA-Z0-9#$%&+-_]+$/", $data["password"])) {
            $this->messages["password"][] = "パスワード名は半角英数字、記号（#$%&+-_）のみ使用可能です。";
        }
        return $this->render->createResponse($this->getContexts($request));
    }

    /**
     * @param ServerRequestInterface $request
     * @return array
     */
    public function getContexts(ServerRequestInterface $request): array
    {
        $contexts = [
            "PAGE_TITLE" => "Login",
        ];

        if (!empty($this->messages)) {
            foreach ($this->messages as $key => $message) {
                $contexts[strtoupper("{$key}_MESSAGE")] = implode("<br>", $message);
            }
        }
        return $contexts;
    }
}
