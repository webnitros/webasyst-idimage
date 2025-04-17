<?php

use IdImage\Similar\ProductIndexer;

class shopIdimagePluginBackendProductInfoAction extends waViewAction
{
    public function execute()
    {
        $setting = wa()->getPlugin('idimage')->getSettings();
        $maximumFound = (int)$setting['maximum_found'];
        $minimumScore = (int)$setting['minimum_score'];
        $cropSize = (string)$setting['crop'];

        $product_id = waRequest::param('id', '', 'int');

        $product = new shopProduct($product_id);
        $images = $product->getImages($cropSize);

        $image = !empty($images) ? array_shift($images) : null;

        $name = 'url_'.$cropSize;
        $thumb = !empty($image[$name]) ? $image[$name] : null;

        $filepath = $thumb ? wa()->getConfig()->getRootPath().$thumb : null;
        $exist = $thumb ? file_exists($filepath) : false;

        $similar = $this->searchSimilarProducts($product_id, $cropSize, $maximumFound, $minimumScore);
        $this->view->assign('minimumScore', $minimumScore);
        $this->view->assign('exist', $exist);
        $this->view->assign('thumb', $thumb);
        $this->view->assign('image', $image);
        $this->view->assign('product', $product);
        $this->view->assign('product_id', $product_id);
        $this->view->assign('similar', $similar);
        $this->view->assign('similar_exist', !empty($similar));

        $data = [
            'product' => $product,
            'content_id' => 'idimage',
        ];

        $this->setLayout(new shopBackendProductsEditSectionLayout($data));
    }


    public function searchSimilarProducts($product_id, string $cropSize = '224x224', int $maximumFound = 100, int $minimumScore = 70)
    {
        $indexer = new ProductIndexer();
        $embedding = $indexer->build()->search($product_id, $maximumFound, $minimumScore);
        if ($embedding->isEmpty()) {
            return null;
        }

        // Получаем изображения
        return $indexer->productsImages($embedding, $cropSize);
    }

}
