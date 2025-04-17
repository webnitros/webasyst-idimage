<?php

$model = new waModel();


$sql = <<<SQL
CREATE TABLE IF NOT EXISTS `shop_idimage_embeddings` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `hash` CHAR(40) NOT NULL UNIQUE,
    `data` TEXT,
    `updatedon` INT(20) NOT NULL DEFAULT 0,
    `createdon` INT(20) NOT NULL DEFAULT 0,
    `pid` INT(10) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SQL;

$model->exec($sql);


$sql = <<<SQL
CREATE TABLE IF NOT EXISTS `shop_idimage_similars` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `pid` INT(10) UNSIGNED NOT NULL,
    `data` TEXT,
    `total` INT(10) UNSIGNED DEFAULT 0,
    `compared` INT(10) UNSIGNED DEFAULT 0,
    `search_scope` INT(10) UNSIGNED DEFAULT 0,
    `min_scope` INT(10) UNSIGNED DEFAULT 0,
    `max_scope` INT(10) UNSIGNED DEFAULT 0,
    `updatedon` INT(20) NOT NULL DEFAULT 0,
    `createdon` INT(20) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    UNIQUE KEY `pid` (`pid`),
    KEY `search_scope` (`search_scope`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SQL;

$model->exec($sql);
