<?php

/**
 * Kdevy framework - My original php framework.
 *
 * Copyright © 2021 kdevy. All Rights Reserved.
 */

namespace Framework;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Framework\Route;

class Render
{
    private ?Route $template_route = null;

    private ?Route $route = null;

    const TEMPLATE_CONTEXT_NAME = "TEMPLATE_MAIN_CONTENTS";

    /**
     * @param Route $route
     * @return void
     */
    public function setPath(Route $route): void
    {
        $this->route = $route;
    }

    /**
     * TODO
     * @param array|string $template_path
     * @return void
     */
    public function useTemplate($template_path): void
    {
        $this->template_route = Route::create($template_path);
    }

    /**
     * @param array $contexts
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function createResponse(array $contexts = [], ResponseInterface $response = null): ResponseInterface
    {
        $template = CONTEXT_KEYWORD . self::TEMPLATE_CONTEXT_NAME . CONTEXT_KEYWORD;
        $psr17_factory = new Psr17Factory();
        if (!isset($response)) {
            $response = $psr17_factory->createResponse(200);
        }
        if (isset($this->template_route)) {
            $template = getFileContents($this->template_route);
            if (!isset($template)) {
                throw new \RuntimeException("Template file does not exsits ({$this->template_route}).");
            }
        }
        $template = str_replace(
            CONTEXT_KEYWORD . self::TEMPLATE_CONTEXT_NAME . CONTEXT_KEYWORD,
            getFileContents($this->route),
            $template
        );
        $template = assignContexts($template, $contexts);
        return createContentsResponse($template, $response);
    }
}
