<?php


return [
    'shop_idimage_embeddings' => [
        'id' => ['int', 11, 'p'], // Добавляем id как первичный ключ
        'hash' => ['char', 40, 'u'], // unique индекс
        'data' => ['text'],
        'updatedon' => ['int', 20, 0],
        'createdon' => ['int', 20, 0],
        'pid' => ['int', 10, 'null'],
    ],

    'shop_idimage_similars' => [
        'id' => ['int', 11, 'p'], // PRIMARY KEY
        'pid' => ['int', 10, 'u'], // UNIQUE индекс для pid
        'data' => ['text'],
        'total' => ['int', 10, 'unsigned', 0], // unsigned, с дефолтным значением
        'compared' => ['int', 10, 'unsigned', 0], // unsigned, с дефолтным значением
        'search_scope' => ['int', 10, 'unsigned', 0], // unsigned, с дефолтным значением
        'min_scope' => ['int', 10, 'unsigned', 0], // unsigned, с дефолтным значением
        'max_scope' => ['int', 10, 'unsigned', 0], // unsigned, с дефолтным значением
        'updatedon' => ['int', 20, 0],
        'createdon' => ['int', 20, 0],
    ],
];
