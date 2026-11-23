<?php
/**
 * Plugin Name: Dragon Legend Hotel Core
 * Plugin URI: https://dragonlegendhotel.com/
 * Description: Plugin quản lý hệ thống khách sạn Dragon Legend Hotel: Quản trị hạng phòng, khuyến mãi, tiện ích, đánh giá, đơn đặt phòng và hệ thống metabox tùy biến mở rộng.
 * Version: 1.0.0
 * Author: Dragon Legend Hotel Team
 * Author URI: https://dragonlegendhotel.com/
 * Text Domain: dragonlegend-core
 * Domain Path: /languages
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define('DL_CORE_VERSION', '1.0.0');
define('DL_CORE_PATH', plugin_dir_path(__FILE__));
define('DL_CORE_URL', plugin_dir_url(__FILE__));

// Load Core Components
require_once DL_CORE_PATH . 'includes/class-post-types.php';
require_once DL_CORE_PATH . 'includes/class-metaboxes.php';
require_once DL_CORE_PATH . 'includes/class-coupon-manager.php';
require_once DL_CORE_PATH . 'includes/class-zalo-service.php';
require_once DL_CORE_PATH . 'includes/class-smtp-service.php';
require_once DL_CORE_PATH . 'includes/class-booking-settings.php';
require_once DL_CORE_PATH . 'includes/class-booking-handler.php';
require_once DL_CORE_PATH . 'includes/class-newsletter-handler.php';
require_once DL_CORE_PATH . 'includes/sample-data.php';

/**
 * Initialize Plugin
 */
function dl_core_init() {
    // Khởi tạo Custom Post Types & Taxonomies
    DragonLegend_Post_Types::init();

    // Khởi tạo Quản trị Metaboxes (OOP)
    DragonLegend_Metaboxes::init();

    // Khởi tạo Quản lý Mã giảm giá (Coupons)
    DragonLegend_Coupon_Manager::init();

    // Khởi tạo Dịch vụ Máy Chủ Mail SMTP
    DragonLegend_SMTP_Service::init();

    // Khởi tạo Cấu hình Thông báo Đặt phòng (Zalo & Email)
    DragonLegend_Booking_Settings::init();

    // Khởi tạo AJAX Booking Handler
    DragonLegend_Booking_Handler::init();

    // Khởi tạo AJAX Newsletter Subscription Handler
    DragonLegend_Newsletter_Handler::init();
}
add_action('plugins_loaded', 'dl_core_init');

/**
 * Activation Hook - Flush rewrite rules and import sample data if empty
 */
register_activation_hook(__FILE__, function() {
    DragonLegend_Post_Types::register_post_types();
    DragonLegend_Post_Types::register_taxonomies();
    flush_rewrite_rules();

    // Tự động nạp dữ liệu mẫu ban đầu nếu chưa có phòng nào
    DragonLegend_Sample_Data::init_sample_data_if_empty();
});

/**
 * Deactivation Hook - Flush rewrite rules
 */
register_deactivation_hook(__FILE__, function() {
    flush_rewrite_rules();
});

/**
 * Enqueue Admin Assets (Styling for custom metaboxes & media uploader)
 */
function dl_core_admin_assets($hook) {
    global $post_type;
    $supported_types = array('dl_room', 'dl_offer', 'dl_amenity', 'dl_testimonial', 'dl_booking');

    if (in_array($post_type, $supported_types)) {
        wp_enqueue_media();
        wp_enqueue_style('dl-core-admin', DL_CORE_URL . 'assets/css/admin.css', array(), DL_CORE_VERSION);
        wp_enqueue_script('dl-core-admin', DL_CORE_URL . 'assets/js/admin.js', array('jquery'), DL_CORE_VERSION, true);
    }
}
add_action('admin_enqueue_scripts', 'dl_core_admin_assets');

/**
 * Bảo vệ plugin SQLite Database Integration tránh bị tắt nhầm (website dùng SQLite)
 */
add_filter('plugin_action_links_sqlite-database-integration/load.php', function($actions) {
    if (isset($actions['deactivate'])) {
        $actions['deactivate'] = '<span style="color: #999; cursor: not-allowed;" title="Website đang vận hành trên SQLite. Không thể tắt để tránh mất kết nối CSDL.">Không thể tắt (Database chính)</span>';
    }
    return $actions;
});
