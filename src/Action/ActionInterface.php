<?php

namespace Kdevy\Phpfw\Action;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

interface ActionInterface
{
    /**
     * @param ServerRequestInterface $request
     * @return void
     */
    public function initialize(ServerRequestInterface $request): void;

    /**
     * @param ServerRequestInterface $request
     * @return void
     */
    public function dispatch(ServerRequestInterface $request): ResponseInterface;
}