<?php

/**
 * Kdevy framework - My original php framework.
 *
 * Copyright © 2021 kdevy. All Rights Reserved.
 */

use Framework\Action\TemplateAction;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class AboutAction extends TemplateAction
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
            "TITLE" => "about",
            "MAIN_C_TITLE" => "About",
        ];
        return $contexts;
    }
}
