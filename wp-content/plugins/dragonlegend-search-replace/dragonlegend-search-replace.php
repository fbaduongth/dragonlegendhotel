<?php
/**
 * Plugin Name: Dragon Legend Search & Replace
 * Plugin URI: https://dragonlegendhotel.com/
 * Description: Công cụ Tìm kiếm & Thay thế dữ liệu chuyên sâu cho cơ sở dữ liệu WordPress, hỗ trợ giải mã dữ liệu serialize, JSON, tương thích hoàn toàn cả SQLite và MySQL/MariaDB. Thay thế hoàn hảo cho Better Search Replace mà không thu phí.
 * Version: 1.0.0
 * Author: Dragon Legend Hotel Team
 * Author URI: https://dragonlegendhotel.com/
 * Text Domain: dragonlegend-search-replace
 * License: GPL-2.0+
 */

if (!defined('ABSPATH')) {
    exit;
}

define('DL_SR_VERSION', '1.0.0');
define('DL_SR_PATH', plugin_dir_path(__FILE__));
define('DL_SR_URL', plugin_dir_url(__FILE__));

require_once DL_SR_PATH . 'includes/class-search-replace-engine.php';
require_once DL_SR_PATH . 'includes/class-admin-page.php';

add_action('plugins_loaded', function() {
    DL_Search_Replace_Admin::init();
});
