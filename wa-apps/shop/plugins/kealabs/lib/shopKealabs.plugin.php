<?php

class shopKealabsPlugin extends shopPlugin
{
    const KEA_COOKIE_NAME = "kea_tracking_data";
    const BUNDLE_BASE = "https://kealabs.com/inc/api/platform/webasyst/";

    const PLUGIN_RECS = "kealabs";
    const PLUGIN_SEARCH = "kealabs_search";
    const MY_PLUGIN_ID = self::PLUGIN_RECS;

    /**
     * Inject Kea Labs bundle into head of the page
     */
    public function frontendHead()
    {
        $head = "\n<!-- Kea Labs-->\n";
        $version = wa('shop')->getVersion();
        $version = substr($version, 0, strpos($version, "."));
        $token = $this->getSettings('token');
        $enabled = $this->getSettings('enabled');
        $uuid = $this->getSettings('uuid');
        $base = self::BUNDLE_BASE . $version;
        if (!empty($token)) {
            $head .= '<script async src="' . $base . "/" . $token . '/kea.min.js" type="text/javascript"></script>';
            $head .= '<div class="kea-data" data-type="token" data-value="' . $token . '" style="display: none;"></div>';
        } else {
            $head .= '<script async src="' . $base . '/kea.default.min.js" type="text/javascript"></script>';
        }
        $head .= '<div class="kea-data" data-type="enabled" data-value="' . $enabled . '" style="display: none;"></div>';
        $head .= '<div class="kea-data" data-type="uuid" data-value="' . $uuid . '" style="display: none;"></div>';
        $head .= "\n<!-- Kea Labs-->\n";
        return $head;
    }

    /**
     * Inject data needed for kealabs plugin
     */
    public function frontendProduct($product)
    {
        $productId = $product['id'];
        return array(
            'block' => '<div class="kea-data" data-type="page_type" data-value="product" style="display: none;"></div>'
                . '<div id="kea-marker-productId" style="display: none;">' . $productId . '</div>'
        );
    }

    /**
     * Inject data needed for kealabs plugin
     */
    public function frontendCategory($category)
    {
        $categoryId = $category['id'];
        return '<div class="kea-data" data-type="page_type" data-value="category" style="display: none;"></div>'
        . '<div id="kea-marker-categoryId" style="display: none;">' . $categoryId . '</div>';
    }

    /**
     * Inject data needed for kealabs plugin
     */
    public function frontendSearch()
    {
        return '<div class="kea-data" data-type="page_type" data-value="search_result" style="display: none;"></div>';
    }

    /**
     * Inject data needed for kealabs plugin
     */
    public function frontendCart()
    {
        return '<div class="kea-data" data-type="page_type" data-value="cart" style="display: none;"></div>';
    }

    /**
     * Inject data needed for kealabs plugin
     */
    public function frontendHomepage()
    {
        return '<div class="kea-data" data-type="page_type" data-value="homepage" style="display: none;"></div>';
    }

    /**
     * Hook for cart_add event.
     */
    public function cartAdd($params)
    {
        self::trackEvent(array(
            'action' => 'product_add',
            'productId' => $params["product_id"],
            'fullMode' => true,
            'quantity' => $params["quantity"],
        ));
    }

    /**
     * Hook for cart_delete event.
     */
    public function cartDelete($params)
    {
        self::trackEvent(array(
            'action' => 'product_remove',
            'productId' => $params["product_id"],
            'fullMode' => true,
            'quantity' => 0,
        ));
    }

    /**
     * Hook for order_action.create event
     */
    public function orderActionCreate($order)
    {
        try {
            $orderItemsModel = new shopOrderItemsModel();
            $orderItems = $orderItemsModel->getItems($order['order_id']);
            $items = array();
            foreach ($orderItems as $item) {
                array_push($items, array(
                    'productId' => $item["product_id"],
                    'quantity' => $item["quantity"],
                    'price' => $item["price"]
                ));
            }
            self::trackEvent(array(
                'action' => "order_placed",
                'orderId' => $order['order_id'],
                'items' => $items
            ));
        } catch (Exception $ex) {
            self::logError('Unexpected error in orderActionCreate:' . $ex->getMessage());
        }
    }

    /**
     * Set data for tracking. Special cookie is used for data propagation.
     * After setting up a value for the cookie, kealabs library takes it's and performs required actions.
     * i.e. notify backend
     */
    public function trackEvent($event)
    {
        $cookie = $_COOKIE[self::KEA_COOKIE_NAME];
        $arr = (empty($cookie)) ? array() : json_decode($cookie);
        array_push($arr, $event);
        setcookie(self::KEA_COOKIE_NAME, json_encode($arr), 0, '/');
    }

    /**
     * Helper function to setup value for a single parameter
     */
    public static function setSettings($name, $value)
    {
        try {
            $plugin = wa()->getPlugin(self::MY_PLUGIN_ID);
            if ($plugin->settings !== null) {
                $plugin->settings[$name] = $value;
            }
            $plugin->getSettingsModel()->set($plugin->getSettingsKey(), $name, is_array($value) ? json_encode($value) : $value);
        } catch (Exception $ex) {
            self::logError('Unexpected error in setSettings:' . $ex->getMessage());
        }
    }

    /**
     * Helper logging function. Data is provided to kealabs library though trackEvent()
     */
    private function log($level, $data)
    {
        self::trackEvent(array(
            'action' => 'backend_logging',
            'platform' => 'webasyst',
            'plugin' => 'kealabs',
            'level' => $level,
            'data' => json_encode($data)
        ));
    }

    private function logError($data)
    {
        self::log("ERROR", $data);
    }
}
