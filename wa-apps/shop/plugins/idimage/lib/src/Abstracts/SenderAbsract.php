<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 22.02.2025
 * Time: 12:48
 */

namespace IdImage\Abstracts;


use Exception;
use idImage;
use IdImage\Entites\TaskEntity;
use IdImage\Exceptions\ExceptionSender;
use IdImage\Support\Response;
use IdImage\TaskCollection;
use idImageTask;

abstract class SenderAbsract
{
    public idImage $idImage;

    const OPERATION_UPLOAD = 'upload';
    const OPERATION_INDEXED = 'indexed';

    static $operationsMap = [
        self::OPERATION_UPLOAD,
        self::OPERATION_INDEXED,
    ];

    /**
     * @var string[]
     */
    protected array $statuses;

    protected array $limits = [
        self::OPERATION_UPLOAD => 10,
        self::OPERATION_INDEXED => 100,
    ];

    public function __construct(\idImage $idImage)
    {
        $this->idImage = $idImage;
        $this->statuses = idImageTask::$statusMap;
        if ($this->idImage->isIndexedService()) {
            // Лимит на получения данных от сервиса индексации
            $this->limits[self::OPERATION_INDEXED] = 1000;
        }
    }


    public function exception($msg)
    {
        $msg = is_array($msg) ? json_encode($msg) : $msg;

        return new ExceptionSender($msg);
    }


    public function send(TaskCollection $collection): void
    {
        // Группируем

        $map = self::$operationsMap;
        $operations = [];
        foreach ($map as $k) {
            $operations[$k] = [];
        }

        // Создаем список операций
        $collection->each(function (TaskEntity $entity) use (&$operations) {
            $operations[$entity->getOperation()][] = $entity;
        });

        // Полный список удаляем
        unset($collection);


        // обработка операций
        foreach ($operations as $operation => $tasks) {
            if (empty($tasks)) {
                continue; // если нету пропускаем
            }

            // Разбиваем на чанки по операциям
            $limit = $this->limit($operation);
            $chunks = array_chunk($tasks, $limit);

            foreach ($chunks as $chunk) {
                if (!in_array($operation, $map)) {
                    throw new Exception('Неизвестный тип запроса');
                }

                if (!method_exists($this, $operation)) {
                    throw new Exception('Method not found: '.$operation);
                }

                // отправляем запрос
                $collection = new TaskCollection($chunk);
                $this->$operation($collection);
            }
        }
    }

    /**
     * Основной процесс обработки операции
     * @param  TaskCollection  $collection
     * @param  \Closure  $apiCallback
     * @param  \Closure  $handle
     * @return bool
     * @throws Exception
     */
    protected function handle(
        TaskCollection $collection,
        \Closure $apiCallback,
        \Closure $handle
    ): bool {
        if ($collection->isEmpty()) {
            return false;
        }
        $api = $this->idImage->api()->task();

        $request = $apiCallback($api, $collection);

        // Отправляем данные в сервис
        /* @var Response|\IdImage\Support\Client $Response */
        $Response = $request instanceof Response ? $request : $request->send();

        // Обработка ответа
        $collection->handerResponse($Response);

        // Загрузка заданий
        $collection->loadTasks($this->idImage->query()->tasks());

        if ($Response->isFail()) {
            // Критическая ошибка автоматчиески вышибает все задания
            $msg = $Response->getMessage();
            $collection->each(function (TaskEntity $entity, ?idImageTask $task) use ($msg) {
                $task->setErrors($msg, null);
                $task->save();
            });
        } else {
            $collection->each(function (TaskEntity $entity, ?idImageTask $task) use ($handle, $collection) {
                if (!$task) {
                    $this->addError('Не найден задание для: '.$entity->getOfferId());

                    return false;
                }
                if (!$entity->exists()) {
                    $task->setErrors('Error get item back');
                } else {
                    $this->handerResponse($entity, $handle, $task);
                }

                return $task->save();
            });
        }

        return true;
    }

    /**
     * Обработка запроса
     * @param  TaskEntity  $entity
     * @param  \Closure  $handle
     * @param  idImageTask|null  $task
     * @return bool
     * @throws Exception
     */
    private function handerResponse(TaskEntity $entity, \Closure $handle, ?idImageTask $task = null): bool
    {
        // обработка ответа
        // Получаем ответ
        $error = null;
        $operation = $entity->getOperation();
        try {
            $status = $handle($task, $entity);
            if (!is_int($status)) {
                $status = idImageTask::STATUS_FAILED;
                $error = 'Ответ должен быть строкой';
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
            $status = idImageTask::STATUS_FAILED;
        }


        if (!array_key_exists($status, $this->statuses)) {
            throw new Exception('Неизвестный статус: '.$status.' taskId: '.$task->get('id').' operation: '.$operation);
        }

        if ($status === idImageTask::STATUS_FAILED) {
            $task->attemptsFailure();
            if (!$task->attemptFailureExceeded()) {
                // Если кол-во попыток еще не превышено
                // Пробуем перевести задание в статус "retry"
                $status = idImageTask::STATUS_RETRY;
            }
        }

        $task->setErrors($error, $status);
        $task->set('status', $status);

        return true;
    }

    public function addError(string $msg)
    {
        $this->errors[] = $msg;

        return $this;
    }


    public function limit(string $operation, int $default = 20)
    {
        return $this->limits[$operation] ?? $default;
    }


}
