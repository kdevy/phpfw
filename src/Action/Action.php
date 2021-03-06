<?php

namespace Kdevy\Phpfw\Action;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Kdevy\Phpfw\UserContainer;

abstract class Action implements ActionInterface
{
    /**
     * @var ServerRequestInterface
     */
    public ServerRequestInterface $request;

    /**
     * @var string
     */
    public string $module_name;

    /**
     * @var string
     */
    public string $action_name;

    /**
     * @var UserContainer
     */
    public UserContainer $user;

    /**
     * @param ServerRequestInterface $request
     * @return void
     */
    abstract public function initialize(ServerRequestInterface $request): void;

    /**
     * @param ServerRequestInterface $request
     * @return void
     */
    abstract public function dispatch(ServerRequestInterface $request): ResponseInterface;
}
