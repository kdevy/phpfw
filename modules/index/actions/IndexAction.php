<?php

/**
 * Kdevy framework - My original php framework.
 *
 * Copyright © 2021 kdevy. All Rights Reserved.
 */

use Framework\Action\TemplateAction;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class IndexAction extends TemplateAction
{
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
        return $this->render->createResponse($this->getContexts($request));
    }

    /**
     * @param ServerRequestInterface $request
     * @return array
     */
    public function getContexts(ServerRequestInterface $request): array
    {
        $contexts = [
            "TEST" => date("Y-m-d H:i:s"),
        ];
        return $contexts;
    }
}
