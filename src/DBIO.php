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
            echo "DBIO Error: " . $e->getMessage();
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
                $this->stmt->bindParam($key + 1, $value);
            }
            $this->stmt->execute();
        } catch (\PDOException $e) {
            echo "DBIO Error: " . $e->getMessage();
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
            echo "DBIO Error: " . $e->getMessage();
            logsave("system:DBIO", $e, LERROR);
            exit();
        }
    }

    /**
     * @return mixed
     */
    public function fetchAssoc()
    {
        try {
            if (!isset($this->dbh) || !isset($this->stmt)) {
                throw new \PDOException("The database connection has not been established.");
            }

            return $this->stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            echo "DBIO Error: " . $e->getMessage();
            logsave("system:DBIO", $e, LERROR);
            exit();
        }
    }

    public function getColumns(string $tablename)
    {
        $sth = $this->dbh->query("SHOW COLUMNS FROM {$tablename}");
        return $sth->fetchAll(\PDO::FETCH_COLUMN);
    }
}
