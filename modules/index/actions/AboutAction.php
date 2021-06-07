<?php

/**
 * Kdevy framework - My original php framework.
 *
 * Copyright © 2021 kdevy. All Rights Reserved.
 */

use Framework\Action\TemplateAction;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Nyholm\Psr7\Factory\Psr17Factory;

class AboutAction extends TemplateAction
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
     * @return ResponseInterface
     */
    public function get(ServerRequestInterface $request): ResponseInterface
    {
        $tmpl_contexts = [
            "TITLE" => "about",
            "MAIN_C_TITLE" => "About",
            "MAIN_C_CONTENTS" => getAssignedFileContents(getTmplAbsPath($this->getPath()), [])
        ];
        $contents = getAssignedFileContents(getTmplAbsPath("/common/default"), $tmpl_contexts);
        return createContentsResponse($contents);
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function post(ServerRequestInterface $request): ResponseInterface
    {
        return render($this, $this->getContexts($request));
    }

    /**
     * @param ServerRequestInterface $request
     * @return array
     */
    public function getContexts(ServerRequestInterface $request): array
    {
        return [];
    }
}
