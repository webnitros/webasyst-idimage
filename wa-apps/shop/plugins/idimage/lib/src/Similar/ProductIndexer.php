<?php
/**
 * Created by Andrey Stepanenko.
 * User: webnitros
 * Date: 22.03.2025
 * Time: 14:53
 */

namespace IdImage\Similar;

use idImageEmbeddingModel;
use shopProduct;

class ProductIndexer
{
    protected array $items = [];
    /**
     * @var true
     */
    private bool $build = false;
    private string $cropSize = '224x224';
    private int $maximumFound = 100;
    private int $minimumScore = 70;

    public function __construct()
    {
    }

    public function setCropSize(string $cropSize)
    {
        $this->cropSize = $cropSize;

        return $this;
    }

    public function setMaximumFound(int $maximumFound)
    {
        $this->maximumFound = $maximumFound;

        return $this;
    }

    public function setMinimumScore(int $minimumScore)
    {
        $this->minimumScore = $minimumScore;

        return $this;
    }

    public function build()
    {
        if ($this->build === false) {
            $this->build = true;
            $model = new idImageEmbeddingModel();
            $embeddings = $model->select('pid,data')->fetchAll();
            foreach ($embeddings as $item) {
                $pid = (int)$item['pid'];
                $embedding = json_decode($item['data'], true);
                if (is_array($embedding) && count($embedding) === 512) {
                    $this->add($pid, $embedding);
                }
            }
        }

        return $this;
    }

    public function add(int $pid, array $embedding)
    {
        $this->items[$pid] = $embedding;

        return $this;
    }

    public function items()
    {
        return $this->items;
    }

    public function all()
    {
        return $this->items;
    }

    public function count()
    {
        return count($this->items);
    }

    public function isEmpty()
    {
        return empty($this->items);
    }

    public function isNotEmpty()
    {
        return !$this->isEmpty();
    }


    public function reset()
    {
        $this->items = [];

        return $this;
    }

    public function embedding(int $pid)
    {
        return $this->items[$pid] ?? null;
    }

    public function search(int $product_id, $maximumFound = null, $minimumScore = null): ?\IdImage\Similar\Embedding
    {
        $maximumFound = $maximumFound ?? $this->maximumFound;
        $minimumScore = $minimumScore ?? $this->minimumScore;
        if (!$data = $this->embedding($product_id)) {
            return null;
        }
        $embedding = new \IdImage\Similar\Embedding($maximumFound, $minimumScore);
        $embedding->create($product_id, $data);

        // Сравниваем со всеми товарами
        $ComparisonCosineSimilarity = new ComparisonCosineSimilarity();
        $ComparisonCosineSimilarity->compareSimilar($embedding, $this->all());

        return $embedding;
    }


    public function productsImages(\IdImage\Similar\Embedding $Embedding, $cropSize = null)
    {
        $cropSize = $cropSize ?? $this->cropSize;
        $products = $Embedding->products();
        $cropSizeName = 'url_'.$cropSize; // url_crop
        $arrays = [];

        // получаем что на сравниваели
        foreach ($products as $product) {
            $id = $product['offer_id'];
            $item = new shopProduct($id);
            $images = $item->getImages($cropSize);
            $image = !empty($images) ? array_shift($images) : null;
            $thumb = $image[$cropSizeName];

            $uri = $item['url'];
            $url = wa()->getRouteUrl('shop/frontendProduct', array('product_url' => $uri), true);
            $arrays[] = $this->addSimilar(
                $id,
                $thumb,
                $product['probability'],
                $item['name'],
                $url
            );
        }

        return $arrays;
    }


    public function addSimilar($id, $thumb, $probability, $name, $url = null)
    {
        return [
            'id' => $id,
            'probability' => $probability,
            'name' => $name,
            'thumb' => $thumb,
            'url' => $url ?? '/webasyst/shop/products/'.$id.'/idimage/',
        ];
    }

}
