<?php
/**
 * Plugin Name: Dragon Legend Auto Translator
 * Plugin URI: https://dragonlegendhotel.com
 * Description: Plugin dịch ngôn ngữ tự động đa ngôn ngữ (Tiếng Việt mặc định, Tiếng Anh, Tiếng Trung) với cơ chế lưu phiên người dùng (Session Cookie) và bảo toàn 100% thứ hạng SEO Google.
 * Version: 1.1.0
 * Author: Dragon Legend Hotel / Viettechsoft
 * Author URI: https://viettechsoft.com
 * Text Domain: dragonlegend-translator
 *
 * @package DragonLegendTranslator
 */

if (!defined('ABSPATH')) {
    exit;
}

define('DL_TRANSLATOR_VERSION', '1.1.0');
define('DL_TRANSLATOR_DIR', plugin_dir_path(__FILE__));
define('DL_TRANSLATOR_URL', plugin_dir_url(__FILE__));

class DragonLegend_Auto_Translator {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Enqueue Assets
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'), 99);

        // Render hidden Google Translate container
        add_action('wp_footer', array($this, 'render_google_container'), 999);

        // Hook after VR Tour in theme
        add_action('dragonlegend_after_vr_tour', array($this, 'render_switcher'));
        add_action('dragonlegend_mobile_language_switcher', array($this, 'render_mobile_switcher'));

        // Shortcode support
        add_shortcode('dl_language_switcher', array($this, 'shortcode_switcher'));

