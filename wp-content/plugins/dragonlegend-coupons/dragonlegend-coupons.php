<?php
/**
 * Plugin Name: Dragon Legend Hotel - Quản Lý Mã Ưu Đãi (Coupons & Promo Manager)
 * Plugin URI: https://dragonlegendhotel.com/
 * Description: Hệ thống tạo, quản lý và xác thực mã ưu đãi/voucher giảm giá theo phần trăm (%) hoặc số tiền cố định (VNĐ) cho khách sạn Dragon Legend Hotel.
 * Version: 1.0.0
 * Author: Dragon Legend Hotel Dev Team
 * Text Domain: dragonlegend-coupons
 * Domain Path: /languages
 *
 * @package DragonLegendCoupons
 */

if (!defined('ABSPATH')) {
    exit;
}

define('DL_COUPONS_VERSION', '1.0.0');
define('DL_COUPONS_DIR', plugin_dir_path(__FILE__));
define('DL_COUPONS_URL', plugin_dir_url(__FILE__));

// Nạp các lớp xử lý chức năng
require_once DL_COUPONS_DIR . 'includes/class-coupon-cpt.php';
require_once DL_COUPONS_DIR . 'includes/class-coupon-metaboxes.php';
require_once DL_COUPONS_DIR . 'includes/class-coupon-manager.php';

/**
 * Khởi tạo plugin
 */
function dragonlegend_coupons_init() {
    DragonLegend_Coupon_CPT::init();
    DragonLegend_Coupon_Metaboxes::init();
    DragonLegend_Coupons::init();
}
add_action('plugins_loaded', 'dragonlegend_coupons_init');

/**
 * Hook kích hoạt plugin: Tự động khởi tạo 4 mã ưu đãi mẫu nếu hệ thống chưa có
 */
function dragonlegend_coupons_activate() {
    // Đăng ký CPT trước để wp_insert_post nhận diện
    DragonLegend_Coupon_CPT::register_post_type();

    // Kiểm tra xem đã có mã ưu đãi nào trong CPT chưa
    $existing = get_posts(array(
        'post_type'      => 'dl_coupon',
        'post_status'    => 'any',
        'posts_per_page' => 1,
    ));

    if (empty($existing)) {
        $sample_coupons = array(
            array(
                'code'         => 'DRAGON2026',
                'title'        => 'Mã tri ân khách hàng trực tuyến DRAGON2026',
                'description'  => 'Giảm 10% tri ân khách hàng đặt phòng trực tuyến',
                'type'         => 'percent',
                'value'        => 10,
                'max_discount' => '',
                'min_spend'    => '',
                'usage_limit'  => '',
                'status'       => 'active',
            ),
            array(
                'code'         => 'VIP500K',
                'title'        => 'Đặc quyền nghỉ dưỡng sang trọng VIP 500K',
                'description'  => 'Giảm trực tiếp 500.000 VNĐ cho kỳ nghỉ sang trọng',
                'type'         => 'fixed',
                'value'        => 500000,
                'max_discount' => '',
                'min_spend'    => 2000000,
                'usage_limit'  => 50,
                'status'       => 'active',
            ),
            array(
                'code'         => 'SUMMER20',
                'title'        => 'Ưu đãi chào hè rực rỡ SUMMER20',
                'description'  => 'Giảm 20% gói ưu đãi mùa hè (tối đa 500.000đ)',
                'type'         => 'percent',
                'value'        => 20,
                'max_discount' => 500000,
                'min_spend'    => 1000000,
                'usage_limit'  => 100,
                'status'       => 'active',
            ),
            array(
                'code'         => 'WELCOME100K',
                'title'        => 'Quà tặng chào mừng khách mới WELCOME100K',
                'description'  => 'Giảm ngay 100.000 VNĐ cho lần đặt phòng đầu tiên',
                'type'         => 'fixed',
                'value'        => 100000,
                'max_discount' => '',
                'min_spend'    => 1000000,
                'usage_limit'  => 200,
                'status'       => 'active',
            ),
        );

        foreach ($sample_coupons as $sample) {
            $post_id = wp_insert_post(array(
                'post_type'    => 'dl_coupon',
                'post_title'   => $sample['code'],
                'post_status'  => 'publish',
                'post_author'  => 1,
            ));

            if ($post_id && !is_wp_error($post_id)) {
                update_post_meta($post_id, '_dl_coupon_code', $sample['code']);
                update_post_meta($post_id, '_dl_coupon_description', $sample['description']);
                update_post_meta($post_id, '_dl_coupon_type', $sample['type']);
                update_post_meta($post_id, '_dl_coupon_value', $sample['value']);
                update_post_meta($post_id, '_dl_coupon_max_discount', $sample['max_discount']);
                update_post_meta($post_id, '_dl_coupon_min_spend', $sample['min_spend']);
                update_post_meta($post_id, '_dl_coupon_usage_limit', $sample['usage_limit']);
                update_post_meta($post_id, '_dl_coupon_usage_count', 0);
                update_post_meta($post_id, '_dl_coupon_status', $sample['status']);
                update_post_meta($post_id, '_dl_coupon_rooms', array());
            }
        }
    }
}
register_activation_hook(__FILE__, 'dragonlegend_coupons_activate');
