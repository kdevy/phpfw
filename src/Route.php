<?php

/**
 * Kdevy framework - My original php framework.
 *
 * Copyright © 2021 kdevy. All Rights Reserved.
 */

namespace Framework;

/**
 * アプリケーションの呼び出しの起点となるルートパスを構成する
 *
 * ルートパスは二階層で表現されて、未指定のパスはindexとして扱われる。
 */
class Route
{
    private array $path;
    private array $args;

    /**
     * @param string|array $module_name
     * @param string|null $action_name
     */
    public function __construct($module_name, $action_name = null)
    {
        $this->path = self::parse($module_name, $action_name);
        $this->args = array_pop($this->path);
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
    public function getArgs(): array
    {
        return $this->args;
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
    public function setPath($module_name, $action_name = null): self
    {
        $this->path = self::parse($module_name, $action_name);
        $this->args = array_pop($this->path);
        return $this;
    }

    /**
     * @param string|array $module_name
     * @param string|null $action_name
     * @return self
     */
    public function withPath($module_name, $action_name = null): array
    {
        $this->path = self::parse($module_name, $action_name);
        $this->args = array_pop($this->path);
        return $this->path;
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
     * @param string $module_name
     * @return self
     */
    public function setModuleName(string $module_name): self
    {
        $this->path[0] = $module_name;
        return $this;
    }

    /**
     * @return string
     */
    public function getActionName(): string
    {
        return $this->path[1];
    }

    /**
     * @param string $module_name
     * @return self
     */
    public function setActionName(string $action_name): self
    {
        $this->path[1] = $action_name;
        return $this;
    }

    /**
     * @return string
     */
    public function getTemplateAbsPath(): string
    {
        return
            MODULE_DIR . DS . $this->getModuleName()
            . DS . TEMPLATES_DIRNAME . DS . $this->getTemplateName() . ".html";
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
    public function getTemplateName(): string
    {
        return strtolower(str_replace([" ", "_", "-"], "-", $this->getActionName()));
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
        $args = [];

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
        // パスの二階層目以降はargsとする
        if (count($result) > 2) {
            $args = array_slice($result, 2);
            array_splice($result, 2);
            if (end($args) === "") {
                array_pop($args);
            }
            reset($args);
        }

        if (count($result) == 1) {
            $result[1] = (isset($result[0]) && trim($result[0]) !== "" ? $result[0] : "index");
            $result[0] = "index";
        } else {
            $result[0] = (isset($result[0]) && trim($result[0]) !== "" ? $result[0] : "index");
            $result[1] = (isset($result[1]) && trim($result[1]) !== "" ? $result[1] : "index");
        }
        $result[2] = $args;
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
