<?php

/**
 * Kdevy framework - My original php framework.
 *
 * Copyright © 2021 kdevy. All Rights Reserved.
 */

namespace Framework;

/**
 * $_SESSION helper
 */
class SessionHelper
{
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            throw new \RuntimeException("Session is not available, you need to run session_start() to use the helper.");
        }
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        if (!$this->exist($key)) {
            return $default;
        }
        return $_SESSION[$key];
    }

    /**
     * @param array $keys
     * @return array
     */
    public function getData(array $keys): array
    {
        $result = [];

        foreach ($keys as $key) {
            $result[$key] = $this->get($key);
        }
        return $result;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * @param array $data
     * @return void
     */
    public function setData(array $data): void
    {
        foreach ($data as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * @param string $key
     * @return boolean
     */
    public function exist(string $key): bool
    {
        return array_key_exists($key, $_SESSION);
    }

    /**
     * @param string $name
     * @return void
     */
    public function delete(string $name): void
    {
        if ($this->exist($name)) {
            unset($_SESSION[$name]);
        }
    }

    /**
     * @param string $name
     * @return void
     */
    public function __get(string $name)
    {
        return $this->get($name);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set(string $name, $value): void
    {
        $this->set($name, $value);
    }

    /**
     * @param string $name
     * @return boolean
     */
    public function __isset(string $name): bool
    {
        return $this->exist($name);
    }

    /**
     * @param string $name
     * @return void
     */
    public function __unset(string $name): void
    {
        $this->delete($name);
    }
}
