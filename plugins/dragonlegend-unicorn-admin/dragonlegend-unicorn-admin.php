<?php
/**
 * Plugin Name: Dragon Legend - Unicorn Admin UI
 * Plugin URI: https://dragonlegendhotel.com
 * Description: Giao diện quản trị WP-Admin và màn hình đăng nhập phong cách Unicorn hiện đại, trẻ trung, chuẩn xác cho Dragon Legend Hotel.
 * Version: 1.1.0
 * Author: Dragon Legend Hotel
 * Text Domain: dragonlegend-unicorn-admin
 */

if (!defined('ABSPATH')) {
    exit;
}

define('DL_UNICORN_ADMIN_VERSION', '1.1.0');
define('DL_UNICORN_ADMIN_URL', plugin_dir_url(__FILE__));
define('DL_UNICORN_ADMIN_PATH', plugin_dir_path(__FILE__));

class DL_Unicorn_Admin {

    public function __construct() {
        // Enqueue admin styles & scripts with high priority
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'), 9999);
        
        // Enqueue login page styles
        add_action('login_enqueue_scripts', array($this, 'enqueue_login_assets'), 9999);
        
        // Replace default Welcome Panel with Unicorn Full-width Hero
        remove_action('welcome_panel', 'wp_welcome_panel');
        add_action('welcome_panel', array($this, 'render_dashboard_welcome_hero'));
        add_filter('get_user_metadata', array($this, 'force_show_welcome_panel'), 10, 4);

        // Customize admin footer
        add_filter('admin_footer_text', array($this, 'custom_admin_footer_text'), 99);
        add_filter('update_footer', array($this, 'custom_admin_footer_version'), 99);

        // Login page customization
        add_filter('login_headerurl', array($this, 'custom_login_logo_url'));
        add_filter('login_headertext', array($this, 'custom_login_logo_title'));

        // Admin head branding injection
        add_action('admin_head', array($this, 'inject_admin_brand_header'));
        
        // Add body class for scoping
        add_filter('admin_body_class', array($this, 'add_admin_body_class'));
        add_filter('login_body_class', array($this, 'add_login_body_class'));
    }

    /**
     * Add scoped body class for safe styling
     */
    public function add_admin_body_class($classes) {
        $classes .= ' dl-unicorn-admin-theme';
        return $classes;
    }

    public function add_login_body_class($classes) {
        $classes[] = 'dl-unicorn-login-theme';
        return $classes;
    }

    /**
     * Enqueue Admin Assets
     */
    public function enqueue_admin_assets() {
        // Google Font: Plus Jakarta Sans
        wp_enqueue_style(
            'dl-unicorn-google-font',
            'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap',
            array(),
            null
        );

        // Main Unicorn Admin CSS
        wp_enqueue_style(
            'dl-unicorn-admin-style',
            DL_UNICORN_ADMIN_URL . 'assets/css/unicorn-admin.css',
            array(),
            DL_UNICORN_ADMIN_VERSION
        );

        // Unicorn Admin JS
        wp_enqueue_script(
            'dl-unicorn-admin-script',
            DL_UNICORN_ADMIN_URL . 'assets/js/unicorn-admin.js',
            array('jquery'),
            DL_UNICORN_ADMIN_VERSION,
            true
        );

        // Pass localized data to script
        $current_user = wp_get_current_user();
        wp_localize_script('dl-unicorn-admin-script', 'dlUnicornData', array(
            'userDisplayName' => esc_html($current_user->display_name ?: $current_user->user_login),
            'homeUrl'         => esc_url(home_url('/')),
            'adminUrl'        => esc_url(admin_url()),
            'hotelName'       => 'Dragon Legend Hotel',
        ));
    }

    /**
     * Enqueue Login Assets
     */
    public function enqueue_login_assets() {
        // Google Font
        wp_enqueue_style(
            'dl-unicorn-google-font-login',
            'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap',
            array(),
            null
        );

        // Unicorn Login CSS
        wp_enqueue_style(
            'dl-unicorn-login-style',
            DL_UNICORN_ADMIN_URL . 'assets/css/unicorn-login.css',
            array(),
            DL_UNICORN_ADMIN_VERSION
        );
    }

