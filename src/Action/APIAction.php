<?php

/**
 * Kdevy framework - My original php framework.
 *
 * Copyright © 2021 kdevy. All Rights Reserved.
 */

namespace Framework\Action;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Nyholm\Psr7\Factory\Psr17Factory;

abstract class APIAction extends Action
{
    /**
     * @var string|null
     */
    protected ?string $action;

    /**
     * @param ServerRequestInterface $request
     * @return void
     */
    abstract public function initialize(ServerRequestInterface $request): void;

    /**
     * @param ServerRequestInterface $request
     * @return void
     */
    public function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        $this->action = filter_input(INPUT_GET, HTTP_ACTION) ?? filter_input(INPUT_POST, HTTP_ACTION);
        $ignores = ["initialize", "dispatch"];

        if (!isset($this->action) || in_array($this->action, $ignores) || !method_exists($this, $this->action)) {
            return (new Psr17Factory())->createResponse(404);
        }

        return call_user_func([$this, $this->action], $request);
    }
}