        // Admin Menu Settings
        add_action('admin_menu', array($this, 'register_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    /**
     * Kiểm tra xem request có phải từ Googlebot / Bingbot / Search crawler không
     */
    public static function is_search_bot() {
        $user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? strtolower($_SERVER['HTTP_USER_AGENT']) : '';
        if (empty($user_agent)) {
            return false;
        }

        $bots = array(
            'googlebot', 'bingbot', 'slurp', 'duckduckbot', 'baiduspider',
            'yandexbot', 'sogou', 'exabot', 'facebot', 'ia_archiver'
        );

        foreach ($bots as $bot) {
            if (strpos($user_agent, $bot) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Enqueue CSS, JS & Google Translate API
     */
    public function enqueue_scripts() {
        // Không tải script dịch đối với Search Bot để tối ưu SEO 100%
        if (self::is_search_bot()) {
            return;
        }

        wp_enqueue_style(
            'dl-translator-css',
            DL_TRANSLATOR_URL . 'assets/css/translator.css',
            array(),
            DL_TRANSLATOR_VERSION
        );

        $js_ver = file_exists(DL_TRANSLATOR_DIR . 'assets/js/translator.js')
            ? filemtime(DL_TRANSLATOR_DIR . 'assets/js/translator.js')
            : DL_TRANSLATOR_VERSION;

        wp_enqueue_script(
            'dl-translator-js',
            DL_TRANSLATOR_URL . 'assets/js/translator.js',
            array(),
            $js_ver,
            true
        );

        add_filter('script_loader_tag', function($tag, $handle) {
            if ('dl-translator-js' === $handle && strpos($tag, ' defer') === false) {
                return str_replace(' src', ' defer src', $tag);
            }
            return $tag;
        }, 10, 2);
    }

    /**
     * Khung chứa ẩn cho Google Translate Element
     */
    public function render_google_container() {
        if (self::is_search_bot()) {
            return;
        }
        echo '<div id="dl_google_translate_element" style="display:none!important;" aria-hidden="true"></div>';
    }

    /**
     * Trả về icon cờ quốc gia dạng SVG sắc nét (Hiển thị đồng nhất 100% trên cả Windows, macOS, iOS, Android)
     */
    public static function get_flag_svg($lang) {
        switch ($lang) {
            case 'vi':
                return '<svg class="dl-flag-svg" width="16" height="12" viewBox="0 0 24 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><rect width="24" height="18" rx="2" fill="#DA251D"/><path d="M12 3.6L13.347 7.746H17.706L14.18 10.308L15.527 14.454L12 11.892L8.473 14.454L9.82 10.308L6.294 7.746H10.653L12 3.6Z" fill="#FFFF00"/></svg>';
            case 'en':
                return '<svg class="dl-flag-svg" width="16" height="12" viewBox="0 0 24 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><rect width="24" height="18" rx="2" fill="#012169"/><path d="M0 0L24 18M24 0L0 18" stroke="#FFFFFF" stroke-width="3.2"/><path d="M0 0L24 18M24 0L0 18" stroke="#C8102E" stroke-width="1.6"/><path d="M12 0V18M0 9H24" stroke="#FFFFFF" stroke-width="5.2"/><path d="M12 0V18M0 9H24" stroke="#C8102E" stroke-width="3.2"/></svg>';
            case 'zh-CN':
                return '<svg class="dl-flag-svg" width="16" height="12" viewBox="0 0 24 18" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><rect width="24" height="18" rx="2" fill="#EE1C25"/><path d="M5.4 2.7L6.18 5.1H8.7L6.66 6.58L7.44 8.98L5.4 7.5L3.36 8.98L4.14 6.58L2.1 5.1H4.62L5.4 2.7Z" fill="#FFFF00"/><circle cx="10.2" cy="2.4" r="0.75" fill="#FFFF00"/><circle cx="11.8" cy="4.2" r="0.75" fill="#FFFF00"/><circle cx="11.8" cy="6.8" r="0.75" fill="#FFFF00"/><circle cx="10.2" cy="8.6" r="0.75" fill="#FFFF00"/></svg>';
            default:
                return '';
        }
    }

    /**
     * Trả về tên hiển thị của các ngôn ngữ tương ứng với ngôn ngữ đang chọn
     */
    public static function get_lang_labels($active_lang = 'vi') {
        $map = array(
            'vi' => array(
                'vi'    => 'Tiếng Việt',
                'en'    => 'Tiếng Anh',
                'zh-CN' => 'Tiếng Trung',
            ),
            'en' => array(
                'vi'    => 'Vietnamese',
                'en'    => 'English',
                'zh-CN' => 'Chinese',
            ),
            'zh-CN' => array(
                'vi'    => '越南语',
                'en'    => '英语',
                'zh-CN' => '中文',
            ),
        );
        return isset($map[$active_lang]) ? $map[$active_lang] : $map['vi'];
    }

    /**
     * Hiển thị thanh chuyển ngôn ngữ Desktop (Phía sau Tour VR 360°)
     */
    public function render_switcher() {
        $current_lang = isset($_COOKIE['googtrans']) ? str_replace('/vi/', '', $_COOKIE['googtrans']) : 'vi';
        if (empty($current_lang) || !in_array($current_lang, array('vi', 'en', 'zh-CN'), true)) {
            $current_lang = 'vi';
        }
        $labels = self::get_lang_labels($current_lang);
        ?>
        <div class="dl-lang-switcher-wrap inline-flex items-center gap-1.5 notranslate" translate="no">
            <span class="text-slate-600 hidden lg:inline">|</span>
            <div class="dl-lang-switcher" role="navigation" aria-label="<?php esc_attr_e('Chọn ngôn ngữ', 'dragonlegend-translator'); ?>">
                <!-- 1. Tiếng Việt (Mặc định) -->
                <button type="button" 
                        data-lang="vi" 
                        class="dl-lang-btn <?php echo ($current_lang === 'vi') ? 'active' : ''; ?>" 
                        title="<?php echo esc_attr($labels['vi']); ?>"
                        aria-label="<?php echo esc_attr($labels['vi']); ?>">
                    <span class="flag-emoji"><?php echo self::get_flag_svg('vi'); ?></span>
                    <span class="dl-lang-label"><?php echo esc_html($labels['vi']); ?></span>
                </button>

                <span class="dl-lang-divider">|</span>

                <!-- 2. Tiếng Anh -->
                <button type="button" 
                        data-lang="en" 
                        class="dl-lang-btn <?php echo ($current_lang === 'en') ? 'active' : ''; ?>" 
                        title="<?php echo esc_attr($labels['en']); ?>"
                        aria-label="<?php echo esc_attr($labels['en']); ?>">
                    <span class="flag-emoji"><?php echo self::get_flag_svg('en'); ?></span>
                    <span class="dl-lang-label"><?php echo esc_html($labels['en']); ?></span>
                </button>

                <span class="dl-lang-divider">|</span>

                <!-- 3. Tiếng Trung -->
                <button type="button" 
                        data-lang="zh-CN" 
                        class="dl-lang-btn <?php echo ($current_lang === 'zh-CN') ? 'active' : ''; ?>" 
                        title="<?php echo esc_attr($labels['zh-CN']); ?>"
                        aria-label="<?php echo esc_attr($labels['zh-CN']); ?>">
                    <span class="flag-emoji"><?php echo self::get_flag_svg('zh-CN'); ?></span>
                    <span class="dl-lang-label"><?php echo esc_html($labels['zh-CN']); ?></span>
                </button>
            </div>
        </div>
        <?php
    }

    /**
     * Hiển thị chọn ngôn ngữ trong Drawer Menu trên Mobile
     */
    public function render_mobile_switcher() {
        $current_lang = isset($_COOKIE['googtrans']) ? str_replace('/vi/', '', $_COOKIE['googtrans']) : 'vi';
        if (empty($current_lang) || !in_array($current_lang, array('vi', 'en', 'zh-CN'), true)) {
            $current_lang = 'vi';
        }
        $labels = self::get_lang_labels($current_lang);
        ?>
        <div class="dl-lang-switcher-mobile notranslate" translate="no">
            <button type="button" data-lang="vi" class="dl-lang-btn <?php echo ($current_lang === 'vi') ? 'active' : ''; ?>" title="<?php echo esc_attr($labels['vi']); ?>" aria-label="<?php echo esc_attr($labels['vi']); ?>">
                <span class="flag-emoji"><?php echo self::get_flag_svg('vi'); ?></span>
                <span class="dl-lang-label"><?php echo esc_html($labels['vi']); ?></span>
            </button>
            <button type="button" data-lang="en" class="dl-lang-btn <?php echo ($current_lang === 'en') ? 'active' : ''; ?>" title="<?php echo esc_attr($labels['en']); ?>" aria-label="<?php echo esc_attr($labels['en']); ?>">
                <span class="flag-emoji"><?php echo self::get_flag_svg('en'); ?></span>
                <span class="dl-lang-label"><?php echo esc_html($labels['en']); ?></span>
            </button>
            <button type="button" data-lang="zh-CN" class="dl-lang-btn <?php echo ($current_lang === 'zh-CN') ? 'active' : ''; ?>" title="<?php echo esc_attr($labels['zh-CN']); ?>" aria-label="<?php echo esc_attr($labels['zh-CN']); ?>">
                <span class="flag-emoji"><?php echo self::get_flag_svg('zh-CN'); ?></span>
                <span class="dl-lang-label"><?php echo esc_html($labels['zh-CN']); ?></span>
            </button>
        </div>
        <?php
    }

    /**
     * Shortcode [dl_language_switcher]
     */
    public function shortcode_switcher($atts) {
        ob_start();
        $this->render_switcher();
        return ob_get_clean();
    }

    /**
     * Đăng ký trang quản trị trong WP-Admin
     */
    public function register_admin_menu() {
        add_submenu_page(
            'dragonlegend-content-manager',
            __('Cài Đặt Dịch Ngôn Ngữ', 'dragonlegend-translator'),
            __('Dịch Ngôn Ngữ', 'dragonlegend-translator'),
            'manage_options',
            'dragonlegend-translator-settings',
            array($this, 'render_admin_settings_page')
        );
    }

    public function register_settings() {
        register_setting('dl_translator_group', 'dl_translator_options');
    }

    public function render_admin_settings_page() {
        require_once DL_TRANSLATOR_DIR . 'includes/admin-settings.php';
    }
}

// Khởi chạy Plugin
function dragonlegend_auto_translator() {
    return DragonLegend_Auto_Translator::get_instance();
}
add_action('plugins_loaded', 'dragonlegend_auto_translator');

// Global helper functions
function dl_render_language_switcher() {
    DragonLegend_Auto_Translator::get_instance()->render_switcher();
}

function dl_render_language_switcher_mobile() {
    DragonLegend_Auto_Translator::get_instance()->render_mobile_switcher();
}
