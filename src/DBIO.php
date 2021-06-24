<?php

/**
 * Kdevy framework - My original php framework.
 *
 * Copyright © 2021 kdevy. All Rights Reserved.
 */

namespace Framework;

use Framework\Exception\InValidFieldName;

class DBIO
{
    /**
     * @var \PDO
     */
    public \PDO $dbh;

    /**
     * @var \PDOStatement
     */
    public \PDOStatement $stmt;

    /**
     * @var string
     */
    protected string $dsn;

    /**
     * @var string
     */
    protected string $user;

    /**
     * @var string
     */
    protected string $password;

    /**
     * @param string $target
     */
    public function __construct(string $target = "default")
    {
        $this->dsn = "mysql:host=" . DB_CONFIG[$target]["HOST"]
            . ";port=" . (DB_CONFIG[$target]["PORT"] ?? 3306)
            . ";dbname=" . DB_CONFIG[$target]["NAME"]
            . ";charset=" . (DB_CONFIG[$target]["CHARSET"] ?? "utf8mb4");
        $this->user = DB_CONFIG[$target]["USER"];
        $this->password = DB_CONFIG[$target]["PASSWORD"];

        $this->connect($target);
    }

    /**
     * @param string $target
     * @return void
     */
    public function connect($target = "default"): void
    {
        $this->close();

        try {
            $this->dbh = new \PDO($this->dsn, $this->user, $this->password);
            $this->dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            if (DEBUG) {
                echo "DBIO Error: " . makeErrorMessage($e);
            } else {
                echo "Error: An error occurred.";
            }
            logsave("system:DBIO", $e, LERROR);
            exit();
        }
    }

    /**
     * @return void
     */
    public function close(): void
    {
        unset($this->dbh);
        unset($this->stmt);
    }

    /**
     * @param string $sql
     * @param array $params
     * @return void
     */
    public function sqlbind(string $sql, array $params = []): void
    {
        try {
            if (DEBUG) {
                logsave("database:sqlbind", self::getSqlStatement($sql, $params), LDEBUG);
            }
            $this->stmt = $this->dbh->prepare($sql);

            foreach ($params as $key => $value) {
                $this->stmt->bindValue($key + 1, $value);
            }
            $this->stmt->execute();
        } catch (\PDOException $e) {
            if (DEBUG) {
                echo "DBIO Error: " . makeErrorMessage($e);
            } else {
                echo "Error: An error occurred.";
            }

            logsave("system:DBIO", $e, LERROR);
            exit();
        }
    }

    /**
     * @return mixed
     */
    public function fetch()
    {
        try {
            return $this->stmt->fetch();
        } catch (\PDOException $e) {
            if (DEBUG) {
                echo "DBIO Error: " . makeErrorMessage($e);
            } else {
                echo "Error: An error occurred.";
            }
            logsave("system:DBIO", $e, LERROR);
            exit();
        }
    }

