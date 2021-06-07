<?php

/**
 * Kdevy framework - My original php framework.
 *
 * Copyright © 2021 kdevy. All Rights Reserved.
 */

use Framework\Action\Action;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class _DefaultAction extends Action
{
    /**
     * @param ServerRequestInterface $request
     * @return void
     */
    public function initialize(ServerRequestInterface $request): void
    {
    }

    /**
     * @param ServerRequestInterface $request
     * @return void
     */
    public function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        $contexts = [];
        return render($this, $contexts);
    }
}
