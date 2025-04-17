<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 09.03.2025
 * Time: 12:42
 */

namespace IdImage\Similar;

class Embedding
{
    private int $total = 0;
    private ?array $data = null;
    private int $status;
    /**
     * @var int|mixed
     */
    private $min_value;
    /**
     * @var int|mixed
     */
    private $max_value;
    private int $compared = 0;
    private ?int $offerId = null;
    private ?array $embedding = null;

    private $search_scope = 0;
    private $maximumFound = 10;
    private $minimumScore = 70;

    public function __construct(
        int $maximumFound,
        int $minimumScore
    ) {
        $this->search_scope = $maximumFound;
        $this->maximumFound = $maximumFound;
        $this->minimumScore = $minimumScore;
    }

    public function maximumFound()
    {
        return $this->maximumFound;
    }

    public function minimumScore()
    {
        return $this->minimumScore;
    }

    public function minValue()
    {
        return $this->min_value;
    }

    public function maxValue()
    {
        return $this->max_value;
    }


    public function total()
    {
        return $this->total;
    }


    public function toArray()
    {
        return [
            'min_scope' => $this->min_value,
            'max_scope' => $this->max_value,
            'search_scope' => $this->search_scope,
            'total' => $this->total,
            'compared' => $this->compared,
            'similar' => $this->data(),
        ];
    }

    public function setCompared(int $total)
    {
        $this->compared = $total;

        return $this;
    }

    public function compared()
    {
        return $this->compared;
    }


    public function create(int $offerId, array $embedding): self
    {
        $this->setOfferId($offerId)
            ->setEmbedding($embedding);

        return $this;
    }

    public function setOfferId(int $offerId)
    {
        $this->offerId = $offerId;

        return $this;
    }

    public function getOfferId()
    {
        return $this->offerId;
    }

    public function setEmbedding(array $embedding)
    {
        $this->embedding = $embedding;

        return $this;
    }

    public function getEmbedding(): array
    {
        return $this->embedding;
    }

    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    public function products(): ?array
    {
        return $this->data;
    }

    public function setMinValue(float $value)
    {
        $this->min_value = $value;

        return $this;
    }

    public function setMaxValue(float $value)
    {
        $this->max_value = $value;

        return $this;
    }

    public function setTotal(int $count)
    {
        $this->total = $count;

        return $this;
    }

    public function isNotEmpty()
    {
        return $this->total() > 0;
    }

    public function isEmpty()
    {
        return !$this->isNotEmpty();
    }

}
