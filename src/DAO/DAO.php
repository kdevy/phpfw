<?php


/**
 * Kdevy framework - My original php framework.
 *
 * Copyright © 2021 kdevy. All Rights Reserved.
 */

namespace Framework\DAO;

use \Framework\DBIO;

class DAO
{
    protected DBIO $db;

    public function __construct()
    {
        $this->db = new DBIO();
    }

    /**
     * @param array $where_params
     * @param string $tablename
     * @param string|null $orderby
     * @return array
     */
    public function getDataAllAssoc(array $where_params = [], string $tablename, ?string $orderby = null): array
    {
        list($where_str, $params) = DBIO::makeWhereString($where_params);

        $where_str = (!empty($where_str) ? "WHERE {$where_str}" : "");
        $sql = "SELECT * FROM `{$tablename}` {$where_str} {$orderby};";

        return $this->db->getAllAssoc($sql, $params);
    }

    /**
     * @param array $where_params
     * @param string $tablename
     * @return array
     */
    public function getDataRowAssoc(array $where_params = [], string $tablename): array
    {
        list($where_str, $params) = DBIO::makeWhereString($where_params);

        $where_str = (!empty($where_str) ? "WHERE {$where_str}" : "");
        $sql = "SELECT * FROM `{$tablename}` {$where_str};";

        return $this->db->getRowAssoc($sql, $params);
    }

    /**
     * @param array $where_params
     * @param string $tablename
     * @return mixed
     */
    public function getDataOne($key, array $where_params = [], string $tablename)
    {
        list($where_str, $params) = DBIO::makeWhereString($where_params);

        $where_str = (!empty($where_str) ? "WHERE {$where_str}" : "");
        $sql = "SELECT * FROM `{$tablename}` {$where_str};";

        return $this->db->getOne($key, $sql, $params);
    }

    /**
     * 単一レコードを追加または更新する
     *
     * @param array $data
     * @return integer
     */
    public function save(array $data, string $tablename, string $pkey = "id"): int
    {
        DBIO::validateFields([$tablename, $pkey]);
        // プライマリキー指定の場合は既存データをチェック
        $is_new = true;
        if (array_key_exists($pkey, $data)) {
            $this->db->sqlbind("SELECT count({$pkey}) FROM {$tablename} WHERE {$pkey}=?;", [$data[$pkey]]);
            $is_new = (intval($this->db->fetchAssoc()[0]) === 0 ? true : false);
        }
        // 既存データが無い場合はインサート
        if ($is_new) {
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
        DBIO::validateFields($tablename);
        // テーブルに存在しないキー名を除外
        $columns = $this->db->getColumns($tablename);
        $data = array_filter($data, function ($key) use ($columns) {
            DBIO::validateFields($key);
            return in_array($key, $columns);
        }, ARRAY_FILTER_USE_KEY);

        // SQL生成
        $sql = "INSERT INTO `{$tablename}` (" . implode(", ", array_keys($data))
            . ") VALUES (" . implode(", ", array_fill(0, count($data), "?")) . ");";
        $params = array_values($data);

        // SQL実行
        $this->db->sqlbind($sql, $params);

        return $this->db->dbh->lastInsertId();
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
        DBIO::validateFields($tablename, $pkey);
        // テーブルに存在しないキー名を除外
        $columns = $this->db->getColumns($tablename);
        $data = array_filter($data, function ($key) use ($columns) {
            DBIO::validateFields($key);
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
        $this->db->sqlbind($sql, $params);

        return true;
    }
}
