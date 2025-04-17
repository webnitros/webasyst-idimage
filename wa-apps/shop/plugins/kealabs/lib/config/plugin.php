<?php

return array(
	'name' => 'Продвинутые рекомендации Kea Labs',
	'description' =>'Продвинутые персональные рекомендации товаров',
	'img' => 'img/kea-logo-16.png',
	'vendor' => 1061710,
	'version' => '1.1',
	'custom_settings' => true,
	'handlers' => array(
		'frontend_head' => 'frontendHead',
		'frontend_product' => 'frontendProduct',
		'frontend_category' => 'frontendCategory',
		'frontend_search' => 'frontendSearch',
		'frontend_cart' => 'frontendCart',
		'frontend_homepage' => 'frontendHomepage',
		'cart_add' => 'cartAdd',
		'cart_delete' => 'cartDelete',
		'order_action.create' => 'orderActionCreate',
	)
);