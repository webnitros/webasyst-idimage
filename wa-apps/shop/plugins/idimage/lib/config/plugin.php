<?php

return array(
    'name' => 'ID Image',
    'description' => 'Автоматически подбор похожих товаров по изображению',
    'vendor' => '922173',
    'version' => '1.0.1',
    'img' => 'img/idimage.png',
    'shop_settings' => false,
    'frontend' => true,
    'handlers' => array(
        'routing' => 'customRouting',
        'backend_menu' => 'backendMenu',
        'frontend_head' => 'frontendHeader',
        'backend_prod' => 'backendProd',
        //'frontend_header' => 'frontendHeader',
    ),
);
