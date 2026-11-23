<?php
/**
 * Dragon Legend Hotel Core - Coupon & Promotion Bridge
 * Tương thích ngược và chuyển tiếp sang plugin Dragon Legend Coupons
 *
 * @package DragonLegendCore
 */

if (!defined('ABSPATH')) {
    exit;
}

class DragonLegend_Coupon_Manager {

    public static function init() {
        // Chỉ đăng ký endpoint nếu plugin dragonlegend-coupons chưa khởi tạo
        if (!class_exists('DragonLegend_Coupons')) {
            add_action('wp_ajax_dl_validate_coupon', array(__CLASS__, 'ajax_validate_coupon'));
            add_action('wp_ajax_nopriv_dl_validate_coupon', array(__CLASS__, 'ajax_validate_coupon'));
        }
    }

    /**
     * Danh sách mã khuyến mãi mặc định (Hỗ trợ filter mở rộng)
     */
    public static function get_coupons() {
        if (class_exists('DragonLegend_Coupons')) {
            $active = DragonLegend_Coupons::get_active_coupons();
            if (!empty($active)) {
                return $active;
            }
        }

        $default_coupons = array(
            'DRAGON2026' => array(
                'percent' => 10,
                'label'   => __('Giảm 10% tri ân khách hàng trực tuyến', 'dragonlegend-core'),
                'active'  => true
            ),
            'SUMMER20' => array(
                'percent' => 20,
                'label'   => __('Giảm 20% gói ưu đãi mùa hè', 'dragonlegend-core'),
                'active'  => true
            ),
            'VIP500K' => array(
                'fixed'   => 500000,
                'label'   => __('Giảm 500.000 VNĐ cho kỳ nghỉ sang trọng', 'dragonlegend-core'),
                'active'  => true
            ),
            'WELCOME100K' => array(
                'fixed'   => 100000,
                'label'   => __('Giảm 100.000 VNĐ chào mừng khách mới', 'dragonlegend-core'),
                'active'  => true
            ),
        );

        return apply_filters('dragonlegend_promo_codes', $default_coupons);
    }

    /**
     * Xác minh một mã giảm giá
     */
    public static function verify_coupon($code, $subtotal = 0, $room_id = 0) {
        if (class_exists('DragonLegend_Coupons')) {
            return DragonLegend_Coupons::verify_coupon($code, $subtotal, $room_id);
        }

        $code = strtoupper(trim($code));
        $coupons = self::get_coupons();

        if (isset($coupons[$code]) && !empty($coupons[$code]['active'])) {
            $is_fixed = isset($coupons[$code]['fixed']);
            $type = $is_fixed ? 'fixed' : 'percent';
            $val = $is_fixed ? $coupons[$code]['fixed'] : $coupons[$code]['percent'];
            $discount_amount = $is_fixed ? min($subtotal ?: $val, $val) : round((($subtotal ?: 0) * $val) / 100);

            return array(
                'valid'              => true,
                'code'               => $code,
                'discount_type'      => $type,
                'discount_value'     => $val,
                'discount_amount'    => $discount_amount,
                'formatted_discount' => $is_fixed ? number_format($val, 0, ',', '.') . ' VNĐ' : "{$val}%",
                'percent'            => $is_fixed ? 0 : intval($val),
                'label'              => $coupons[$code]['label']
            );
        }

        return array(
            'valid'   => false,
            'message' => __('Mã ưu đãi không hợp lệ hoặc đã hết hạn.', 'dragonlegend-core')
        );
    }

    /**
     * AJAX Endpoint kiểm tra mã ưu đãi từ Frontend
     */
    public static function ajax_validate_coupon() {
        if (class_exists('DragonLegend_Coupons')) {
            DragonLegend_Coupons::ajax_validate_coupon();
            return;
        }

        $code = isset($_POST['code']) ? sanitize_text_field($_POST['code']) : '';
        $subtotal = isset($_POST['subtotal']) ? floatval($_POST['subtotal']) : 0;
        $room_id = isset($_POST['room_id']) ? sanitize_text_field($_POST['room_id']) : 0;

        if (empty($code)) {
            wp_send_json_error(array('message' => __('Vui lòng nhập mã ưu đãi.', 'dragonlegend-core')));
        }

        $result = self::verify_coupon($code, $subtotal, $room_id);
        if ($result['valid']) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error($result);
        }
    }
}
