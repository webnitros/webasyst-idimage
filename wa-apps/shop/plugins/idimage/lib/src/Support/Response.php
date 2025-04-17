<?php

namespace IdImage\Support;

use Exception;

/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 19.02.2025
 * Time: 23:48
 */
class Response
{
    private $status;
    private $content;

    /* @var string|null $msg */
    private $msg;

    public function __construct($status, $content, $msg = null)
    {
        $this->status = $status;
        $this->content = $content;
        $this->msg = $msg;
    }


    public function isOk()
    {
        return ($this->status === 200 || $this->status === 201 || $this->status === 204);
    }

    public function isFail()
    {
        return !$this->isOk();
    }

    public function getStatus(): int
    {
        return !empty($this->status) ? $this->status : 0;
    }

    public function getContent()
    {
        return !empty($this->content) ? $this->content : null;
    }

    public function getMessage()
    {
        $msg = !empty($this->msg) ? $this->msg : '';
        if ($this->isFail()) {
            if ($data = $this->json()) {
                if (!empty($data['message'])) {
                    $msg = $data['message'];
                }
            }
        }

        return $msg;
    }

    protected $decoded;

    public function setDecoded(array $data)
    {
        $this->decoded = $data;

        return $this;
    }

    public function json($key = null, $default = null)
    {
        if (!$this->decoded) {
            if ($content = $this->getContent()) {
                if (substr($content, 0, 1) === '{') {
                    $this->decoded = json_decode($this->getContent(), 1) ?? null;
                }
            }
        }

        if (is_null($this->decoded)) {
            return $default;
        }
        if (is_null($key)) {
            return $this->decoded;
        }

        if (!is_array($this->decoded)) {
            return $default;
        }

        return $this->decoded[$key];
    }

    public function toArray()
    {
        return [
            'status' => $this->getStatus(),
            'msg' => $this->getMessage(),
            'data' => $this->json() ?? $this->getContent(),
        ];
    }


    public function exception()
    {
        $msg = "[STATUS: ".$this->getStatus()."] msg: ".$this->getMessage();

        return new Exception($msg);
    }

    public function items($callback = null)
    {
        $items = $this->json('items');
        if (empty($items) || !is_array($items)) {
            return null;
        }
        if (is_callable($callback)) {
            if (!empty($items) && is_array($items)) {
                foreach ($items as $item) {
                    $callback($item);
                }
            }
        }

        return $items;
    }

    private $entity = null;

    public function setEntity(string $entity)
    {
        $this->entity = $entity;

        return $this;
    }

    public function entity()
    {
        if ($this->isFail()) {
            return null;
        }
        if (empty($this->entity)) {
            throw new Exception("Не указан класс для обработки данных entity");
        }

        $Entity = new $this->entity();

        if (empty($Entity)) {
            return null;
        }
    }
}
