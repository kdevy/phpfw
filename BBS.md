# bbs

## database schema
```
CREATE DATABASE bbs CHARACTER SET utf8mb4;

CREATE TABLE thread (
    id INT(11) AUTO_INCREMENT NOT NULL PRIMARY KEY COMMENT 'ID',
    title VARCHAR(255) COMMENT 'タイトル',
    user_id INT(11) NOT NULL COMMENT '作成者ID',
    created DATETIME COMMENT '作成日時',
    updated DATETIME COMMENT '更新日時'
);

CREATE TABLE user (
    id INT(11) AUTO_INCREMENT NOT NULL PRIMARY KEY COMMENT 'ID',
    username VARCHAR(100) COMMENT 'ユーザID',
    name VARCHAR(100) COMMENT 'プロフィール名',
    password VARCHAR(255) COMMENT 'パスワードハッシュ',
    created DATETIME COMMENT '作成日',
    updated DATETIME COMMENT '更新日'
);

CREATE TABLE res (
    id INT(11) AUTO_INCREMENT NOT NULL PRIMARY KEY COMMENT 'ID',
    thread_id INT(11) NOT NULL COMMENT 'スレッドID',
    user_id INT(11) COMMENT 'ユーザID',
    to_id INT(11) COMMENT '返信先レスID',
    text TEXT COMMENT 'レス内容',
    created DATETIME COMMENT '作成日',
    updated DATETIME COMMENT '更新日'
);
```