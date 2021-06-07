<?php

/**
 * Kdevy framework - My original php framework.
 *
 * Copyright © 2021 kdevy. All Rights Reserved.
 */

namespace Framework\Command\Handler;


use Framework\Command\Handler\CreatePathHandler;

class CreateModuleHandler extends CreatePathHandler
{
    /**
     * @return void
     */
    public function main(): void
    {
        if (count($this->argv) != 1) {
            echo "Please specify the module name." . PHP_EOL;
            exit(1);
        }

        list($module_name) = $this->argv;

        if (file_exists(MODULE_DIR . DS . $module_name)) {
            echo "Error: Module {$module_name} that already exists." . PHP_EOL;
            exit(1);
        }
        passthru("cp -r " . ACTION_TEMPLATE_DIR . " " . MODULE_DIR . DS . $module_name);
        foreach (self::ACTION_TYPES as $type) {
            passthru("rm -rf " . MODULE_DIR . DS . $module_name . DS . ACTIONS_DIRNAME . DS . "_" . camelize($type) . "Action.php");
        }

        echo "Creation of the module '{$module_name}' was successfully." . PHP_EOL;
    }
}
