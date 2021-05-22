<?php

use Kdevy\Phpfw\Action\Action;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class IndexAction extends Action
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
        $contexts = ["DATE" => date("Y/m/d H:i:s")];
        return render([$this->module_name, $this->action_name], $contexts);
    }
}