    /**
     * Inject Unicorn Brand Item cleanly at top of #adminmenu
     */
    public function inject_admin_brand_header() {
        ?>
        <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            var adminMenu = document.getElementById('adminmenu');
            if (adminMenu && !document.getElementById('dl-unicorn-brand-item')) {
                var brandLi = document.createElement('li');
                brandLi.id = 'dl-unicorn-brand-item';
                brandLi.className = 'dl-unicorn-brand-item';
                brandLi.innerHTML = `
                    <a href="<?php echo esc_url(admin_url()); ?>" class="dl-brand-link" title="Dragon Legend Hotel">
                        <span class="dl-brand-icon">🦄</span>
                        <div class="dl-brand-info">
                            <span class="dl-brand-title">DRAGON LEGEND</span>
                            <span class="dl-brand-badge">PRO</span>
                        </div>
                    </a>
                `;
                adminMenu.insertBefore(brandLi, adminMenu.firstChild);
            }
        });
        </script>
        <?php
    }

    /**
     * Force show welcome panel on dashboard
     */
    public function force_show_welcome_panel($val, $object_id, $meta_key, $single) {
        if ($meta_key === 'show_welcome_panel') {
            return 1;
        }
        return $val;
    }

    /**
     * Render Full-Width Unicorn Welcome Hero Banner
     */
    public function render_dashboard_welcome_hero() {
        $current_user = wp_get_current_user();
        $user_name = esc_html($current_user->display_name ?: $current_user->user_login);
        
        // Count rooms and posts
        $rooms_count = wp_count_posts('dl_room');
        $total_rooms = isset($rooms_count->publish) ? $rooms_count->publish : 0;
        
        $posts_count = wp_count_posts('post');
        $total_posts = isset($posts_count->publish) ? $posts_count->publish : 0;

        ?>
        <div class="dl-unicorn-hero-banner">
            <div class="dl-hero-content">
                <div class="dl-hero-top-row">
                    <div class="dl-hero-greeting-pill">
                        <span class="dl-pill-dot"></span>
                        <span id="dl-greeting-time">Chào ngày mới năng động!</span>
                    </div>
                    <div class="dl-hero-badge-tag">DRAGON LEGEND UNICORN ADMIN</div>
                </div>

                <h1 class="dl-hero-title">
                    Xin chào, <span class="dl-hero-name"><?php echo $user_name; ?></span>! 🦄
                </h1>
                <p class="dl-hero-desc">
                    Hệ thống quản trị <strong>Dragon Legend Hotel</strong> đang vận hành mượt mà với tiêu chuẩn chất lượng cao. Chúc bạn có một ngày làm việc tràn đầy năng lượng & hiệu quả!
                </p>
                
                <!-- Quick Stats Strip -->
                <div class="dl-hero-stats">
                    <div class="dl-stat-item">
                        <span class="dl-stat-num"><?php echo esc_html($total_rooms); ?></span>
                        <span class="dl-stat-label">Hạng phòng nghỉ</span>
                    </div>
                    <div class="dl-stat-sep"></div>
                    <div class="dl-stat-item">
                        <span class="dl-stat-num"><?php echo esc_html($total_posts); ?></span>
                        <span class="dl-stat-label">Bài viết & Dịch vụ</span>
                    </div>
                    <div class="dl-stat-sep"></div>
                    <div class="dl-stat-item">
                        <span class="dl-stat-num">4.9★</span>
                        <span class="dl-stat-label">Đánh giá khách hàng</span>
                    </div>
                </div>

                <!-- Action Shortcuts -->
                <div class="dl-hero-actions">
                    <a href="<?php echo esc_url(admin_url('edit.php?post_type=dl_room')); ?>" class="dl-btn-pill dl-btn-gradient">
                        <span class="dashicons dashicons-building"></span> Quản lý Phòng Nghỉ
                    </a>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=dragonlegend-content-manager')); ?>" class="dl-btn-pill dl-btn-outline">
                        <span class="dashicons dashicons-admin-site-alt3"></span> Quản lý Nội Dung Tĩnh
                    </a>
                    <a href="<?php echo esc_url(admin_url('post-new.php')); ?>" class="dl-btn-pill dl-btn-outline">
                        <span class="dashicons dashicons-edit"></span> Viết Bài Mới
                    </a>
                    <a href="<?php echo esc_url(home_url('/')); ?>" target="_blank" class="dl-btn-pill dl-btn-glass">
                        <span class="dashicons dashicons-external"></span> Xem Website ↗
                    </a>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Custom Footer Text
     */
    public function custom_admin_footer_text() {
        return '<span class="dl-unicorn-footer-brand">✨ <strong>Dragon Legend Hotel</strong> &bull; Giao diện quản trị phong cách <span class="dl-footer-unicorn-gradient">Unicorn Modern UI</span></span>';
    }

    public function custom_admin_footer_version() {
        return '<span class="dl-footer-pill">Phiên bản 1.1.0 🦄</span>';
    }

    /**
     * Custom Login URL and Title
     */
    public function custom_login_logo_url() {
        return home_url('/');
    }

    public function custom_login_logo_title() {
        return 'Dragon Legend Hotel &bull; Trang Đăng Nhập Quản Trị';
    }
}

new DL_Unicorn_Admin();
