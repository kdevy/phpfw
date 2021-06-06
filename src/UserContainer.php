<?php

/**
 * Kdevy framework - My original php framework.
 *
 * Copyright © 2021 kdevy. All Rights Reserved.
 */

namespace Framework;

class UserContainer
{
    /**
     * @var bool
     */
    private bool $is_login;

    /**
     * @var int
     */
    public string $userid;

    /**
     * @return boolean
     */
    public function isLogin(): bool
    {
        return $this->is_login;
    }

    /**
     * @return boolean
     */
    public function validateAuth(): bool
    {
        if ($this->isLogin()) {
            return true;
        } else {
            header("Location: /");
            exit();
        }
    }

    /**
     * @param string $userid
     * @param string $password
     * @return boolean
     */
    public function authenticate(string $userid, string $password): bool
    {
        return true;
    }
}
