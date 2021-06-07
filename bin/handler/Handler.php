<?php

/**
 * Kdevy framework - My original php framework.
 *
 * Copyright © 2021 kdevy. All Rights Reserved.
 */

namespace Framework\Command\Handler;

class Handler
{
    protected array $arguments;
    protected array $argv;

    /**
     * @param array $arguments
     * @param array $argv
     */
    public function __construct(array $arguments, array $argv)
    {
        $this->arguments = $arguments;
        $this->argv = $argv;
    }
}
