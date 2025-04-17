<?php

class shopMarketingCouponsAction extends shopMarketingViewAction
{

    public function execute()
    {
        $id = waRequest::param('coupon_id');
        $page_number = waRequest::get('page', null, waRequest::TYPE_INT);
        $coupon_name = waRequest::get('coupon_name', '', waRequest::TYPE_STRING_TRIM);

        if ($id == 'create') {
            $id = null;
            $is_create_action = true;
        }

        if (!empty($id)) {
            $id = (int)$id;
        }

        $coupm = new shopCouponModel();
        $set_model = new shopSetModel();
        $curm = new shopCurrencyModel();
        $type_model = new shopTypeModel();

        $currencies = $curm->getAll('code');

        if (strlen($coupon_name)) {
            $coupons_count = $coupm->select('COUNT(*) AS count')->where("code LIKE ?", ['%' . $coupon_name . '%'])->fetchField('count');
        } else {
            $coupons_count = $coupm->countAll();
        }
        $pages_count = ceil($coupons_count / $coupm::PAGES_STEP);
        if ($id && $page_number == null) {
            $page_number = $coupm->getPageNumber($id, $coupon_name);
        }
        if ($page_number < 1) {
            $page_number = 1;
        } elseif ($page_number > $pages_count && $pages_count > 0) {
            $page_number = $pages_count;
        }
        $offset = $page_number * $coupm::PAGES_STEP - $coupm::PAGES_STEP;

        if (strlen($coupon_name)) {
            $coupons = $coupm->where("code LIKE ?", ['%' . $coupon_name . '%'])->order('id DESC')->limit($offset . ',' . $coupm::PAGES_STEP)->fetchAll('id');
        } else {
            $coupons = $coupm->order('id DESC')->limit($offset . ',' . $coupm::PAGES_STEP)->fetchAll('id');
        }
        foreach ($coupons as &$c) {
            $c['enabled'] = shopCouponModel::isEnabled($c);
            $c['hint'] = shopCouponModel::formatValue($c, $currencies);
        }
        unset($c);

        $this->view->assign('coupons', $coupons);

        if (empty($is_create_action) && empty($id) && !empty($coupons)) {
            $first_coupon = current($coupons);
            $id = $first_coupon['id'];
        }

        if (!empty($id) && !empty($coupons[$id])) {
            $coupon = $coupons[$id];
        }

        if (!empty($coupon)) {
            $coupon['value'] = (float)$coupon['value'];
        } else {
            if ($id) {
                throw new waException(_w('Coupon not found.'), 404);
            } else {
                // show form to create new coupon
                $coupon = $coupm->getEmptyRow();
                $coupon['code'] = self::generateCode();
            }
        }

        if (!$id) {
            $hash = shopImportexportHelper::getCollectionHash(waRequest::get('products_hash'));
            $coupon['products_hash'] = $hash['hash'];
        }

        // Coupon types
        $currencies = $curm->getAll('code');
        $types = self::getTypes($currencies);

        // Orders this coupon was used for
        $orders = array();
        $overall_discount = 0;
        $overall_discount_formatted = '';
        if ($coupon['id']) {
            $om = new shopOrderModel();
            $orders = $om->getByCoupon($coupon['id']);
            shopHelper::workupOrders($orders);
            foreach ($orders as &$o) {
                $discount = (float)ifset($o['params']['coupon_discount'], 0);
                $o['coupon_discount_formatted'] = waCurrency::format('%{h}', $discount, $o['currency']);
                if ($discount) {
                    $overall_discount += $curm->convert($discount, $o['currency'], $curm->getPrimaryCurrency());
                    $o['coupon_discount_percent'] = round($discount * 100.0 / ($discount + $o['total']), 1);
                } else {
                    $o['coupon_discount_percent'] = 0;
                }
            }
            unset($o);
            $overall_discount_formatted = waCurrency::format('%{h}', $overall_discount, $curm->getPrimaryCurrency());
        }

        $this->view->assign('product_sets', $set_model->getByField('type', shopSetModel::TYPE_STATIC, $set_model->getTableId()));
        $this->view->assign('product_types', $type_model->getTypes());
        $this->view->assign('types', $types);
        $this->view->assign('orders', $orders);
        $this->view->assign('coupon', $coupon);
        $this->view->assign('overall_discount', $overall_discount);
        $this->view->assign('overall_discount_formatted', $overall_discount_formatted);
        $this->view->assign('formatted_value', shopCouponModel::formatValue($coupon, $currencies));
        $this->view->assign('is_enabled', shopCouponModel::isEnabled($coupon));
        $this->view->assign('pages_count', $pages_count);
        $this->view->assign('page_number', $page_number);
        $this->view->assign('coupon_name', $coupon_name);

    }

    public static function getTypes($currencies)
    {
        $result = array(
            '%' => _w('% Discount'),
        );
        foreach ($currencies as $c) {
            $info = waCurrency::getInfo($c['code']);
            $result[$c['code']] = $info['sign'].' '.$c['code'];
        }
        $result['$FS'] = _w('Free shipping');
        return $result;
    }

    public static function generateCode()
    {
        $alphabet = "QWERTYUIOPASDFGHJKLZXCVBNM1234567890";
        $result = '';
        while (strlen($result) < 8) {
            $result .= $alphabet[mt_rand(0, strlen($alphabet) - 1)];
        }
        return $result;
    }
}
