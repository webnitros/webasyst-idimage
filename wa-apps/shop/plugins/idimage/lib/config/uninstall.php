<?php

$model = new waModel();
$model->exec("DROP TABLE IF EXISTS shop_idimage_embeddings");
$model->exec("DROP TABLE IF EXISTS shop_idimage_similars");
