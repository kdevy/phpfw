<?php

/**
 * Kdevy framework - My original php framework.
 *
 * Copyright © 2021 kdevy. All Rights Reserved.
 */

namespace Framework\Action;

use Framework\Renderer;
use Framework\Route;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class Action implements ActionInterface
{
    protected Renderer $render;

    /**
     * @var ServerRequestInterface
     */
    public ServerRequestInterface $request;

    /**
     * @var Route
     */
    public Route $route;

    public function __construct(Route $route)
    {
        $this->setPath($route);
        $this->render = new Renderer();
        $this->render->setPath($route);
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
     * @return Route
     */
    public function getPath(): Route
    {
        return $this->route;
    }

    /**
     * @param Route $route
     * @return void
     */
    public function setPath(Route $route): void
    {
        $this->route = $route;
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
