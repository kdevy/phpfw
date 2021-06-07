<?php

/**
 * Kdevy framework - My original php framework.
 *
 * Copyright © 2021 kdevy. All Rights Reserved.
 */

use Framework\Action\APIAction;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class _ApiAction extends APIAction
{
    /**
     * @param ServerRequestInterface $request
     * @return void
     */
    public function initialize(ServerRequestInterface $request): void
    {
    }
}
