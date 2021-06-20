<?php

/**
 * Kdevy framework - My original php framework.
 *
 * Copyright © 2021 kdevy. All Rights Reserved.
 */

namespace Framework\Exception;

class InValidFieldName extends \PDOException
{
    public function __construct()
    {
        parent::__construct("Invalid field name was detected.");
    }
}