    /**
     * @return array
     */
    public function fetchAssoc(): array
    {
        try {
            $res = $this->stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$res) {
                return [];
            } else {
                return $res;
            }
        } catch (\PDOException $e) {
            if (DEBUG) {
                echo "DBIO Error: " . makeErrorMessage($e);
            } else {
                echo "Error: An error occurred.";
            }
            logsave("system:DBIO", $e, LERROR);
            exit();
        }
    }

    /**
     * @param string $tablename
     * @return array
     */
    public function getColumns(string $tablename): array
    {
        try {
            self::validateFields($tablename);
            $sth = $this->dbh->query("SHOW COLUMNS FROM {$tablename}");
            return $sth->fetchAll(\PDO::FETCH_COLUMN);
        } catch (\PDOException $e) {
            if (DEBUG) {
                echo "DBIO Error: " . makeErrorMessage($e);
            } else {
                echo "Error: An error occurred.";
            }
            logsave("system:DBIO", $e, LERROR);
            exit();
        }
    }

    /**
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function getAll(string $sql, array $params = []): array
    {
        try {
            $this->sqlbind($sql, $params);
            $result = $this->stmt->fetchAll();
            $this->stmt->closeCursor();

            return $result;
        } catch (\PDOException $e) {
            if (DEBUG) {
                echo "DBIO Error: " . makeErrorMessage($e);
            } else {
                echo "Error: An error occurred.";
            }
            logsave("system:DBIO", $e, LERROR);
            exit();
        }
    }

    /**
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function getAllAssoc(string $sql, array $params = []): array
    {
        try {
            $this->sqlbind($sql, $params);
            $result = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
            $this->stmt->closeCursor();

            return $result;
        } catch (\PDOException $e) {
            if (DEBUG) {
                echo "DBIO Error: " . makeErrorMessage($e);
            } else {
                echo "Error: An error occurred.";
            }
            logsave("system:DBIO", $e, LERROR);
            exit();
        }
    }

    /**
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function getRow(string $sql, array $params = []): array
    {
        try {
            $this->sqlbind($sql, $params);
            $result = $this->stmt->fetch();
            $this->stmt->closeCursor();

            return $result;
        } catch (\PDOException $e) {
            if (DEBUG) {
                echo "DBIO Error: " . makeErrorMessage($e);
            } else {
                echo "Error: An error occurred.";
            }
            logsave("system:DBIO", $e, LERROR);
            exit();
        }
    }

    /**
     * @param string $sql
     * @param array $params
     * @return array
     */
    public function getRowAssoc(string $sql, array $params = []): array
    {
        try {
            $this->sqlbind($sql, $params);
            $result = $this->stmt->fetch(\PDO::FETCH_ASSOC);
            $this->stmt->closeCursor();

            return $result;
        } catch (\PDOException $e) {
            if (DEBUG) {
                echo "DBIO Error: " . makeErrorMessage($e);
            } else {
                echo "Error: An error occurred.";
            }
            logsave("system:DBIO", $e, LERROR);
            exit();
        }
    }

    /**
     * @param string $sql
     * @param array $params
     * @param integer $key
     * @return mixed
     */
    public function getOne($key, string $sql, array $params)
    {
        $result = $this->getRow($sql, $params);
        return $result[$key] ?? null;
    }

    /**
     * SQLとパラメータでアサイン済みのSQL文を取得する
     *
     * 実際に実行されるSQL文との一致を保証するものではなく、あくまでもデバッグ用。
     *
     * @param string $sql
     * @param array $params
     * @return string
     */
    static public function getSqlStatement(string $sql, array $params = []): string
    {
        $result = "";
        $exp = explode("?", $sql);

        // TODO: nullが値として渡された時にここでエラーになる
        // if (count($exp) - 1 !== count($params)) {
        //     throw new \InvalidArgumentException("The number of parameters and placeholders do not match.");
        // }

        foreach ($exp as $key => $part) {
            if (count($exp) - 1 != $key) {
                $value = $params[$key];

                if (is_string($value)) {
                    $value = "'{$value}'";
                } elseif (is_null($value)) {
                    $value = "NULL";
                }
                $result .= $part . $value;
            } else {
                $result .= $part;
            }
        }

        return $result;
    }

    /**
     * 配列を元にWhere句を生成
     *
     * ['field_name' => 'value'] or ['field_name' => ['value', 'condition', 'separator']]
     *
     * @param array $where_params
     * @return array
     */
    static public function makeWhereString(array $where_params): array
    {
        $result = ["", []];

        if (empty($where_params)) {
            return $result;
        }

        foreach ($where_params as $key => $param) {
            self::validateFields($key);
            $value = $param;
            $condition = "=";
            $separator = "AND";

            if (is_array($param)) {
                $value = $param[0];
                $condition = $param[1] ?? "=";
                $separator = $param[2] ?? "AND";
            }

            if ($condition === false) {
                $result[0] .= "{$key} {$value} {$separator} ";
            } elseif (is_null($value)) {
                $result[0] .= "{$key} IS NULL {$separator} ";
            } else {
                $result[0] .= "{$key} {$condition} ? {$separator} ";
                $result[1][] = $value;
            }
        }
        $result[0] = substr($result[0], 0, -4);
        return $result;
    }

    /**
     * 原則動的に渡されたフィールド名とテーブル名はこれを通す
     *
     * @param string|array $fieldnames
     * @return bool
     * @throws InValidFieldName
     */
    static public function validateFields($fieldnames): bool
    {
        try {
            if (is_array($fieldnames)) {
                foreach ($fieldnames as $name) {
                    if (preg_match('/^[_a-zA-Z0-9]+$/', $name) !== 1) {
                        throw new InValidFieldName();
                    }
                }
            } else {
                if (preg_match('/^[_a-zA-Z0-9]+$/', $fieldnames) !== 1) {
                    throw new InValidFieldName();
                }
            }
            return true;
        } catch (\PDOException  $e) {
            if (DEBUG) {
                echo "DBIO Error: " . makeErrorMessage($e);
            } else {
                echo "Error: An error occurred.";
            }

            logsave("system:DBIO", $e, LERROR);
            exit();
        }
    }
}
