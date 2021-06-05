<?php

namespace Framework\Exception;

use RuntimeException;

class HttpError extends \Exception
{
    public function __construct(int $code)
    {
        if (!isset(HTTP_ERROR_CODE[$code])) {
            throw new RuntimeException("Bound the invalid http error code ({$code}).");
        }
        parent::__construct("", $code);
    }
}