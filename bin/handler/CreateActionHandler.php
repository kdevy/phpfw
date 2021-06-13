<?php

/**
 * Kdevy framework - My original php framework.
 *
 * Copyright © 2021 kdevy. All Rights Reserved.
 */

namespace Framework\Command\Handler;

use Framework\Command\Handler\CreatePathHandler;
use Framework\Route;

class CreateActionHandler extends CreatePathHandler
{
    /**
     * @return void
     */
    public function main(): void
    {
        if (count($this->argv) != 2) {
            echo "Error: Please specify the module name and action name." . PHP_EOL;
            exit(1);
        }

        $route = Route::create($this->argv);
        $action_file_path = $route->getActionAbsPath();
        $html_template_path = $route->getTemplateAbsPath();
        $action_class = $route->getActionClassName();

        if (file_exists($action_file_path)) {
            echo "Error: Action " . $route->getActionName() . " that already exists." . PHP_EOL;
            exit(1);
        }

        if (!file_exists(MODULE_DIR . DS . $route->getModuleName())) {
            passthru(BIN_DIR . DS . "manage create-module " . $route->getModuleName());
        }

        // create module directory.
        passthru("cp -p " . ACTION_TEMPLATE_DIR . DS . ACTIONS_DIRNAME . DS . "{$this->template_action_class}.php " .
            " " . $action_file_path);
        // replace class name.
        passthru("sed -i 's/{$this->template_action_class}/{$action_class}/g' " . $action_file_path);

        // api action not create html file.
        if (!in_array($this->arguments["type"], ["api"])) {
            if (!file_exists($html_template_path)) {
                passthru("touch " . $html_template_path);
            } else {
                echo "Skip: Html template that already exists." . PHP_EOL;
            }
        }

        echo "Creation of the action '" . $route->getActionName() . "' was successfully." . PHP_EOL;
    }
}
