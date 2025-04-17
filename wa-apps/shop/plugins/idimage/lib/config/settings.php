<?php

return array(
    'enabled' => [
        'title' => _wp('Enable plugin'),
        'value' => 1,
        'control_type' => waHtmlControl::CHECKBOX,
    ],

    'token' => [
        'title' => _wp('Token'),
        'value' => '',
        'description' => _wp('Enter token to work with api'),
        'control_type' => waHtmlControl::INPUT,
    ],

    'api_url' => [
        'title' => _wp('API URL'),
        'value' => 'https://idimage.ru/api',
        'description' => _wp('Enter API URL to work with API'),
        'control_type' => waHtmlControl::INPUT,
    ],

    'crop' => [
        'title' => _wp('Crop image'),
        'value' => '224x224',
        'description' => _wp('Image size for finding similar products'),
        'control_type' => waHtmlControl::INPUT,
    ],



    'maximum_found' => [
        'title' => _wp('Maximum found'),
        'value' => 100,
        'description' => _wp('Maximum number of products to save'),
        'control_type' => waHtmlControl::INPUT,
    ],

    'minimum_score' => [
        'title' => _wp('Minimum score'),
        'value' => 70,
        'description' => _wp('Minimum similarity score'),
        'control_type' => waHtmlControl::INPUT,
    ],

    'selector_thumb' => [
        'title' => _wp('Selector thumb'),
        'value' => '.js-product_gallery-images-main',
        'description' => _wp('Selector to bind the similar products button'),
        'control_type' => waHtmlControl::INPUT,
    ],

    'selector_button' => [
        'title' => _wp('Selector button'),
        'value' => '.idimage-similar',
        'description' => _wp('Selector of button that opens similar products modal (must have data-product-id attribute)'),
        'control_type' => waHtmlControl::INPUT,
    ],

    'selector' => [
        'title' => _wp('Selector'),
        'value' => '.idimage',
        'description' => _wp('Main wrapper class for the button'),
        'control_type' => waHtmlControl::INPUT,
    ],

    'selector_products_id' => [
        'title' => _wp('Selector Products Id'),
        'value' => '[data-product-num]',
        'description' => _wp('Selector that contains product ID'),
        'control_type' => waHtmlControl::INPUT,
    ],
);
