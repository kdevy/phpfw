<?php

namespace Kdevy\Phpfw\Exception;

class HttpError extends \Exception
{
    public function __construct(int $code)
    {
        parent::__construct("", $code);
    }
}