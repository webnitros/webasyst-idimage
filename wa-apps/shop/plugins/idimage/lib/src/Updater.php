<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 10.03.2025
 * Time: 18:52
 */

namespace IdImage;

use IdImage\Api\Ai;
use IdImage\Entites\TaskEntity;
use IdImage\Support\Client;
use idImageEmbeddingModel;

class Updater
{
    private idImageEmbeddingModel $model;
    private string $root;
    private string $targetPath;
    private \shopProductImagesModel $modelImage;
    /**
     * @var string
     */
    private string $imageSize = 'crop';

    public function __construct()
    {
        $this->model = new idImageEmbeddingModel();
        $this->root = wa()->getConfig()->getRootPath().'/';
        $this->targetPath = $this->root.'wa-data/public/shop/idimage/';
        if (!file_exists($this->targetPath)) {
            mkdir($this->targetPath, 0777, true);
        }
        $config = wa('shop')->getConfig();
        $sizes = $config->getImageSizes();
        $this->imageSize = $sizes[0];
        $this->modelImage = new \shopProductImagesModel();
    }

    public function create($id, string $path)
    {
        return new TaskEntity(
            (string)$id,
            $this->root.$path,
            $this->targetPath
        );
    }

    public function add(TaskCollection $collection, TaskEntity $entity, $exist = true): bool
    {
        if (!$exist && $this->exist($entity->getHash())) {
            return false;
        }

        $source = $entity->getSource();
        $target = $entity->getTarget();
        // Сохранение ресурса
        if (!$entity->targetFileExist()) {
            if (!$entity->checkFormat($source)) {
                \IdImage\Support\GenerateThumb::create($source, $target);
            } else {
                copy($source, $target);
            }
        }

        // Записываем верный путь
        $entity->setPicturePath($target);
        $collection->add($entity);

        return true;
    }


    public function send(TaskCollection $collection)
    {
        if ($collection->isEmpty()) {
            return null;
        }

        $upload = (new Ai(new Client()))->upload($collection);

        // Отправляем запрос
        $response = $upload->send();
        $response->items(function ($item) use ($collection) {
            // обработчик каждого элемента
            $entity = $collection->get($item['offer_id']);

            $entity->setEmbedding($item['embedding']);
            $entity->setStatus($item['status']);

            $data = [
                'pid' => $entity->getOfferId(),
                'hash' => $entity->getHash(),
                'data' => json_encode($entity->getEmbedding()),
            ];

            if (!$id = $this->getEmbeddingId($entity->getHash())) {
                $data = array_merge($data, ['createdon' => time()]);
                // Добавление новой записи
                $this->model->insert($data);
            } else {
                $data = array_merge($data, ['updatedon' => time()]);
                $this->model->updateById($id, $data);
            }
        });
    }

    public function getEmbeddingId(string $hash): ?int
    {
        return @$this->model->getByField('hash', $hash)['id'] ?? null;
    }

    public function exist(string $hash): bool
    {
        return !empty($this->getEmbeddingId($hash));
    }

    public function fileExist(string $path)
    {
        $path = $this->root.$path;

        return file_exists($path);
    }

    public function imagePath(int $id): ?string
    {
        $images = $this->modelImage->getImages($id, $this->imageSize);

        if (empty($images)) {
            return null;
        }
        $image = array_shift($images);
        $fieldName = 'url_'.$this->imageSize;
        $path = $image[$fieldName] ?? null;

        return $path;
    }

    /**
     * Запускает обработчик для получения векторов
     * @param  array  $products
     * @return int
     * @throws \Exception
     */
    public function process(array $products)
    {
        $i = 0;
        $TaskCollection = new TaskCollection();
        foreach ($products as $product) {
            $id = $product['id'];

            // Получаем путь до изображения
            if ($imagePath = $this->imagePath($id)) {
                if ($this->fileExist($imagePath)) {
                    // Добавляем в коллекцию для получения
                    $TaskEntity = $this->create($id, $imagePath);

                    // Проверяем наличие изображения по его hash
                    if (!$TaskEntity->existEmbedding()) {
                        // Создаем нужный формат

                        if ($TaskEntity->isNeedToCreateAnImageTarget()) {
                            $TaskEntity->createThumb();
                        }

                        // Записываем верный путь
                        $TaskEntity->setPicturePath($TaskEntity->getTarget());
                        // Добавляем в коллекцию для получения
                        $this->add($TaskCollection, $TaskEntity);
                        $i++;
                    }
                }
            }

            if ($TaskCollection->count() > 10) {
                // Отправляем и создаем новую коллекцию
                $this->send($TaskCollection);
                $TaskCollection = new TaskCollection();
            }
        }

        // Если что то осталось
        if ($TaskCollection->isNotEmpty()) {
            $this->send($TaskCollection);
        }

        return $i;
    }

}
