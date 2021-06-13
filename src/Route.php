<?php

/**
 * Kdevy framework - My original php framework.
 *
 * Copyright © 2021 kdevy. All Rights Reserved.
 */

namespace Framework;

use InvalidArgumentException;

/**
 * アプリケーションの呼び出しの起点となるルートパスを構成する
 *
 * ルートパスは二階層で表現されて、未指定のパスはindexとして扱われる。
 */
class Route
{
    private array $path;

    /**
     * @param string|array $module_name
     * @param string|null $action_name
     */
    public function __construct($module_name, $action_name = null)
    {
        $this->path = self::parse($module_name, $action_name);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getPathName();
    }

    /**
     * @return array
     */
    public function getPath(): array
    {
        return $this->path;
    }

    /**
     * @param string|array $module_name
     * @param string|null $action_name
     * @return self
     */
    public function withPath($module_name, $action_name): self
    {
        $route = clone $this;
        $route->path = self::parse($module_name, $action_name);
        return $route;
    }

    /**
     * @return string
     */
    public function getPathName(): string
    {
        return "/{$this->path[0]}/{$this->path[1]}";
    }

    /**
     * @return string
     */
    public function getModuleName(): string
    {
        return $this->path[0];
    }

    /**
     * @return string
     */
    public function getActionName(): string
    {
        return $this->path[1];
    }

    /**
     * @return string
     */
    public function getTemplateAbsPath(): string
    {
        return
            MODULE_DIR . DS . $this->getModuleName()
            . DS . TEMPLATES_DIRNAME . DS . strtolower($this->getActionName()) . ".html";
    }

    /**
     * @return string
     */
    public function getActionClassName(): string
    {
        return camelize($this->getActionName()) . "Action";
    }

    /**
     * @return string
     */
    public function getActionAbsPath(): string
    {
        return MODULE_DIR . DS . $this->getModuleName()
            . DS . ACTIONS_DIRNAME . DS . $this->getActionClassName() . ".php";
    }

    /**
     * @param string|array $module_name
     * @param string|null $action_name
     * @return array
     * @throws \InvalidArgumentException
     */
    static public function parse($module_name, $action_name = null): array
    {
        $result = null;

        // from array
        if (is_array($module_name)) {
            $result = $module_name;
        }
        // from string
        elseif (is_string($module_name)) {
            if (strpos($module_name, "?") !== false) {
                $result = explode("/", explode("?", $module_name)[0]);
                array_shift($result);
            } elseif (is_string($module_name) && !isset($action_name)) {
                $result = explode("/", $module_name);
                array_shift($result);
            } elseif (is_string($module_name) && is_string($action_name)) {
                $result = [$module_name, $action_name];
            }
        }

        if (!isset($result)) {
            throw new \InvalidArgumentException("Arguments that cannot be parsed.");
        }
        if (count($result) > 2) {
            throw new \InvalidArgumentException("Root path is up to two hierarchy.");
        }

        $result[0] = (isset($result[0]) && trim($result[0]) !== "" ? $result[0] : "index");
        $result[1] = (isset($result[1]) && trim($result[1]) !== "" ? $result[1] : "index");
        return $result;
    }

    /**
     * @param string|array $module_name
     * @param string|null $action_name
     * @return self
     */
    static public function create($module_name, $action_name = null): self
    {
        $static = new self($module_name, $action_name);
        return $static;
    }
}
