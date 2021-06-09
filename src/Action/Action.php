<?php

/**
 * Kdevy framework - My original php framework.
 *
 * Copyright © 2021 kdevy. All Rights Reserved.
 */

namespace Framework\Action;

use Framework\UserContainer;
use Framework\Render;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class Action implements ActionInterface
{
    protected Render $render;

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

    public function __construct($path)
    {
        $this->setPath($path);
        $this->render = new Render();
        $this->render->setPath($path);
    }

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
     * @param array|string $module_name
     * @param string $action_name
     * @return void
     */
    public function setPath($module_name, string $action_name = null): void
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
