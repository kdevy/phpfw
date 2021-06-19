<?php

/**
 * Kdevy framework - My original php framework.
 *
 * Copyright © 2021 kdevy. All Rights Reserved.
 */

namespace Framework;

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
        $this->destroy();

        try {
            $this->dbh = new \PDO($this->dsn, $this->user, $this->password);
            $this->dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            if (DEBUG) {
                echo "DBIO Error: " . $e->getMessage();
            } else {
                echo "Error: An error occurred while operating the database.";
            }
            logsave("system:DBIO", $e, LERROR);
            exit();
        }
    }

    /**
     * @return void
     */
    public function destroy(): void
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
            if (!isset($this->dbh)) {
                throw new \PDOException("The database connection has not been established.");
            }

            $this->stmt = $this->dbh->prepare($sql);

            foreach ($params as $key => $value) {
                $this->stmt->bindValue($key + 1, $value);
            }
            $this->stmt->execute();
            if (DEBUG) {
                logsave("database:sqlbind", self::getSqlStatement($sql, $params));
            }
        } catch (\PDOException $e) {
            if (DEBUG) {
                echo "DBIO Error: " . $e->getMessage();
            } else {
                echo "Error: An error occurred while operating the database.";
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
            if (!isset($this->dbh) || !isset($this->stmt)) {
                throw new \PDOException("The database connection has not been established.");
            }

            return $this->stmt->fetch();
        } catch (\PDOException $e) {
            if (DEBUG) {
                echo "DBIO Error: " . $e->getMessage();
            } else {
                echo "Error: An error occurred while operating the database.";
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
            if (!isset($this->dbh) || !isset($this->stmt)) {
                throw new \PDOException("The database connection has not been established.");
            }

            $res = $this->stmt->fetch(\PDO::FETCH_ASSOC);
            if (!$res) {
                return [];
            } else {
                return $res;
            }
        } catch (\PDOException $e) {
            if (DEBUG) {
                echo "DBIO Error: " . $e->getMessage();
            } else {
                echo "Error: An error occurred while operating the database.";
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
        $sth = $this->dbh->query("SHOW COLUMNS FROM {$tablename}");
        return $sth->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * 単一レコードを追加または更新する
     *
     * @param array $data
     * @return integer
     */
    public function save(array $data, string $tablename, string $pkey = "id"): int
    {
        // プライマリキー指定の場合は既存データをチェック
        $res = [];
        if (array_key_exists($pkey, $data)) {
            $this->sqlbind("SELECT {$pkey} FROM {$tablename} WHERE {$pkey}=?;", [$data[$pkey]]);
            $res = $this->fetchAssoc();
        }
        // 既存データが無い場合はインサート
        if (count($res) == 0) {
            return $this->insert($data, $tablename);
        }
        // 既存データがある場合はアップデート
        else {
            return $this->update($data, $tablename, $pkey);
        }
    }

    /**
     * 単一レコードを追加する
     *
     * @param array $data
     * @param string $tablename
     * @return integer
     */
    public function insert(array $data, string $tablename): int
    {
        // テーブルに存在しないキー名を除外
        $columns = $this->getColumns($tablename);
        $data = array_filter($data, function ($key) use ($columns) {
            return in_array($key, $columns);
        }, ARRAY_FILTER_USE_KEY);

        // SQL生成
        $sql = "INSERT INTO `{$tablename}` (" . implode(", ", array_keys($data))
            . ") VALUES (" . implode(", ", array_fill(0, count($data), "?")) . ");";
        $params = array_values($data);

        // SQL実行
        $this->sqlbind($sql, $params);

        return $this->dbh->lastInsertId();
    }

    /**
     * 単一レコードを更新する
     *
     * @param array $data
     * @param string $tablename
     * @return bool
     */
    public function update(array $data, string $tablename, string $pkey = "id"): bool
    {
        // テーブルに存在しないキー名を除外
        $columns = $this->getColumns($tablename);
        $data = array_filter($data, function ($key) use ($columns) {
            return in_array($key, $columns);
        }, ARRAY_FILTER_USE_KEY);

        // SQL生成
        $set_strs = [];
        foreach ($data as $key => $value) {
            $set_strs[] = "{$key}=?";
        }

        $sql = "UPDATE `{$tablename}` SET " . implode(", ", $set_strs) . " WHERE {$pkey}=?;";
        $params = array_values($data);
        $params[] = $data[$pkey];

        // SQL実行
        $this->sqlbind($sql, $params);

        return true;
    }

    /**
     * SQLとパラメータでアサイン済みのSQL文を取得する
     *
     * 実際に実行されるSQL文との一致を保証するものではないく、あくまでもデバッグ用。
     *
     * @param string $sql
     * @param array $params
     * @return string
     */
    static public function getSqlStatement(string $sql, array $params = []): string
    {
        $result = "";
        $exp = explode("?", $sql);

        if (count($exp) - 1 !== count($params)) {
            throw new \InvalidArgumentException("The number of parameters and placeholders do not match.");
        }

        foreach ($exp as $key => $part) {
            $value = $params[$key];

            if (is_string($value)) {
                $value = "'{$value}'";
            } elseif (is_null($value)) {
                $value = "NULL";
            }

            if (count($exp) - 1 !== $key) {
                $result .= $part . $value;
            } else {
                $result .= $part;
            }
        }

        return $result;
    }
}
