<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 24.02.2025
 * Time: 11:50
 */

namespace IdImage\Api;

use CURLFile;
use IdImage\Abstracts\ApiAbstract;
use IdImage\Entites\TaskEntity;
use IdImage\Interfaces\ApiInterfaces;
use IdImage\TaskCollection;

class Ai extends ApiAbstract implements ApiInterfaces
{

    public function balance()
    {
        return $this->client->post('/ai/balance');
    }

    public function upload(TaskCollection $collection, bool $force = false)
    {
        $postFields = [];
        $i = 0;

        $collection->each(function (TaskEntity $entity) use (&$postFields, &$i) {
            $offerId = $entity->getOfferId();
            $imagePath = $entity->getPicturePath();
            $postFields["files[$i]"] = new CURLFile($imagePath, 'image/jpeg', basename($imagePath));
            $postFields["file_ids[$i]"] = $offerId; // Добавляем ID
            $i++;
        });

        if ($force) {
            $postFields['force'] = true; // Быстрое получение векторов
        }

        return $this->client->post('/ai/upload', $postFields)->setHeaders([
            'Accept: application/json',
        ]);
    }

}
