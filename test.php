#!/usr/bin/php
<?php

use IdImage\Api\Ai;
use IdImage\Entites\TaskEntity;
use IdImage\Similar\ComparisonCosineSimilarity;
use IdImage\Similar\ProductIndexer;
use IdImage\Support\Client;
use IdImage\TaskCollection;
use IdImage\Updater;

if (PHP_SAPI !== 'cli') {
    die("Run from CLI only!");
}

require_once dirname(__FILE__).'/wa-config/SystemConfig.class.php';
$wa = waSystem::getInstance(null, new SystemConfig('cli'));
// Replace script name
//array_splice($_SERVER['argv'], 1, 0, 'webasyst');
// Run CLI

//$wa->dispatchCli('shop');

define('WA_APP', 'shop'); // укажи нужное приложение

// Инициализация Webasyst дляуказанного приложения
waSystem::getInstance(WA_APP);
echo "Webasyst загружен для приложения '".WA_APP."'\n";
require wa()->getAppPath('plugins/idimage/vendor/autoload.php', 'shop');

$productId = 410;

$Updater = new Updater();
$i = $Updater->process([
    [
        'id' => $productId,
    ],
]);

dd($i);


//$product = new shopProduct();
$ProductIndexer = (new ProductIndexer())->build();

$embedding = new \IdImage\Similar\Embedding(100, 50);

$embedding->create($productId, $ProductIndexer->embedding($productId));

(new ComparisonCosineSimilarity())->compareSimilar($embedding, $ProductIndexer->all());

dd($embedding->toArray());


$model = new shopProductModel();
$products = $model->select('id')->order('id DESC')->limit(200)->fetchAll();

$Updater = new Updater();
$i = $Updater->process($products);

echo '<pre>';
print_r([
    'i' => $i,
]);
die;

$id = 426;
$TaskEntity = (new TaskEntity($source, $pathSave))->setOfferId('1');


$collection = (new TaskCollection());
$model = new idImageEmbeddingModel();
if (!$record = $model->getByField('hash', $TaskEntity->getHash())) {
    // Сохранение ресурса
    if (!$TaskEntity->targetFileExist()) {
        if (!$TaskEntity->checkFormat($TaskEntity->getSource())) {
            $GenerateThumb = \IdImage\Support\GenerateThumb::create($TaskEntity->getSource(), $TaskEntity->getTarget());
        } else {
            copy($TaskEntity->getSource(), $TaskEntity->getTarget());
        }
    }

    // Записываем верный путь
    $TaskEntity->setPicturePath($TaskEntity->getTarget());

    $collection = $collection->add($TaskEntity);
    $upload = (new Ai(new Client()))->upload($collection);

    // Отправляем запрос
    $response = $upload->send();
    $response->items(function ($item) use ($collection) { // обработчик каждого элемента
        $model = new idImageEmbeddingModel();
        $entity = $collection->get($item['offer_id']);

        echo '<pre>';
        print_r($entity);
        die;

        if (!$record = $model->getByField('hash', $entity->getHash())) {
            $entity->setEmbedding($item['embedding']);
            $entity->setStatus($item['status']);

            // Добавление новой записи
            $model->insert([
                'pid' => $entity->getOfferId(),
                'hash' => $entity->getHash(),
                'data' => json_encode($entity->getEmbedding()),
                'updatedon' => time(),
                'createdon' => time(),
            ]);
        }

        // Получение записи по ID
        echo '<pre>';
        print_r($record);
        die;
    });
}


echo '<pre>';
print_r(222);
die;


echo '<pre>';
print_r($response->toArray());
die;

echo '<pre>';
print_r($upload);
die;


echo '<pre>';
print_r([
    '$collection' => $collection->toArray(),
]);
die;

echo '<pre>';
print_r($TaskEntity->toArray());
die;


echo '<pre>';
print_r($TaskEntity);
die;


// Пример использования модели плагина
$model = new idImageEmbeddingModel();

$data = $model->getById(1);

echo '<pre>';
print_r($data);
die;


