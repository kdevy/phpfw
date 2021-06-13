<?php

/**
 * Kdevy framework - My original php framework.
 *
 * Copyright © 2021 kdevy. All Rights Reserved.
 */

namespace Framework\Command\Handler;

use Framework\Command\Handler\Handler;

class CreatePathHandler extends Handler
{
    protected string $template_action_class;

    const ACTION_TYPES = [
        "default",
        "api",
        "template"
    ];

    /**
     * @param array $arguments
     * @param array $argv
     */
    public function __construct(array $arguments, array $argv)
    {
        parent::__construct($arguments, $argv);

        $this->arguments["type"] = $this->arguments["type"] ?? "default";
        if (!in_array($this->arguments["type"], self::ACTION_TYPES)) {
            echo "An unknown type {$this->arguments["type"]} was passed." . PHP_EOL;
            exit(1);
        }
        $this->template_action_class = "_" . camelize($this->arguments["type"]) . "Action";
    }
}
