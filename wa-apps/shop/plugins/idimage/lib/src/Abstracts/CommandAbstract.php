<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 22.02.2025
 * Time: 12:48
 */

namespace IdImage\Abstracts;


use idImage;
use IdImage\Cli;
use idImageTask;

abstract class CommandAbstract
{

    public Cli $cli;
    public idImage $idImage;
    public $action = 'mgr/actions/task/send';

    protected ?string $operation = null;
    private int $stepLimit = 20;

    public function __construct(idImage $idImage, Cli $cli, int $stepLimit = 20)
    {
        $this->idImage = $idImage;
        $this->cli = $cli;
        $this->stepLimit = $stepLimit;
    }

    public function stepLimit()
    {
        return $this->stepLimit;
    }

    public function run()
    {
        $totalTask = $this->idImage->query()->tasksQueue($this->operation())->count();

        $this->cli->title('Operation: '.$this->operation);

        $this->cli->info('Total tasks: '.$totalTask);
        $this->cli->info('Step limit: '.$this->stepLimit());

        $response = $this->idImage->runProcessor($this->action, [
            'operation' => $this->operation(),
            'steps' => true,
            'step_limit' => $this->stepLimit(),
            'limit' => $this->idImage->limitTask(),
        ]);

        if ($response->isError()) {
            $this->cli->info('Error: '.$response->getMessage());

            return false;
        }
        $data = $response->getObject();

        $total = $data['total'];
        $iterations = $data['iterations'];

        $this->cli->info('Launch tasks: '.$total);

        $totalIterations = $iterations ? count($iterations) : 0;
        $this->cli->info('Iterations: '.$totalIterations);

        $this->cli->startTime();


        if (!empty($iterations)) {
            foreach ($iterations as $i => $ids) {
                $this->idImage->modx->error->reset();
                $this->handle($i, $ids);
            }
        }


        $time = $this->cli->endTime();
        $this->cli->info('Time: '.$time);

        $queue = $this->idImage->query()->tasks()->where([
            'operation' => $this->operation(),
            'status:IN' => [
                idImageTask::STATUS_PENDING,
                idImageTask::STATUS_QUEUE,
            ],
        ])->count();
        $queueEx = $this->idImage->query()->tasksExecuteAt()->where([
            'operation' => $this->operation(),
            'status:IN' => [
                idImageTask::STATUS_PENDING,
                idImageTask::STATUS_QUEUE,
            ],
        ])->count();
        if ($queueEx > 0) {
            $this->cli->warning('Queue tasks executeAt: '.$queueEx);
        }
        $this->cli->info('Queue tasks: '.$queue);
        $this->cli->info('Completed');
    }

    public function operation()
    {
        return $this->operation;
    }

    public function handleTask(int $i, array $ids)
    {
        $time = microtime(true);

        // Создать товары
        $response = $this->idImage->runProcessor($this->action, [
            'operation' => $this->operation(),
            'ids' => $ids,
        ]);

        if ($response->isError()) {
            $this->cli->error($response->getMessage());

            return false;
        }

        $data = $response->getObject();
        $time = round(microtime(true) - $time, 2).' s';
        $this->cli->info('[iteration:'.$i.'] tasks: '.$data['total'].' | '.$time);

        return true;
    }
}
