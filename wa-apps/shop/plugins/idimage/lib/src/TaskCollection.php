<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 10.03.2025
 * Time: 18:52
 */

namespace IdImage;

use IdImage\Entites\TaskEntity;
use IdImage\Support\Response;

class TaskCollection
{
    /* @var TaskEntity[] $items */
    private array $items = [];

    public function __construct($items = null)
    {
        if (is_array($items)) {
            $this->items = $items;
        }
    }


    public function add(TaskEntity $entity): self
    {
        $this->items[$entity->getOfferId()] = $entity;

        return $this;
    }

    public function toArray(): array
    {
        return $this->items;
    }

    public function each(callable $callback): self
    {
        /* @var TaskEntity $item */
        foreach ($this->items as $key => $item) {
            $task = $this->get($item->getOfferId());
            $callback($item, $task, $key);
        }

        return $this;
    }

    public function forget(int $key)
    {
        if (isset($this->items[$key])) {
            unset($this->items[$key]);
        }

        return $this;
    }


    public function get($id): ?TaskEntity
    {
        return $this->items[$id] ?? null;
    }


    public function reset()
    {
        $this->items = [];

        return $this;
    }

    public function count()
    {
        return count($this->items);
    }

    public function first()
    {
        return current($this->items);
    }

    public function last()
    {
        return end($this->items);
    }

    public function isNotEmpty()
    {
        return $this->count() > 0;
    }

    public function isEmpty()
    {
        return !$this->isNotEmpty();
    }

    public function handerResponse(Response $Response)
    {
        $items = $Response->json('items');
        if (!empty($items)) {
            $items = array_combine(array_column($items, 'offer_id'), $items);
            $this->each(function (TaskEntity $entity) use ($items) {
                $item = $items[$entity->getOfferId()] ?? null;
                if ($item) {
                    $entity->setResponse($item);
                }
            });
        }
    }

    public function pids()
    {
        $pids = [];
        foreach ($this->items as $entity) {
            $pids[] = $entity->getOfferId();
        }

        return $pids;
    }

}
