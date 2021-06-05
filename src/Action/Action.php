<?php

namespace Framework\Action;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Framework\UserContainer;

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
     * @param ServerRequestInterface $request
     * @return void
     */
    abstract public function initialize(ServerRequestInterface $request): void;

    /**
     * @param ServerRequestInterface $request
     * @return void
     */
    abstract public function dispatch(ServerRequestInterface $request): ResponseInterface;

    /**
     * @return array
     */
    public function getPath(): array
    {
        return [$this->module_name, $this->action_name];
    }

    /**
     * @param mixed $path
     * @return void
     */
    public function setPath($module_name, string $action_name=null): void
    {
        if (isset($action_name)) {
            $path = [$module_name, $action_name];
        } else {
            $path = $module_name;
        }
        $path = parsePath($path);
        $this->module_name = $path[0];
        $this->action_name = $path[1];
    }

    /**
     * @param ServerRequestInterface $request
     * @return void
     */
    public function setRequest(ServerRequestInterface $request): void
    {
        $this->request = $request;
    }
}
