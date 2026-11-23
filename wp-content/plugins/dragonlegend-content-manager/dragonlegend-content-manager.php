<?php
/**
 * Plugin Name: Dragon Legend - Quản Lý Nội Dung Tĩnh (Static Content Manager)
 * Plugin URI: https://dragonlegendhotel.com
 * Description: Plugin quản lý toàn bộ nội dung tĩnh, câu chữ, tiêu đề, số liệu thống kê, ảnh banner và thông tin liên hệ trên website Dragon Legend Hotel.
 * Version: 1.0.0
 * Author: Dragon Legend Hotel Dev Team
 * Text Domain: dragonlegend-content-manager
 * Domain Path: /languages
 *
 * @package DragonLegendContentManager
 */

if (!defined('ABSPATH')) {
    exit;
}

define('DL_CONTENT_MANAGER_VERSION', '1.0.0');
define('DL_CONTENT_MANAGER_DIR', plugin_dir_path(__FILE__));
define('DL_CONTENT_MANAGER_URL', plugin_dir_url(__FILE__));

// Nạp file xử lý chính
require_once DL_CONTENT_MANAGER_DIR . 'includes/class-content-manager.php';

// Khởi chạy plugin
add_action('plugins_loaded', array('DragonLegend_Content_Manager', 'init'));

/**
 * Helper toàn cục: Lấy giá trị nội dung tĩnh
 * 
 * @param string $key Tên trường nội dung
 * @param string $fallback Giá trị dự phòng nếu chưa cài đặt
 * @return mixed
 */
function dl_get_content($key, $fallback = '') {
    return DragonLegend_Content_Manager::get($key, $fallback);
}

/**
 * Helper toàn cục: Lấy danh sách 12 tiện nghi nổi bật đã cấu hình
 * 
 * @return array
 */
function dl_get_popular_amenities() {
    if (class_exists('DragonLegend_Content_Manager')) {
        return DragonLegend_Content_Manager::get_popular_amenities();
    }
    return array();
}

