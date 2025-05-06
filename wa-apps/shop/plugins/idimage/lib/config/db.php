<?php
return array(
    'shop_idimage_embeddings' => array(
        'id' => array('int', 'null' => 0, 'autoincrement' => 1),
        'hash' => array('char', 40, 'null' => 0),
        'data' => array('text'),
        'updatedon' => array('int', 'null' => 0, 'default' => '0'),
        'createdon' => array('int', 'null' => 0, 'default' => '0'),
        'pid' => array('int'),
        ':keys' => array(
            'PRIMARY' => 'id',
            'hash' => array('hash', 'unique' => 1),
            'pid' => 'pid',
        ),
    ),
    'shop_idimage_similars' => array(
        'id' => array('int', 'null' => 0, 'autoincrement' => 1),
        'pid' => array('int unsigned', 'null' => 0),
        'data' => array('text'),
        'total' => array('int unsigned', 'default' => '0'),
        'compared' => array('int unsigned', 'default' => '0'),
        'search_scope' => array('int unsigned', 'default' => '0'),
        'min_scope' => array('int unsigned', 'default' => '0'),
        'max_scope' => array('int unsigned', 'default' => '0'),
        'updatedon' => array('int', 'null' => 0, 'default' => '0'),
        'createdon' => array('int', 'null' => 0, 'default' => '0'),
        ':keys' => array(
            'PRIMARY' => 'id',
            'pid' => array('pid', 'unique' => 1),
            'search_scope' => 'search_scope',
        ),
    ),
);
