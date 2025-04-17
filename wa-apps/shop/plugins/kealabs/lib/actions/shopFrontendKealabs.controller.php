<?php

/**
 * This controller serves catalog data for recommendations.
 * This method is protected by secret key, shared between shop and kea labs service.
 * Nobody else may load the data.
 *
 * This is memory-efficient implementation and support pagination
 */
class shopFrontendKealabsController extends waJsonController
{
    const PLUGIN_ID = "kealabs";

    function execute()
    {
        $job = waRequest::get('job', "product");
        $limit = waRequest::get('limit', 25);
        $offset = waRequest::get('skip', 0);
        $escape = waRequest::get('escape', true);
        $secret = waRequest::get('secret');

        $data = null;
        if ($job == "meta") {
            $data = self::returnMetadata();
        } else {
            $correctSecret = wa()->getPlugin(self::PLUGIN_ID)->getSettings('secret');
            if (empty($secret) || $secret != $correctSecret) {
                $this->setError(_wp('Invalid key'));
                return;
            }
            if ($job == "category") {
                $data = self::returnCategories();
            } else {
                $data = self::returnProducts($offset, $limit, $escape);
            }
        }
        $this->getResponse()->addHeader('Content-type', 'application/json');
        $this->response = $data;
    }

    private function returnMetadata()
    {
        $appVersion = wa()->getVersion('shop');
        $appDetails = wa()->getConfig()->getAppConfig('shop');
        $pluginDetails = $appDetails->getPluginInfo(self::PLUGIN_ID);
        $productsCollection = new shopProductsCollection();
        $primaryCurrency = wa()->getConfig()->getCurrency(true);
        $plugin = wa()->getPlugin(self::PLUGIN_ID);
        $token = $plugin->getSettings('token');
        $secret = $plugin->getSettings('secret');
        $configured = !(empty($token) || empty($secret));
        return array(
            'shopVersion' => $appVersion,
            'kealabsVersion' => $pluginDetails['version'],
            'uuid' => $plugin->getSettings('uuid'),
            'enabled' => $plugin->getSettings('enabled'),
            'configured' => $configured,
            'products_count' => $productsCollection->count(),
            'currency' => $primaryCurrency,
            'currencies' => self::getCurrenciesConfig($primaryCurrency)
        );
    }

    private function getCurrenciesConfig($primary)
    {
        $config = wa()->getConfig();
        $currencies = array();
        if (!empty($primary)) {
            $available_currencies = $config->getCurrencies();
            foreach ($available_currencies as $currency) {
                $currencies[$currency['code']] = array(
                    'value' => $currency['code'],
                    'title' => $currency['title'],
                    'rate' => $currency['rate'],
                );
            }
        }
        return $currencies;
    }

    private function returnCategories()
    {
        $model = new shopCategoryModel();
        $categories = $model->getTree(0, null, false, $this->data['domain']);
        foreach ($categories as $id => $category) {
            if ($category['type'] != shopCategoryModel::TYPE_STATIC) {
                unset($categories[$id]);
            }
        }
        return $categories;
    }

    private function returnProducts($offset, $limit, $escape)
    {
        $features_model = new shopProductFeaturesModel();
        $related_model = new shopProductRelatedModel();
        $collection = new shopProductsCollection();
        $products = $collection->getProducts("*", $offset, $limit, $escape);
        $result = array();
        foreach ($products as $product) {
            $product["thumbnail"] = shopImage::getUrl(array(
                'product_id' => $product["id"],
                'id' => $product['image_id'],
                'ext' => $product['ext']
            ), 200, true);
            $product["features"] = $features_model->getValues($product['id']);
            $product["related"] = $related_model->getAllRelated($product['id']);
            array_push($result, $product);
        }
        return $result;
    }
}