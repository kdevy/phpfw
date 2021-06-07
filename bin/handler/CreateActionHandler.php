<?php

/**
 * Kdevy framework - My original php framework.
 *
 * Copyright © 2021 kdevy. All Rights Reserved.
 */

namespace Framework\Command\Handler;

use Framework\Command\Handler\CreatePathHandler;

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

        list($module_name, $action_name) = $this->argv;
        $action_class = camelize($action_name) . "Action";

        if (file_exists(MODULE_DIR . DS . $module_name . DS . ACTIONS_DIRNAME . DS  . "{$action_class}.php")) {
            echo "Error: Action {$action_name} that already exists." . PHP_EOL;
            exit(1);
        }

        if (!file_exists(MODULE_DIR . DS . $module_name)) {
            passthru(BIN_DIR . DS . "manage create-module {$module_name}");
        }

        passthru("cp -p " . ACTION_TEMPLATE_DIR . DS . ACTIONS_DIRNAME . DS . "{$this->template_action_class}.php " .
            " " . MODULE_DIR . DS . $module_name . DS . ACTIONS_DIRNAME . DS  . "{$action_class}.php");
        passthru("sed -i 's/{$this->template_action_class}/{$action_class}/g' " . MODULE_DIR . DS . $module_name . DS . ACTIONS_DIRNAME . DS  . "{$action_class}.php");
        $html_template_path = MODULE_DIR . DS . $module_name . DS . TEMPLATES_DIRNAME . DS . str_replace([" ", "-", "_"], "", $action_name) . ".html";
        if (file_exists($html_template_path)) {
            echo "Skip: Html template that already exists." . PHP_EOL;
        }
        passthru("touch " . $html_template_path);

        echo "Creation of the action '{$action_name}' was successfully." . PHP_EOL;
    }
}
