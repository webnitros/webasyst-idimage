<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 10.03.2025
 * Time: 16:33
 */

namespace IdImage\Entites;


use Exception;
use idImageEmbeddingModel;

class TaskEntity
{
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_PENDING = 'pending';

    protected bool $exists = false;
    protected ?array $response = null;

    private string $status = self::STATUS_PENDING;
    private ?array $embedding = null;
    /**
     * @var true
     */
    private ?array $errors = null;
    private ?string $offer_id = null;
    private ?string $picturePath = null;

    protected ?array $similar = null;
    private ?string $source = null;
    private ?string $hash = null;
    private ?string $target = null;
    private idImageEmbeddingModel $model;

    public function __construct($id, string $source, string $target)
    {
        $id = (string)$id;
        $this->setOfferId($id);
        $this->setSource($source);
        $this->setTarget($target.$this->getHash().'.jpg');

        $this->model = new idImageEmbeddingModel();
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getEmbedding(): ?array
    {
        return $this->embedding;
    }


    public function toArray(): array
    {
        return [
            'status' => $this->status,
            'offer_id' => $this->offer_id,
            'source' => $this->source,
            'target' => $this->target,
            'picture_path' => $this->picturePath,
            'received' => $this->received,
            'embedding' => $this->embedding,
            'similar' => $this->similar,
            'errors' => $this->errors,
        ];
    }

    public function setStatus(string $string)
    {
        $this->status = $string;

        return $this;
    }

    public function setEmbedding(array $embedding)
    {
        $this->embedding = $embedding;

        return $this;
    }


    public function getErrors(): ?array
    {
        return $this->errors;
    }

    public function setErrors($errors)
    {
        $this->setStatus(self::STATUS_FAILED);

        if ($errors instanceof \Throwable) {
            $this->errors = [
                'msg' => '[Throwable] '.$errors->getMessage(),
            ];
        } elseif ($errors instanceof \Exception) {
            $this->errors = [
                'msg' => '[Exception] '.$errors->getMessage(),
            ];
        } elseif (is_array($errors) || is_null($errors)) {
            $this->errors = $errors;
        } elseif (is_string($errors)) {
            $this->errors = [
                'msg' => $errors,
            ];
        }

        return $this;
    }

    public function setOfferId(string $offer_id)
    {
        $this->offer_id = $offer_id;

        return $this;
    }

    public function getOfferId(): string
    {
        return $this->offer_id;
    }


    /**
     * Check if image size is correct
     * @param  string  $picturePath
     * @return string|bool
     */
    public function checkFormat(string $picturePath)
    {
        $size = @getimagesize($picturePath);

        if ($size[0] !== 224 || $size[1] !== 224) {
            return 'Incorrect image size, should be 224x224';
        }

        if ($size['mime'] !== 'image/jpeg') {
            return 'Invalid image format, must be jpeg';
        }

        return true;
    }

    public function setPicturePath(string $picturePath)
    {
        $result = $this->checkFormat($picturePath);
        if ($result !== true) {
            throw new Exception($result);
        }
        $this->picturePath = $picturePath;

        return $this;
    }

    public function getPicturePath()
    {
        return $this->picturePath;
    }

    public function setSimilar(array $similar)
    {
        $this->similar = $similar;

        return $this;
    }

    public function getSimilar()
    {
        return $this->similar;
    }

    public function setResponse(array $item)
    {
        $this->response = $item;
        $this->exists = true;

        return $this;
    }

    public function getResponse(): array
    {
        return $this->response;
    }

    public function exists()
    {
        return $this->exists;
    }

    public function setHash(string $hash)
    {
        $this->hash = $hash;

        return $this;
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function setSource(string $source): self
    {
        $this->source = $source;
        if ($this->sourceFileExist()) {
            $this->setHash(md5_file($source));
        }

        return $this;
    }

    public function sourceFileExist()
    {
        return file_exists($this->getSource());
    }

    public function getSource(): string
    {
        return $this->source;
    }

    public function setTarget(string $string)
    {
        $this->target = $string;

        return $this;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function targetFileExist()
    {
        return file_exists($this->target);
    }

    /**
     * Проверка необходимости создать изображение
     * @return bool
     */
    public function isNeedToCreateAnImageTarget()
    {
        // Если нету целевого изображения
        if (!$this->targetFileExist()) {
            return true;
        }

        // Если целевое изображение не в нужном формате
        $re = $this->checkFormat($this->getTarget());
        if ($re !== true) {
            return true;
        }

        return false;
    }

    public function createThumb()
    {
        $source = $this->getSource();
        $target = $this->getTarget();
        $result = $this->checkFormat($source);
        if ($result !== true) {
            \IdImage\Support\GenerateThumb::create($source, $target);
        } else {
            copy($source, $target);
        }
    }

    public function existEmbedding()
    {
        return $this->model->countByField('hash', $this->getHash()) > 0;
    }
}
