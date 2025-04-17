<?php

use IdImage\Similar\ProductIndexer;

require wa()->getAppPath('plugins/idimage/lib/vendor/autoload.php', 'shop');

class shopIdimagePlugin extends shopPlugin
{
    /**
     * Handler for frontend_head event: add callbFrontend module in frontend head section
     * @return string
     */
    public function frontendHeader()
    {
        $settings = $this->getSettings();
        $enable = (bool)$settings['enabled'];
        if (!$enable) {
            return;
        }

        if (!empty($_GET['similar_product'])) {
            $this->similar($_GET['similar_product']);
        }

        $this->addCss('css/frontend.css');
        $this->addCss('css/modal.css');
        $this->addJs('js/frontend.js');
        $this->addJs('js/jquery.modal.js');

        return;
    }

    public function similar($id)
    {
        $product_id = (int)$id;
        $indexer = new ProductIndexer();
        $embedding = $indexer->build()->search($product_id);
        $data = null;
        if (!$embedding->isEmpty()) {
            // Получаем изображения
            $data = $indexer->productsImages($embedding);
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => !empty($data),
            'data' => $data,
        ]);
        exit;
    }

    /**
     * URL для страницы, ссылку на которую добавляет плагин в сайдбар редактора товаров.
     * см. также backendProd() и lib/actions/shopEditorhooksPluginBackendProductInfo.action.php
     */
    public function customRouting($params)
    {
        return [
            'products/<id>/idimage/?' => 'backend/productInfo',
        ];
    }

    /**
     * Добавляет пункт меню в сайдбар и кнопку в заголовок.
     */
    public function backendProd(&$params)
    {
        $client = new \IdImage\Support\Client();

        $wa_app_url = wa()->getAppUrl('shop', true);
        $id = intval($params['product']->getId());

        // Пункт в сайдбаре (см. также customRouting())
        $sidebar_item = <<<HTML
        <li>
            <a href="{$wa_app_url}products/$id/idimage/">
                <span>Похожие товары</span>
                <span class="count">
                    <span class="wa-tooltip bottom-left" data-title="Визуально схожие товары">
                        <i class="s-icon fas fa-question-circle"></i>
                    </span>
                </span>
            </a>
        </li>
HTML;

        return [
            'sidebar_item' => $sidebar_item,
        ];
    }
}
