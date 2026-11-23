<?php
/**
 * Plugin Name: Dragon Legend - Quản Lý Tuyển Dụng (Tuyển Dụng)
 * Plugin URI: https://dragonlegendhotel.com/
 * Description: Hệ thống đăng tải thông tin tuyển dụng nhân sự, phân loại theo bộ phận khách sạn và tiếp nhận hồ sơ ứng tuyển trực tuyến cho Dragon Legend Hotel.
 * Version: 1.0.0
 * Author: Dragon Legend Hotel Team
 * Author URI: https://dragonlegendhotel.com/
 * Text Domain: tuyen-dung
 */

if (!defined('ABSPATH')) {
    exit;
}

define('DL_RECRUITMENT_VERSION', '1.0.0');
define('DL_RECRUITMENT_PATH', plugin_dir_path(__FILE__));
define('DL_RECRUITMENT_URL', plugin_dir_url(__FILE__));

class DragonLegend_Recruitment {

    public static function init() {
        // 1. Đăng ký Custom Post Type & Taxonomy
        add_action('init', array(__CLASS__, 'register_post_type_and_taxonomy'));

        // 2. Đăng ký Metabox chi tiết tuyển dụng
        add_action('add_meta_boxes', array(__CLASS__, 'add_job_metaboxes'));
        add_action('save_post_dl_job', array(__CLASS__, 'save_job_metaboxes'), 10, 2);

        // 3. Tùy biến cột hiển thị trong danh sách Admin
        add_filter('manage_dl_job_posts_columns', array(__CLASS__, 'custom_admin_columns'));
        add_action('manage_dl_job_posts_custom_column', array(__CLASS__, 'render_admin_columns'), 10, 2);

        // 4. Đăng ký Submenu: Hồ sơ ứng tuyển & Cài đặt liên hệ Nhân sự
        add_action('admin_menu', array(__CLASS__, 'register_admin_submenus'));

        // 5. Load Template hiển thị ngoài Frontend (Archive & Single) & Redirect URL cũ
        add_action('template_redirect', array(__CLASS__, 'redirect_legacy_department_query'), 1);
        add_filter('template_include', array(__CLASS__, 'load_job_templates'));

        // 6. Đăng ký Shortcode [tuyen_dung]
        add_shortcode('tuyen_dung', array(__CLASS__, 'render_jobs_shortcode'));
        add_shortcode('dl_jobs', array(__CLASS__, 'render_jobs_shortcode'));

        // 7. Xử lý AJAX nộp hồ sơ ứng tuyển trực tuyến
        add_action('wp_ajax_dl_submit_job_application', array(__CLASS__, 'handle_job_application'));
        add_action('wp_ajax_nopriv_dl_submit_job_application', array(__CLASS__, 'handle_job_application'));

        // 8. AJAX Test thông báo riêng cho bộ phận Tuyển dụng (Email, Telegram, Zalo)
        add_action('wp_ajax_dl_test_hr_telegram', array(__CLASS__, 'handle_test_hr_telegram'));
        add_action('wp_ajax_dl_test_hr_zalo', array(__CLASS__, 'handle_test_hr_zalo'));
        add_action('wp_ajax_dl_test_hr_email', array(__CLASS__, 'handle_test_hr_email'));
    }

    /**
     * Đăng ký Post Type: dl_job (Slug: tuyen-dung) & Taxonomy: dl_job_department
     */
    public static function register_post_type_and_taxonomy() {
        $labels = array(
            'name'               => 'Tuyển Dụng',
            'singular_name'      => 'Tin tuyển dụng',
            'menu_name'          => 'Tuyển Dụng',
            'name_admin_bar'     => 'Tin tuyển dụng',
            'add_new'            => 'Đăng tin mới',
            'add_new_item'       => 'Đăng tin tuyển dụng mới',
            'new_item'           => 'Tin tuyển dụng mới',
            'edit_item'          => 'Chỉnh sửa tin tuyển dụng',
            'view_item'          => 'Xem tin tuyển dụng',
            'all_items'          => 'Tất cả tin tuyển dụng',
            'search_items'       => 'Tìm kiếm vị trí tuyển dụng',
            'not_found'          => 'Chưa có tin tuyển dụng nào.',
            'not_found_in_trash' => 'Không có tin tuyển dụng nào trong thùng rác.'
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'tuyen-dung', 'with_front' => false),
            'capability_type'    => 'post',
            'has_archive'        => 'tuyen-dung',
            'hierarchical'       => false,
            'menu_position'      => 9,
            'menu_icon'          => 'dashicons-id-alt',
            'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
            'show_in_rest'       => true,
        );

        register_post_type('dl_job', $args);

        // Taxonomy: Bộ phận / Phòng ban
        $tax_labels = array(
            'name'              => 'Bộ phận / Phòng ban',
            'singular_name'     => 'Bộ phận',
            'search_items'      => 'Tìm bộ phận',
            'all_items'         => 'Tất cả bộ phận',
            'edit_item'         => 'Sửa bộ phận',
            'update_item'       => 'Cập nhật bộ phận',
            'add_new_item'      => 'Thêm bộ phận mới',
            'new_item_name'     => 'Tên bộ phận mới',
            'menu_name'         => 'Bộ phận / Phòng ban',
        );

        register_taxonomy('dl_job_department', array('dl_job'), array(
            'hierarchical'      => true,
            'labels'            => $tax_labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'bo-phan-tuyen-dung'),
            'show_in_rest'      => true,
        ));
    }

    /**
     * Lấy thông tin cài đặt liên hệ & thông báo riêng của Phòng Nhân sự (HR)
     */
    public static function get_hr_settings() {
        $defaults = array(
            'hr_name'                    => 'Phòng Hành chính - Nhân sự (HR Department)',
            'hr_phone'                   => '(+84) 203 3518 999 - 0968 926 999',
            'hr_email'                   => 'info@dragonlegendhotel.com',
            'hr_address'                 => 'Đại lộ Hạ Long Marine, Phường Bãi Cháy, TP. Hạ Long, Tỉnh Quảng Ninh',
            'hr_note'                    => 'Ứng viên có thể nộp hồ sơ trực tuyến ngay trên website, gửi CV qua Email hoặc nộp trực tiếp tại Quầy Lễ tân khách sạn.',
            // Cấu hình Email Nhân sự riêng
            'hr_email_enabled'           => '1',
            'hr_candidate_email_enabled' => '1',
            // Cấu hình Telegram Nhân sự riêng
            'hr_telegram_enabled'        => '0',
            'hr_telegram_bot_token'      => '',
            'hr_telegram_chat_id'        => '',
            // Cấu hình Zalo Nhân sự riêng
            'hr_zalo_enabled'            => '0',
            'hr_zalo_mode'               => 'simulation', // 'simulation' | 'webhook' | 'zns'
            'hr_zalo_phones'             => '0968926999',
            'hr_zalo_webhook_url'        => '',
            'hr_zalo_webhook_secret'     => '',
            'hr_zalo_access_token'       => '',
            'hr_zalo_template_id'        => '',
            'hr_zalo_msg_template'       => "🔔 [HỒ SƠ ỨNG TUYỂN MỚI - DRAGON LEGEND] Vị trí: {job_title} | Ứng viên: {candidate_name} ({candidate_phone}) | Email: {candidate_email} | Lời nhắn: {message} | Link CV: {cv_url}",
        );
        $saved = get_option('dl_recruitment_hr_settings', array());
        if (!is_array($saved)) {
            $saved = array();
        }
        return wp_parse_args($saved, $defaults);
    }

    /**
     * Đăng ký Metabox thông tin chi tiết vị trí tuyển dụng
     */
    public static function add_job_metaboxes() {
        add_meta_box(
            'dl_job_details_box',
            '📋 Thông Tin Chi Tiết Vị Trí Tuyển Dụng',
            array(__CLASS__, 'render_job_metabox'),
            'dl_job',
            'normal',
            'high'
        );
    }

    public static function render_job_metabox($post) {
        wp_nonce_field('dl_save_job_meta', 'dl_job_meta_nonce');

        $status       = get_post_meta($post->ID, '_dl_job_status', true) ?: 'open';
        $salary       = get_post_meta($post->ID, '_dl_job_salary', true) ?: 'Thỏa thuận theo năng lực';
        $quantity     = get_post_meta($post->ID, '_dl_job_quantity', true) ?: '02 người';
        $job_type     = get_post_meta($post->ID, '_dl_job_type', true) ?: 'Toàn thời gian (Full-time)';
        $experience   = get_post_meta($post->ID, '_dl_job_experience', true) ?: 'Ưu tiên có kinh nghiệm khách sạn';
        $deadline     = get_post_meta($post->ID, '_dl_job_deadline', true) ?: '';
        $location     = get_post_meta($post->ID, '_dl_job_location', true) ?: 'Dragon Legend Hotel - Bãi Cháy, Hạ Long';
        $requirements = get_post_meta($post->ID, '_dl_job_requirements', true);
        $benefits     = get_post_meta($post->ID, '_dl_job_benefits', true);
        ?>
        <style>
            .dl-job-meta-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; margin-top: 10px; }
            .dl-job-meta-field { display: flex; flex-direction: column; }
            .dl-job-meta-field.full-width { grid-column: span 2; }
            .dl-job-meta-field label { font-weight: 600; color: #1e293b; margin-bottom: 6px; font-size: 13px; }
            .dl-job-meta-field input, .dl-job-meta-field select, .dl-job-meta-field textarea {
                padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13px; width: 100%; box-sizing: border-box;
            }
            .dl-job-meta-field small { color: #64748b; margin-top: 4px; font-size: 11px; }
            @media (max-width: 782px) { .dl-job-meta-grid { grid-template-columns: 1fr; } .dl-job-meta-field.full-width { grid-column: span 1; } }
        </style>

        <div class="dl-job-meta-grid">
            <div class="dl-job-meta-field">
                <label for="dl_job_status">Trạng thái tuyển dụng</label>
                <select id="dl_job_status" name="dl_job_status">
                    <option value="open" <?php selected($status, 'open'); ?>>🟢 Đang tuyển dụng</option>
                    <option value="urgent" <?php selected($status, 'urgent'); ?>>🔥 Tuyển gấp (Ưu tiên hiển thị)</option>
                    <option value="closed" <?php selected($status, 'closed'); ?>>⚪ Đã tuyển đủ / Tạm đóng</option>
                </select>
            </div>

            <div class="dl-job-meta-field">
                <label for="dl_job_salary">Mức lương & Thu nhập</label>
                <input type="text" id="dl_job_salary" name="dl_job_salary" value="<?php echo esc_attr($salary); ?>" placeholder="VD: 8.000.000 - 12.000.000 VNĐ + Service Charge">
            </div>

            <div class="dl-job-meta-field">
                <label for="dl_job_quantity">Số lượng cần tuyển</label>
                <input type="text" id="dl_job_quantity" name="dl_job_quantity" value="<?php echo esc_attr($quantity); ?>" placeholder="VD: 03 nhân sự">
            </div>

            <div class="dl-job-meta-field">
                <label for="dl_job_type">Hình thức làm việc</label>
                <select id="dl_job_type" name="dl_job_type">
                    <option value="Toàn thời gian (Full-time)" <?php selected($job_type, 'Toàn thời gian (Full-time)'); ?>>Toàn thời gian (Full-time)</option>
                    <option value="Theo ca (Xoay ca linh hoạt)" <?php selected($job_type, 'Theo ca (Xoay ca linh hoạt)'); ?>>Theo ca (Xoay ca linh hoạt)</option>
                    <option value="Bán thời gian (Part-time)" <?php selected($job_type, 'Bán thời gian (Part-time)'); ?>>Bán thời gian (Part-time)</option>
                    <option value="Thực tập sinh (Internship)" <?php selected($job_type, 'Thực tập sinh (Internship)'); ?>>Thực tập sinh (Internship)</option>
                </select>
            </div>

            <div class="dl-job-meta-field">
                <label for="dl_job_experience">Yêu cầu kinh nghiệm / Cấp bậc</label>
                <input type="text" id="dl_job_experience" name="dl_job_experience" value="<?php echo esc_attr($experience); ?>" placeholder="VD: Từ 1 năm kinh nghiệm ở vị trí tương đương">
            </div>

            <div class="dl-job-meta-field">
                <label for="dl_job_deadline">Hạn nộp hồ sơ</label>
                <input type="text" id="dl_job_deadline" name="dl_job_deadline" value="<?php echo esc_attr($deadline); ?>" placeholder="VD: 30/11/2026 hoặc Tuyển liên tục">
                <small>Để trống sẽ hiển thị "Tuyển liên tục đến khi đủ nhân sự".</small>
            </div>

            <div class="dl-job-meta-field full-width">
                <label for="dl_job_location">Địa điểm làm việc</label>
                <input type="text" id="dl_job_location" name="dl_job_location" value="<?php echo esc_attr($location); ?>">
            </div>

            <div class="dl-job-meta-field full-width">
                <label for="dl_job_requirements">Yêu cầu ứng viên (Mỗi dòng 1 ý)</label>
                <textarea id="dl_job_requirements" name="dl_job_requirements" rows="5" placeholder="Nam/Nữ độ tuổi từ 20 - 35&#10;Ngoại hình ưa nhìn, giao tiếp tốt&#10;Ưu tiên ứng viên biết tiếng Anh giao tiếp cơ bản"><?php echo esc_textarea($requirements); ?></textarea>
                <small>Các yêu cầu này sẽ được hiển thị thành danh sách rõ ràng trong trang chi tiết tuyển dụng.</small>
            </div>

            <div class="dl-job-meta-field full-width">
                <label for="dl_job_benefits">Quyền lợi & Chế độ đãi ngộ (Mỗi dòng 1 quyền lợi)</label>
                <textarea id="dl_job_benefits" name="dl_job_benefits" rows="5" placeholder="Mức lương cạnh tranh + Thưởng phí dịch vụ (Service Charge) hàng tháng&#10;Hỗ trợ bữa ăn giữa ca tại nhà ăn khách sạn&#10;Đóng BHXH, BHYT, BHTN đầy đủ theo quy định Luật Lao động&#10;Thưởng các ngày Lễ, Tết, lương tháng 13"><?php echo esc_textarea($benefits); ?></textarea>
                <small>Mỗi dòng sẽ hiển thị kèm biểu tượng dấu tích xanh nổi bật.</small>
            </div>
        </div>
        <?php
    }

    public static function save_job_metaboxes($post_id, $post) {
        if (!isset($_POST['dl_job_meta_nonce']) || !wp_verify_nonce($_POST['dl_job_meta_nonce'], 'dl_save_job_meta')) {
            return;
        }
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        $fields = array(
            'dl_job_status'       => '_dl_job_status',
            'dl_job_salary'       => '_dl_job_salary',
            'dl_job_quantity'     => '_dl_job_quantity',
            'dl_job_type'         => '_dl_job_type',
            'dl_job_experience'   => '_dl_job_experience',
            'dl_job_deadline'     => '_dl_job_deadline',
            'dl_job_location'     => '_dl_job_location',
        );

        foreach ($fields as $input => $meta_key) {
            if (isset($_POST[$input])) {
                update_post_meta($post_id, $meta_key, sanitize_text_field($_POST[$input]));
            }
        }

        if (isset($_POST['dl_job_requirements'])) {
            update_post_meta($post_id, '_dl_job_requirements', sanitize_textarea_field($_POST['dl_job_requirements']));
        }
        if (isset($_POST['dl_job_benefits'])) {
            update_post_meta($post_id, '_dl_job_benefits', sanitize_textarea_field($_POST['dl_job_benefits']));
        }
    }

    /**
     * Cột hiển thị trong Admin
     */
    public static function custom_admin_columns($columns) {
        $new_cols = array(
            'cb'                     => $columns['cb'],
            'title'                  => 'Vị trí tuyển dụng',
            'taxonomy-dl_job_department' => 'Bộ phận',
            'job_status'             => 'Trạng thái',
            'job_salary'             => 'Mức lương',
            'job_quantity'           => 'Số lượng',
            'job_deadline'           => 'Hạn nộp',
            'date'                   => 'Ngày đăng'
        );
        return $new_cols;
    }

    public static function render_admin_columns($column, $post_id) {
        switch ($column) {
            case 'job_status':
                $st = get_post_meta($post_id, '_dl_job_status', true) ?: 'open';
                if ($st === 'urgent') {
                    echo '<span style="background:#fee2e2;color:#b91c1c;padding:3px 8px;border-radius:99px;font-weight:700;font-size:11px;">🔥 Tuyển gấp</span>';
                } elseif ($st === 'closed') {
                    echo '<span style="background:#f1f5f9;color:#64748b;padding:3px 8px;border-radius:99px;font-weight:600;font-size:11px;">⚪ Đã đóng</span>';
                } else {
                    echo '<span style="background:#dcfce7;color:#15803d;padding:3px 8px;border-radius:99px;font-weight:700;font-size:11px;">🟢 Đang tuyển</span>';
                }
                break;
            case 'job_salary':
                echo esc_html(get_post_meta($post_id, '_dl_job_salary', true) ?: 'Thỏa thuận');
                break;
            case 'job_quantity':
                echo esc_html(get_post_meta($post_id, '_dl_job_quantity', true) ?: '01');
                break;
            case 'job_deadline':
                echo esc_html(get_post_meta($post_id, '_dl_job_deadline', true) ?: 'Tuyển liên tục');
                break;
        }
    }

    /**
     * Submenu: Hồ sơ ứng tuyển & Cài đặt Nhân sự
     */
    public static function register_admin_submenus() {
        $apps = get_option('dl_job_applications', array());
        $count = is_array($apps) ? count($apps) : 0;
        $badge = $count > 0 ? ' <span class="awaiting-mod">' . $count . '</span>' : '';

        add_submenu_page(
            'edit.php?post_type=dl_job',
            'Hồ sơ ứng tuyển',
            'Hồ sơ ứng tuyển' . $badge,
            'manage_options',
            'dl-job-applications',
            array(__CLASS__, 'render_applications_page')
        );

        add_submenu_page(
            'edit.php?post_type=dl_job',
            'Cài đặt Liên hệ Nhân sự',
            'Cài đặt Nhân sự',
            'manage_options',
            'dl-hr-settings',
            array(__CLASS__, 'render_hr_settings_page')
        );
    }

    public static function render_applications_page() {
        if (isset($_GET['delete_app']) && check_admin_referer('dl_del_app')) {
            $idx = intval($_GET['delete_app']);
            $apps = get_option('dl_job_applications', array());
            if (isset($apps[$idx])) {
                unset($apps[$idx]);
                update_option('dl_job_applications', array_values($apps), false);
                echo '<div class="notice notice-success is-dismissible"><p>Đã xóa hồ sơ ứng tuyển.</p></div>';
            }
        }

        $apps = get_option('dl_job_applications', array());
        if (!is_array($apps)) $apps = array();
        $apps_reversed = array_reverse($apps, true);
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">📩 Danh Sách Hồ Sơ Ứng Tuyển Trực Tuyến</h1>
            <p>Các hồ sơ do ứng viên gửi trực tiếp từ trang Tuyển dụng trên website.</p>
            <table class="wp-list-table widefat fixed striped" style="margin-top:15px;">
                <thead>
                    <tr>
                        <th style="width:140px;">Ngày nộp</th>
                        <th style="width:180px;">Họ và tên</th>
                        <th style="width:180px;">Liên hệ</th>
                        <th style="width:200px;">Vị trí ứng tuyển</th>
                        <th>Kinh nghiệm / Giới thiệu & CV</th>
                        <th style="width:90px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($apps_reversed)) : ?>
                        <tr><td colspan="6">Chưa có hồ sơ ứng tuyển nào.</td></tr>
                    <?php else : ?>
                        <?php foreach ($apps_reversed as $idx => $app) : ?>
                            <tr>
                                <td><?php echo esc_html($app['date'] ?? ''); ?></td>
                                <td><strong><?php echo esc_html($app['name'] ?? ''); ?></strong></td>
                                <td>
                                    📞 <a href="tel:<?php echo esc_attr($app['phone'] ?? ''); ?>"><?php echo esc_html($app['phone'] ?? ''); ?></a><br>
                                    <?php if (!empty($app['email'])) : ?>
                                        ✉️ <a href="mailto:<?php echo esc_attr($app['email']); ?>"><?php echo esc_html($app['email']); ?></a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span style="font-weight:600;color:#48100f;"><?php echo esc_html($app['job_title'] ?? ''); ?></span>
                                </td>
                                <td>
                                    <?php echo nl2br(esc_html($app['message'] ?? '')); ?>
                                    <?php if (!empty($app['cv_url'])) : ?>
                                        <div style="margin-top:6px;">
                                            <a href="<?php echo esc_url($app['cv_url']); ?>" target="_blank" class="button button-small">📎 Xem File CV đính kèm</a>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo esc_url(wp_nonce_url(admin_url('edit.php?post_type=dl_job&page=dl-job-applications&delete_app=' . $idx), 'dl_del_app')); ?>" class="button button-small" onclick="return confirm('Bạn có chắc muốn xóa hồ sơ này?');" style="color:#b91c1c;border-color:#fca5a5;">Xóa</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    public static function render_hr_settings_page() {
        $is_saved = false;
        if (isset($_POST['dl_save_hr_settings']) && check_admin_referer('dl_hr_settings_nonce')) {
            $new_settings = array(
                'hr_name'                    => sanitize_text_field($_POST['hr_name'] ?? ''),
                'hr_phone'                   => sanitize_text_field($_POST['hr_phone'] ?? ''),
                'hr_email'                   => sanitize_text_field($_POST['hr_email'] ?? ''),
                'hr_address'                 => sanitize_text_field($_POST['hr_address'] ?? ''),
                'hr_note'                    => sanitize_textarea_field($_POST['hr_note'] ?? ''),
                'hr_email_enabled'           => isset($_POST['hr_email_enabled']) ? '1' : '0',
                'hr_candidate_email_enabled' => isset($_POST['hr_candidate_email_enabled']) ? '1' : '0',
                'hr_telegram_enabled'        => isset($_POST['hr_telegram_enabled']) ? '1' : '0',
                'hr_telegram_bot_token'      => sanitize_text_field($_POST['hr_telegram_bot_token'] ?? ''),
                'hr_telegram_chat_id'        => sanitize_text_field($_POST['hr_telegram_chat_id'] ?? ''),
                'hr_zalo_enabled'            => isset($_POST['hr_zalo_enabled']) ? '1' : '0',
                'hr_zalo_mode'               => sanitize_text_field($_POST['hr_zalo_mode'] ?? 'simulation'),
                'hr_zalo_phones'             => sanitize_text_field($_POST['hr_zalo_phones'] ?? ''),
                'hr_zalo_webhook_url'        => esc_url_raw($_POST['hr_zalo_webhook_url'] ?? ''),
                'hr_zalo_webhook_secret'     => sanitize_text_field($_POST['hr_zalo_webhook_secret'] ?? ''),
                'hr_zalo_access_token'       => sanitize_textarea_field($_POST['hr_zalo_access_token'] ?? ''),
                'hr_zalo_template_id'        => sanitize_text_field($_POST['hr_zalo_template_id'] ?? ''),
                'hr_zalo_msg_template'       => wp_kses_post($_POST['hr_zalo_msg_template'] ?? ''),
            );
            update_option('dl_recruitment_hr_settings', $new_settings);
            $is_saved = true;
        }

        $hr = self::get_hr_settings();
        $test_nonce = wp_create_nonce('dl_hr_test_nonce');
        ?>
        <div class="wrap" style="max-width: 1050px; margin-top: 20px;">
            <div style="background: linear-gradient(135deg, #48100f 0%, #2a0706 100%); color: #fff; padding: 24px 30px; border-radius: 16px; margin-bottom: 24px; border: 1px solid rgba(254, 240, 138, 0.3); box-shadow: 0 10px 25px rgba(0,0,0,0.15);">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
                    <div>
                        <span style="font-size: 11px; text-transform: uppercase; letter-spacing: 2px; color: #fef08a; font-weight: 700; display: block; margin-bottom: 4px;">Hệ Thống Tuyển Dụng Độc Lập — Phòng Hành Chính Nhân Sự</span>
                        <h1 style="color: #fff; font-size: 23px; font-weight: 700; margin: 0; padding: 0;">⚙️ Cấu Hình Thông Báo Tuyển Dụng (Email, Telegram & Zalo Riêng)</h1>
                        <p style="color: #fca5a5; font-size: 13px; margin: 6px 0 0 0;">Cấu hình kênh nhận hồ sơ ứng viên hoàn toàn độc lập với bộ phận Lễ tân / Đặt phòng.</p>
                    </div>
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <span style="padding: 5px 12px; background: rgba(255,255,255,0.1); border-radius: 20px; font-size: 12px;">
                            Email HR: <strong style="color: <?php echo !empty($hr['hr_email_enabled']) ? '#4ade80' : '#f87171'; ?>;"><?php echo !empty($hr['hr_email_enabled']) ? '✓ Đang BẬT' : 'Đang tắt'; ?></strong>
                        </span>
                        <span style="padding: 5px 12px; background: rgba(255,255,255,0.1); border-radius: 20px; font-size: 12px;">
                            Telegram HR: <strong style="color: <?php echo !empty($hr['hr_telegram_enabled']) ? '#4ade80' : '#f87171'; ?>;"><?php echo !empty($hr['hr_telegram_enabled']) ? '✓ Đang BẬT' : 'Đang tắt'; ?></strong>
                        </span>
                        <span style="padding: 5px 12px; background: rgba(255,255,255,0.1); border-radius: 20px; font-size: 12px;">
                            Zalo HR: <strong style="color: <?php echo !empty($hr['hr_zalo_enabled']) ? '#4ade80' : '#f87171'; ?>;"><?php echo !empty($hr['hr_zalo_enabled']) ? '✓ Đang BẬT' : 'Đang tắt'; ?></strong>
                        </span>
                    </div>
                </div>
            </div>

            <?php if ($is_saved) : ?>
                <div class="notice notice-success is-dismissible" style="border-radius: 8px; border-left-color: #22c55e;">
                    <p><strong>Đã lưu thành công cấu hình Liên hệ & Thông báo riêng cho bộ phận Tuyển dụng (Phòng Nhân sự)!</strong></p>
                </div>
            <?php endif; ?>

            <form method="post">
                <?php wp_nonce_field('dl_hr_settings_nonce'); ?>

                <!-- 1. THÔNG TIN LIÊN HỆ HIỂN THỊ & EMAIL NHẬN HỒ SƠ -->
                <div class="postbox" style="border-radius: 12px; overflow: hidden; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                    <div class="postbox-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 14px 20px;">
                        <h2 style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0;">1. Thông Tin Phòng Nhân Sự & Cấu Hình Email Nhận Hồ Sơ CV</h2>
                    </div>
                    <div class="inside" style="padding: 20px;">
                        <table class="form-table" style="margin: 0;">
                            <tr>
                                <th style="width: 240px;"><label for="hr_name"><strong>Tên bộ phận tiếp nhận:</strong></label></th>
                                <td><input type="text" id="hr_name" name="hr_name" value="<?php echo esc_attr($hr['hr_name']); ?>" class="regular-text" style="width:100%; max-width:500px;"></td>
                            </tr>
                            <tr>
                                <th><label for="hr_phone"><strong>Điện thoại liên hệ Nhân sự:</strong></label></th>
                                <td><input type="text" id="hr_phone" name="hr_phone" value="<?php echo esc_attr($hr['hr_phone']); ?>" class="regular-text" style="width:100%; max-width:500px;"></td>
                            </tr>
                            <tr>
                                <th><label for="hr_email"><strong>Email nhận CV ứng tuyển:</strong></label></th>
                                <td>
                                    <input type="text" id="hr_email" name="hr_email" value="<?php echo esc_attr($hr['hr_email']); ?>" class="regular-text" style="width:100%; max-width:500px;" placeholder="hr@dragonlegendhotel.com, info@dragonlegendhotel.com">
                                    <p class="description">Hộp thư của Phòng Nhân sự nhận thông báo kèm file CV khi ứng viên nộp đơn. Có thể nhập <strong>nhiều email cách nhau bằng dấu phẩy (,)</strong>.</p>
                                </td>
                            </tr>
                            <tr>
                                <th><label><strong>Tùy chọn gửi Email:</strong></label></th>
                                <td>
                                    <div style="display:flex; flex-direction:column; gap:8px;">
                                        <label style="display:inline-flex; align-items:center; gap:8px; cursor:pointer;">
                                            <input type="checkbox" name="hr_email_enabled" value="1" <?php checked($hr['hr_email_enabled'] ?? '1', '1'); ?>>
                                            <span><strong>Gửi Email HTML thông báo hồ sơ mới</strong> kèm link tải CV cho Phòng Nhân sự</span>
                                        </label>
                                        <label style="display:inline-flex; align-items:center; gap:8px; cursor:pointer;">
                                            <input type="checkbox" name="hr_candidate_email_enabled" value="1" <?php checked($hr['hr_candidate_email_enabled'] ?? '1', '1'); ?>>
                                            <span><strong>Gửi Email xác nhận tự động cho Ứng viên</strong> (nếu ứng viên có điền địa chỉ email)</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="hr_address"><strong>Địa chỉ nộp hồ sơ trực tiếp:</strong></label></th>
                                <td><input type="text" id="hr_address" name="hr_address" value="<?php echo esc_attr($hr['hr_address']); ?>" class="regular-text" style="width:100%; max-width:500px;"></td>
                            </tr>
                            <tr>
                                <th><label for="hr_note"><strong>Ghi chú hướng dẫn ứng viên:</strong></label></th>
                                <td><textarea id="hr_note" name="hr_note" rows="3" style="width:100%; max-width:500px;"><?php echo esc_textarea($hr['hr_note']); ?></textarea></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- 2. CẤU HÌNH TELEGRAM TUYỂN DỤNG RIÊNG -->
                <div class="postbox" style="border-radius: 12px; overflow: hidden; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border: 1px solid #bae6fd;">
                    <div class="postbox-header" style="background: linear-gradient(to right, #f0f9ff, #e0f2fe); border-bottom: 1px solid #bae6fd; padding: 14px 20px;">
                        <h2 style="font-size: 15px; font-weight: 700; color: #0369a1; margin: 0;">✈️ 2. Cấu Hình Telegram Riêng Cho Tuyển Dụng (Group / Chat Phòng Nhân Sự)</h2>
                    </div>
                    <div class="inside" style="padding: 20px;">
                        <table class="form-table" style="margin: 0;">
                            <tr>
                                <th style="width: 240px;"><label for="hr_telegram_enabled"><strong>Bật Telegram Tuyển dụng:</strong></label></th>
                                <td>
                                    <label style="display:inline-flex; align-items:center; gap:8px; cursor:pointer; font-weight:600;">
                                        <input type="checkbox" id="hr_telegram_enabled" name="hr_telegram_enabled" value="1" <?php checked($hr['hr_telegram_enabled'] ?? '0', '1'); ?>>
                                        <span>Nhận thông báo tức thì qua Telegram khi có ứng viên nộp hồ sơ ứng tuyển</span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="hr_telegram_bot_token"><strong>Telegram Bot Token (HR):</strong></label></th>
                                <td>
                                    <input type="text" id="hr_telegram_bot_token" name="hr_telegram_bot_token" value="<?php echo esc_attr($hr['hr_telegram_bot_token'] ?? ''); ?>" class="regular-text" style="width:100%; max-width:500px; font-family:monospace;" placeholder="Ví dụ: 7123456789:AAHxyz...">
                                    <p class="description">Có thể dùng Bot Token riêng của phòng Nhân sự hoặc dùng chung Bot của khách sạn nhưng gửi vào Group Chat ID riêng của HR.</p>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="hr_telegram_chat_id"><strong>Telegram Chat ID / Group ID (HR):</strong></label></th>
                                <td>
                                    <input type="text" id="hr_telegram_chat_id" name="hr_telegram_chat_id" value="<?php echo esc_attr($hr['hr_telegram_chat_id'] ?? ''); ?>" class="regular-text" style="width:100%; max-width:500px; font-family:monospace;" placeholder="Ví dụ: -100987654321 hoặc 123456789">
                                    <p class="description">ID nhóm Telegram của Phòng Nhân sự hoặc ID cá nhân cán bộ tuyển dụng (hỗ trợ nhập nhiều ID cách nhau bằng dấu phẩy).</p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- 3. CẤU HÌNH ZALO TUYỂN DỤNG RIÊNG -->
                <div class="postbox" style="border-radius: 12px; overflow: hidden; margin-bottom: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                    <div class="postbox-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 14px 20px;">
                        <h2 style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0;">📲 3. Cấu Hình Zalo Riêng Cho Tuyển Dụng (Nhận Thông Báo Ứng Viên Mới)</h2>
                    </div>
                    <div class="inside" style="padding: 20px;">
                        <table class="form-table" style="margin: 0;">
                            <tr>
                                <th style="width: 240px;"><label for="hr_zalo_enabled"><strong>Bật Zalo Tuyển dụng:</strong></label></th>
                                <td>
                                    <label style="display:inline-flex; align-items:center; gap:8px; cursor:pointer; font-weight:600;">
                                        <input type="checkbox" id="hr_zalo_enabled" name="hr_zalo_enabled" value="1" <?php checked($hr['hr_zalo_enabled'] ?? '0', '1'); ?>>
                                        <span>Gửi thông báo Zalo tới cán bộ Nhân sự khi có ứng viên nộp hồ sơ</span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="hr_zalo_phones"><strong>Số điện thoại Zalo Nhân sự (HR):</strong></label></th>
                                <td>
                                    <input type="text" id="hr_zalo_phones" name="hr_zalo_phones" value="<?php echo esc_attr($hr['hr_zalo_phones'] ?? ''); ?>" class="regular-text" style="width:100%; max-width:500px;" placeholder="0968926999, 0912345678">
                                    <p class="description">SĐT Zalo của Trưởng phòng Nhân sự / Cán bộ tuyển dụng (hỗ trợ nhiều số cách nhau bằng dấu phẩy).</p>
                                </td>
                            </tr>
                            <tr>
                                <th><label for="hr_zalo_mode"><strong>Chế độ kết nối Zalo HR:</strong></label></th>
                                <td>
                                    <select id="hr_zalo_mode" name="hr_zalo_mode" style="min-width: 320px;" onchange="toggleHrZaloMode(this.value)">
                                        <option value="simulation" <?php selected($hr['hr_zalo_mode'] ?? 'simulation', 'simulation'); ?>>🧪 Chế độ Mô phỏng & Ghi Log</option>
                                        <option value="webhook" <?php selected($hr['hr_zalo_mode'] ?? '', 'webhook'); ?>>🔗 Zalo Webhook / Gateway (n8n / Make / Zalo Bot riêng của HR)</option>
                                        <option value="zns" <?php selected($hr['hr_zalo_mode'] ?? '', 'zns'); ?>>⚡ Zalo Cloud ZNS API</option>
                                    </select>
                                </td>
                            </tr>
                        </table>

                        <div id="hr_zalo_webhook_box" style="margin-top:15px; padding:16px; background:#faf5ff; border:1px solid #e9d5ff; border-radius:10px; display:<?php echo (($hr['hr_zalo_mode'] ?? '') === 'webhook') ? 'block' : 'none'; ?>;">
                            <div style="display:grid; grid-template-columns:2fr 1fr; gap:15px;">
                                <div>
                                    <label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Webhook URL Tuyển dụng:</label>
                                    <input type="url" name="hr_zalo_webhook_url" value="<?php echo esc_attr($hr['hr_zalo_webhook_url'] ?? ''); ?>" class="widefat" placeholder="https://webhook.yourserver.com/zalo-hr">
                                </div>
                                <div>
                                    <label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Secret Token (Nếu có):</label>
                                    <input type="text" name="hr_zalo_webhook_secret" value="<?php echo esc_attr($hr['hr_zalo_webhook_secret'] ?? ''); ?>" class="widefat">
                                </div>
                            </div>
                        </div>

                        <div id="hr_zalo_zns_box" style="margin-top:15px; padding:16px; background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; display:<?php echo (($hr['hr_zalo_mode'] ?? '') === 'zns') ? 'block' : 'none'; ?>;">
                            <div style="display:grid; grid-template-columns:1fr 2fr; gap:15px;">
                                <div>
                                    <label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">ZNS Template ID (HR):</label>
                                    <input type="text" name="hr_zalo_template_id" value="<?php echo esc_attr($hr['hr_zalo_template_id'] ?? ''); ?>" class="widefat" placeholder="VD: 324899">
                                </div>
                                <div>
                                    <label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Zalo OA Access Token (Để trống sẽ dùng Token chung của khách sạn):</label>
                                    <input type="text" name="hr_zalo_access_token" value="<?php echo esc_attr($hr['hr_zalo_access_token'] ?? ''); ?>" class="widefat">
                                </div>
                            </div>
                        </div>

                        <div style="margin-top:16px;">
                            <label for="hr_zalo_msg_template" style="display:block; font-weight:700; margin-bottom:6px;">Mẫu tin nhắn Zalo thông báo Hồ sơ ứng tuyển mới:</label>
                            <textarea id="hr_zalo_msg_template" name="hr_zalo_msg_template" rows="3" class="widefat"><?php echo esc_textarea($hr['hr_zalo_msg_template']); ?></textarea>
                            <p class="description">Biến hỗ trợ: <code>{job_title}</code>, <code>{candidate_name}</code>, <code>{candidate_phone}</code>, <code>{candidate_email}</code>, <code>{message}</code>, <code>{cv_url}</code>.</p>
                        </div>
                    </div>
                </div>

                <p class="submit" style="margin-bottom: 28px;">
                    <button type="submit" name="dl_save_hr_settings" class="button button-primary button-large" style="background:#48100f; border-color:#48100f; padding:8px 28px; font-weight:700; border-radius:8px;">
                        💾 Lưu Toàn Bộ Cấu Hình Tuyển Dụng (Phòng Nhân Sự)
                    </button>
                </p>
            </form>

            <!-- 4. CÔNG CỤ TEST THÔNG BÁO RIÊNG CHO PHÒNG NHÂN SỰ -->
            <div class="postbox" style="border-radius: 12px; overflow: hidden; margin-bottom: 40px; border: 1px solid #cbd5e1;">
                <div class="postbox-header" style="background: #f1f5f9; border-bottom: 1px solid #cbd5e1; padding: 14px 20px;">
                    <h2 style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0;">🛠️ Công Cụ Kiểm Tra Gửi Thông Báo Tuyển Dụng (Test Telegram, Zalo & Email HR)</h2>
                </div>
                <div class="inside" style="padding: 20px;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
                        <div style="background:#f0f9ff; border:1px solid #bae6fd; border-radius:10px; padding:16px;">
                            <h3 style="margin:0 0 8px 0; font-size:14px; color:#0369a1;">✈️ Test Telegram Nhân Sự</h3>
                            <p style="font-size:12px; color:#475569; margin-top:0;">Gửi thử thông báo hồ sơ ứng viên mẫu vào Telegram của Phòng Nhân sự.</p>
                            <button type="button" onclick="testHrChannel('telegram')" id="btn_test_hr_telegram" class="button button-secondary" style="border-color:#0284c7; color:#0284c7; font-weight:600;">✈️ Gửi Thử Telegram HR</button>
                            <div id="res_test_hr_telegram" style="margin-top:10px; font-size:12px; display:none;"></div>
                        </div>

                        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:16px;">
                            <h3 style="margin:0 0 8px 0; font-size:14px; color:#0f172a;">📲 Test Zalo Nhân Sự</h3>
                            <p style="font-size:12px; color:#475569; margin-top:0;">Kiểm tra gửi tin nhắn Zalo thông báo ứng viên mới tới SĐT Nhân sự.</p>
                            <button type="button" onclick="testHrChannel('zalo')" id="btn_test_hr_zalo" class="button button-secondary" style="font-weight:600;">📲 Gửi Thử Zalo HR</button>
                            <div id="res_test_hr_zalo" style="margin-top:10px; font-size:12px; display:none;"></div>
                        </div>

                        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:16px;">
                            <h3 style="margin:0 0 8px 0; font-size:14px; color:#0f172a;">✉️ Test Email Nhân Sự</h3>
                            <p style="font-size:12px; color:#475569; margin-top:0;">Gửi thử email thông báo hồ sơ ứng tuyển tới hòm thư Phòng Nhân sự.</p>
                            <button type="button" onclick="testHrChannel('email')" id="btn_test_hr_email" class="button button-secondary" style="font-weight:600;">✉️ Gửi Thử Email HR</button>
                            <div id="res_test_hr_email" style="margin-top:10px; font-size:12px; display:none;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
        function toggleHrZaloMode(mode) {
            const wh = document.getElementById('hr_zalo_webhook_box');
            const zns = document.getElementById('hr_zalo_zns_box');
            if (wh) wh.style.display = (mode === 'webhook') ? 'block' : 'none';
            if (zns) zns.style.display = (mode === 'zns') ? 'block' : 'none';
        }

        function testHrChannel(channel) {
            const btn = document.getElementById('btn_test_hr_' + channel);
            const res = document.getElementById('res_test_hr_' + channel);
            const origText = btn.innerText;

            btn.disabled = true;
            btn.innerText = 'Đang gửi thử...';
            res.style.display = 'block';
            res.innerHTML = '<span style="color:#64748b;">⏳ Đang kiểm tra kết nối...</span>';

            const data = new FormData();
            data.append('action', 'dl_test_hr_' + channel);
            data.append('nonce', '<?php echo esc_js($test_nonce); ?>');

            if (channel === 'telegram') {
                data.append('bot_token', document.getElementById('hr_telegram_bot_token').value);
                data.append('chat_id', document.getElementById('hr_telegram_chat_id').value);
            } else if (channel === 'zalo') {
                data.append('phones', document.getElementById('hr_zalo_phones').value);
                data.append('mode', document.getElementById('hr_zalo_mode').value);
            } else if (channel === 'email') {
                data.append('emails', document.getElementById('hr_email').value);
            }

            fetch(ajaxurl, { method: 'POST', body: data })
                .then(r => r.json())
                .then(resp => {
                    if (resp.success) {
                        res.innerHTML = '<div style="padding:8px 10px;background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;border-radius:6px;"><strong>✓ Thành công:</strong> ' + resp.data.message + '</div>';
                    } else {
                        res.innerHTML = '<div style="padding:8px 10px;background:#fef2f2;border:1px solid #fecaca;color:#991b1b;border-radius:6px;"><strong>✕ Lỗi:</strong> ' + resp.data.message + '</div>';
                    }
                })
                .catch(err => {
                    res.innerHTML = '<div style="padding:8px 10px;background:#fef2f2;border:1px solid #fecaca;color:#991b1b;border-radius:6px;"><strong>✕ Lỗi:</strong> ' + err.message + '</div>';
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerText = origText;
                });
        }
        </script>
        <?php
    }

    /**
     * Chuyển hướng 301 vĩnh viễn các URL dạng ?bo-phan={slug} về đường dẫn chuẩn /bo-phan-tuyen-dung/{slug}/
     */
    public static function redirect_legacy_department_query() {
        if (is_admin() || !isset($_GET['bo-phan'])) {
            return;
        }

        $dept_slug = sanitize_title(wp_unslash($_GET['bo-phan']));
        if (!empty($dept_slug)) {
            $term = get_term_by('slug', $dept_slug, 'dl_job_department');
            if ($term && !is_wp_error($term)) {
                $term_link = get_term_link($term, 'dl_job_department');
                if (!is_wp_error($term_link)) {
                    wp_safe_redirect($term_link, 301);
                    exit;
                }
            }
        }

        $archive_link = get_post_type_archive_link('dl_job');
        if ($archive_link) {
            wp_safe_redirect($archive_link, 301);
            exit;
        }
    }

    /**
     * Load Template hiển thị ngoài Frontend
     */
    public static function load_job_templates($template) {
        if (is_post_type_archive('dl_job') || is_tax('dl_job_department')) {
            $plugin_tpl = DL_RECRUITMENT_PATH . 'templates/archive-dl_job.php';
            if (file_exists($plugin_tpl)) {
                return $plugin_tpl;
            }
        }
        if (is_singular('dl_job')) {
            $plugin_tpl = DL_RECRUITMENT_PATH . 'templates/single-dl_job.php';
            if (file_exists($plugin_tpl)) {
                return $plugin_tpl;
            }
        }
        return $template;
    }

    /**
     * Shortcode [tuyen_dung]
     */
    public static function render_jobs_shortcode($atts) {
        ob_start();
        $jobs = new WP_Query(array(
            'post_type'      => 'dl_job',
            'posts_per_page' => 12,
            'post_status'    => 'publish'
        ));
        if ($jobs->have_posts()) {
            echo '<div class="grid grid-cols-1 md:grid-cols-2 gap-6 my-8">';
            while ($jobs->have_posts()) {
                $jobs->the_post();
                $id       = get_the_ID();
                $salary   = get_post_meta($id, '_dl_job_salary', true) ?: 'Thỏa thuận';
                $qty      = get_post_meta($id, '_dl_job_quantity', true) ?: '01 người';
                $deadline = get_post_meta($id, '_dl_job_deadline', true) ?: 'Tuyển liên tục';
                ?>
                <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-sm hover:border-gold transition-all flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-navy-deep mb-2"><a href="<?php the_permalink(); ?>" class="hover:text-gold no-underline"><?php the_title(); ?></a></h3>
                        <p class="text-xs text-slate-600 mb-3 flex flex-wrap items-center gap-3">
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-gold flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><rect width="20" height="12" x="2" y="6" rx="2" stroke-width="1.8"/><circle cx="12" cy="12" r="2" stroke-width="1.8"/></svg>
                                <span>Lương: <strong><?php echo esc_html($salary); ?></strong></span>
                            </span>
                            <span>|</span>
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-gold flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>Số lượng: <?php echo esc_html($qty); ?></span>
                            </span>
                        </p>
                    </div>
                    <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs">
                        <span class="text-slate-500 inline-flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-gold flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="1.8"/><polyline points="12 6 12 12 16 14" stroke-width="1.8"/></svg>
                            <span>Hạn: <?php echo esc_html($deadline); ?></span>
                        </span>
                        <a href="<?php the_permalink(); ?>" class="font-bold text-amber-700 hover:underline">Xem & Ứng tuyển &rarr;</a>
                    </div>
                </div>
                <?php
            }
            echo '</div>';
            wp_reset_postdata();
        } else {
            echo '<p class="text-center text-slate-500 py-8">Hiện tại khách sạn chưa có đợt tuyển dụng mới.</p>';
        }
        return ob_get_clean();
    }

    /**
     * Xử lý AJAX nộp hồ sơ ứng tuyển
     */
    public static function handle_job_application() {
        check_ajax_referer('dl_job_apply_nonce', 'nonce');

        $name      = sanitize_text_field($_POST['name'] ?? '');
        $phone     = sanitize_text_field($_POST['phone'] ?? '');
        $email     = sanitize_email($_POST['email'] ?? '');
        $job_id    = intval($_POST['job_id'] ?? 0);
        $job_title = sanitize_text_field($_POST['job_title'] ?? 'Ứng tuyển chung');
        $message   = sanitize_textarea_field($_POST['message'] ?? '');

        if (empty($name) || empty($phone)) {
            wp_send_json_error(array('message' => 'Vui lòng nhập đầy đủ Họ tên và Số điện thoại liên hệ.'));
        }

        // Xử lý upload file CV nếu có
        $cv_url = '';
        if (!empty($_FILES['cv_file']['name'])) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            $allowed_mimes = array(
                'pdf'  => 'application/pdf',
                'doc'  => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'jpg|jpeg' => 'image/jpeg',
                'png'  => 'image/png'
            );
            $uploaded = wp_handle_upload($_FILES['cv_file'], array('test_form' => false, 'mimes' => $allowed_mimes));
            if (isset($uploaded['url'])) {
                $cv_url = $uploaded['url'];
            } elseif (isset($uploaded['error'])) {
                wp_send_json_error(array('message' => 'Lỗi tải file CV: ' . $uploaded['error']));
            }
        }

        $app_data = array(
            'date'      => current_time('d/m/Y H:i'),
            'name'      => $name,
            'phone'     => $phone,
            'email'     => $email,
            'job_id'    => $job_id,
            'job_title' => $job_title,
            'message'   => $message,
            'cv_url'    => $cv_url
        );

        $apps = get_option('dl_job_applications', array());
        if (!is_array($apps)) $apps = array();
        $apps[] = $app_data;
        update_option('dl_job_applications', $apps, false);

        $hr = self::get_hr_settings();

        // 1. Gửi Email HTML thông báo cho Phòng Nhân sự & Ứng viên
        if (!isset($hr['hr_email_enabled']) || $hr['hr_email_enabled'] === '1') {
            self::send_hr_email_notification($app_data, $hr);
        }

        // 2. Gửi thông báo qua Telegram riêng của Phòng Nhân sự (nếu bật)
        if (!empty($hr['hr_telegram_enabled']) && $hr['hr_telegram_enabled'] === '1') {
            self::send_hr_telegram_notification($app_data, $hr);
        }

        // 3. Gửi thông báo qua Zalo riêng của Phòng Nhân sự (nếu bật)
        if (!empty($hr['hr_zalo_enabled']) && $hr['hr_zalo_enabled'] === '1') {
            self::send_hr_zalo_notification($app_data, $hr);
        }

        wp_send_json_success(array(
            'message' => 'Chúc mừng ' . esc_html($name) . '! Hồ sơ ứng tuyển vị trí "' . esc_html($job_title) . '" đã được gửi thành công tới Phòng Nhân sự Dragon Legend Hotel. Chúng tôi sẽ liên hệ phỏng vấn trong thời gian sớm nhất.'
        ));
    }

    /**
     * Chuẩn hóa SĐT sang 84...
     */
    public static function normalize_phone($phone) {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (empty($phone)) return '';
        if (strpos($phone, '84') === 0) return $phone;
        if (strpos($phone, '0') === 0) return '84' . substr($phone, 1);
        return '84' . $phone;
    }

    /**
     * Gửi Email HTML thông báo hồ sơ ứng tuyển cho Phòng Nhân sự & Ứng viên
     */
    public static function send_hr_email_notification($app_data, $hr) {
        $raw_emails = !empty($hr['hr_email']) ? $hr['hr_email'] : get_option('admin_email');
        $hr_emails  = array();
        foreach (explode(',', $raw_emails) as $em) {
            $em = trim($em);
            if (is_email($em)) {
                $hr_emails[] = $em;
            }
        }
        if (empty($hr_emails)) {
            $hr_emails[] = get_option('admin_email');
        }

        $hotel_name = get_bloginfo('name') ?: 'Dragon Legend Hotel';
        $logo_url   = 'https://dragonlegendhotel.com/wp-content/uploads/2015/07/logoweb-1.png';
        $zalo_phone = self::normalize_phone($app_data['phone']);
        $admin_apps_url = admin_url('edit.php?post_type=dl_job&page=dl-job-applications');

        $subject = '[Tuyển Dụng ' . $hotel_name . '] Hồ sơ ứng tuyển mới: ' . $app_data['job_title'] . ' — ' . $app_data['name'];

        $cv_row = !empty($app_data['cv_url'])
            ? '<a href="' . esc_url($app_data['cv_url']) . '" target="_blank" style="display:inline-block;padding:6px 14px;background:#16a34a;color:#ffffff;border-radius:6px;text-decoration:none;font-weight:700;font-size:12.5px;">📎 Tải / Xem File CV Đính Kèm</a>'
            : '<span style="color:#94a3b8;font-style:italic;">Ứng viên không đính kèm file CV</span>';

        $html_body = '<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Hồ sơ ứng tuyển mới</title></head>
<body style="margin:0;padding:25px 15px;background-color:#f1f5f9;font-family:\'Helvetica Neue\',Helvetica,Arial,sans-serif;color:#334155;line-height:1.6;">
  <div style="max-width:640px;margin:0 auto;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e2e8f0;box-shadow:0 10px 25px rgba(0,0,0,0.06);">
    <div style="background:linear-gradient(135deg,#48100f 0%,#2a0706 100%);padding:28px;text-align:center;border-bottom:3px solid #c5a880;">
      <img src="' . esc_url($logo_url) . '" alt="' . esc_attr($hotel_name) . '" style="max-height:46px;width:auto;margin-bottom:10px;">
      <span style="display:block;font-size:11px;text-transform:uppercase;letter-spacing:2px;color:#fef08a;font-weight:700;">PHÒNG HÀNH CHÍNH - NHÂN SỰ (HR DEPARTMENT)</span>
      <h1 style="color:#ffffff;font-size:20px;font-weight:700;margin:6px 0 0 0;">📋 HỒ SƠ ỨNG TUYỂN MỚI TỪ WEBSITE</h1>
    </div>
    <div style="padding:28px;">
      <p style="margin-top:0;font-size:14px;color:#475569;">Kính gửi <strong>' . esc_html($hr['hr_name'] ?: 'Phòng Nhân sự') . '</strong>,<br>Hệ thống Tuyển dụng vừa tiếp nhận một hồ sơ ứng viên mới nộp trực tuyến:</p>
      <table width="100%" cellspacing="0" cellpadding="10" style="font-size:13.5px;border:1px solid #e2e8f0;border-radius:10px;margin:18px 0;">
        <tr style="background:#fef2f2;">
          <td width="35%" style="color:#991b1b;font-weight:700;">Vị trí ứng tuyển:</td>
          <td style="color:#48100f;font-weight:800;font-size:15px;">' . esc_html($app_data['job_title']) . '</td>
        </tr>
        <tr>
          <td style="color:#64748b;font-weight:600;">Họ và tên ứng viên:</td>
          <td style="color:#0f172a;font-weight:700;font-size:14.5px;">' . esc_html($app_data['name']) . '</td>
        </tr>
        <tr style="background:#f8fafc;">
          <td style="color:#64748b;font-weight:600;">Số điện thoại:</td>
          <td>
            <a href="tel:' . esc_attr($app_data['phone']) . '" style="color:#0284c7;font-weight:700;text-decoration:none;">📞 ' . esc_html($app_data['phone']) . '</a>
            <a href="https://zalo.me/' . esc_attr($zalo_phone) . '" target="_blank" style="margin-left:10px;display:inline-block;padding:2px 8px;background:#0068ff;color:#fff;border-radius:4px;font-size:11px;text-decoration:none;font-weight:600;">Chat Zalo</a>
          </td>
        </tr>
        <tr>
          <td style="color:#64748b;font-weight:600;">Địa chỉ Email:</td>
          <td>' . (!empty($app_data['email']) ? '<a href="mailto:' . esc_attr($app_data['email']) . '" style="color:#0284c7;text-decoration:none;">' . esc_html($app_data['email']) . '</a>' : '<span style="color:#94a3b8;font-style:italic;">Không cung cấp</span>') . '</td>
        </tr>
        <tr style="background:#f8fafc;">
          <td style="color:#64748b;font-weight:600;">File CV đính kèm:</td>
          <td>' . $cv_row . '</td>
        </tr>
        <tr>
          <td style="color:#64748b;font-weight:600;vertical-align:top;">Kinh nghiệm / Giới thiệu:</td>
          <td style="color:#1e293b;white-space:pre-line;">' . (!empty($app_data['message']) ? esc_html($app_data['message']) : 'Không có ghi chú thêm') . '</td>
        </tr>
        <tr style="background:#f8fafc;">
          <td style="color:#64748b;font-weight:600;">Thời gian nộp:</td>
          <td style="color:#64748b;">' . esc_html($app_data['date']) . '</td>
        </tr>
      </table>
      <div style="text-align:center;margin:26px 0 8px 0;">
        <a href="' . esc_url($admin_apps_url) . '" target="_blank" style="display:inline-block;background:linear-gradient(135deg,#c5a880 0%,#b59265 100%);color:#07111e;font-weight:700;font-size:13px;text-transform:uppercase;letter-spacing:1px;padding:12px 26px;text-decoration:none;border-radius:30px;">👉 Xem Tất Cả Hồ Sơ Ứng Tuyển Trong WP-Admin</a>
      </div>
    </div>
    <div style="background:#f8fafc;padding:16px 28px;border-top:1px solid #e2e8f0;text-align:center;font-size:12px;color:#64748b;">
      <strong>' . esc_html($hr['hr_name']) . ' — ' . esc_html($hotel_name) . '</strong><br>
      Hotline Nhân sự: ' . esc_html($hr['hr_phone']) . '
    </div>
  </div>
</body>
</html>';

        $headers = array('Content-Type: text/html; charset=UTF-8');
        if (!empty($app_data['email']) && is_email($app_data['email'])) {
            $headers[] = 'Reply-To: ' . $app_data['name'] . ' <' . $app_data['email'] . '>';
        }
        $sent = @wp_mail($hr_emails, $subject, $html_body, $headers);

        // Gửi email xác nhận cho ứng viên nếu ứng viên có nhập email
        if ((!isset($hr['hr_candidate_email_enabled']) || $hr['hr_candidate_email_enabled'] === '1') && !empty($app_data['email']) && is_email($app_data['email'])) {
            $cand_subject = '[' . $hotel_name . '] Xác nhận tiếp nhận hồ sơ ứng tuyển vị trí: ' . $app_data['job_title'];
            $cand_body = '<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:25px 15px;background-color:#f1f5f9;font-family:\'Helvetica Neue\',Helvetica,Arial,sans-serif;color:#334155;line-height:1.6;">
  <div style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e2e8f0;">
    <div style="background:linear-gradient(135deg,#48100f 0%,#2a0706 100%);padding:26px;text-align:center;border-bottom:3px solid #c5a880;">
      <img src="' . esc_url($logo_url) . '" alt="' . esc_attr($hotel_name) . '" style="max-height:44px;width:auto;margin-bottom:8px;">
      <h2 style="color:#ffffff;font-size:18px;margin:0;">XÁC NHẬN ĐÃ NHẬN HỒ SƠ ỨNG TUYỂN</h2>
    </div>
    <div style="padding:26px;">
      <p style="margin-top:0;">Chào bạn <strong>' . esc_html($app_data['name']) . '</strong>,</p>
      <p><strong>' . esc_html($hr['hr_name']) . ' — ' . esc_html($hotel_name) . '</strong> trân trọng cảm ơn bạn đã quan tâm đến cơ hội nghề nghiệp tại khách sạn và gửi hồ sơ ứng tuyển cho vị trí <strong style="color:#48100f;">' . esc_html($app_data['job_title']) . '</strong>.</p>
      <p>Hồ sơ của bạn đã được chuyển tới bộ phận Tuyển dụng để xem xét. Nếu hồ sơ phù hợp với tiêu chí tuyển dụng hiện tại, chúng tôi sẽ liên hệ trực tiếp với bạn qua số điện thoại <strong>' . esc_html($app_data['phone']) . '</strong> để sắp xếp lịch phỏng vấn.</p>
      <p style="margin-bottom:0;font-size:13px;color:#64748b;">Mọi thắc mắc về tuyển dụng, vui lòng liên hệ: <strong>' . esc_html($hr['hr_phone']) . '</strong> hoặc địa chỉ: ' . esc_html($hr['hr_address']) . '.</p>
    </div>
  </div>
</body>
</html>';
            @wp_mail($app_data['email'], $cand_subject, $cand_body, array('Content-Type: text/html; charset=UTF-8'));
        }

        return $sent;
    }

    /**
     * Gửi thông báo Telegram riêng cho Phòng Nhân sự khi có ứng viên mới
     */
    public static function send_hr_telegram_notification($app_data, $hr, $override_token = '', $override_chat_id = '') {
        $bot_token    = !empty($override_token) ? trim($override_token) : trim($hr['hr_telegram_bot_token'] ?? '');
        $chat_ids_str = !empty($override_chat_id) ? trim($override_chat_id) : trim($hr['hr_telegram_chat_id'] ?? '');

        if (empty($bot_token) || empty($chat_ids_str)) {
            return array('success' => false, 'message' => 'Chưa nhập Telegram Bot Token hoặc Chat ID của Phòng Nhân sự.');
        }

        $zalo_link = 'https://zalo.me/' . self::normalize_phone($app_data['phone'] ?? '');
        $admin_apps_url = admin_url('edit.php?post_type=dl_job&page=dl-job-applications');

        $lines = array(
            "📋 <b>[HỒ SƠ ỨNG TUYỂN MỚI - PHÒNG NHÂN SỰ]</b>",
            "━━━━━━━━━━━━━━━━━━",
            "💼 <b>Vị trí ứng tuyển:</b> <b>" . esc_html($app_data['job_title'] ?? '') . "</b>",
            "👤 <b>Ứng viên:</b> " . esc_html($app_data['name'] ?? ''),
            "📞 <b>Điện thoại:</b> " . esc_html($app_data['phone'] ?? '') . " (<a href=\"" . esc_url($zalo_link) . "\">Chat Zalo</a>)",
            "✉️ <b>Email:</b> " . esc_html(!empty($app_data['email']) ? $app_data['email'] : 'Không cung cấp'),
        );

        if (!empty($app_data['cv_url'])) {
            $lines[] = "📎 <b>File CV đính kèm:</b> <a href=\"" . esc_url($app_data['cv_url']) . "\">Bấm để xem / tải CV</a>";
        } else {
            $lines[] = "📎 <b>File CV:</b> Chưa đính kèm file";
        }

        $lines[] = "📝 <b>Kinh nghiệm / Giới thiệu:</b>\n" . esc_html(!empty($app_data['message']) ? $app_data['message'] : 'Không có');
        $lines[] = "━━━━━━━━━━━━━━━━━━";
        $lines[] = "👉 <a href=\"" . esc_url($admin_apps_url) . "\">Quản lý hồ sơ ứng tuyển trong WP-Admin</a>";

        $text = implode("\n", $lines);
        $chat_ids = array_filter(array_map('trim', explode(',', $chat_ids_str)));
        $endpoint = 'https://api.telegram.org/bot' . $bot_token . '/sendMessage';

        $ok_count = 0;
        $errors   = array();

        foreach ($chat_ids as $cid) {
            $resp = wp_remote_post($endpoint, array(
                'headers' => array('Content-Type' => 'application/json; charset=utf-8'),
                'body'    => wp_json_encode(array(
                    'chat_id'                  => $cid,
                    'text'                     => $text,
                    'parse_mode'               => 'HTML',
                    'disable_web_page_preview' => true,
                )),
                'timeout' => 15,
            ));

            if (is_wp_error($resp)) {
                $errors[] = $cid . ': ' . $resp->get_error_message();
                continue;
            }
            $json = json_decode(wp_remote_retrieve_body($resp), true);
            if (!empty($json['ok'])) {
                $ok_count++;
            } else {
                $errors[] = $cid . ': ' . ($json['description'] ?? 'Lỗi Telegram API');
            }
        }

        if ($ok_count > 0) {
            return array('success' => true, 'message' => sprintf('Đã gửi thông báo Telegram Nhân sự thành công tới %d/%d Chat ID!', $ok_count, count($chat_ids)));
        }
        return array('success' => false, 'message' => 'Lỗi gửi Telegram Nhân sự: ' . implode(' | ', $errors));
    }

    /**
     * Gửi thông báo Zalo riêng cho Phòng Nhân sự khi có ứng viên mới
     */
    public static function send_hr_zalo_notification($app_data, $hr, $override_phones = '', $override_mode = '') {
        $phones_str = !empty($override_phones) ? $override_phones : ($hr['hr_zalo_phones'] ?? '');
        $phones     = array_filter(array_map('trim', explode(',', $phones_str)));
        $mode       = !empty($override_mode) ? $override_mode : ($hr['hr_zalo_mode'] ?? 'simulation');

        if (empty($phones)) {
            return array('success' => false, 'message' => 'Chưa nhập số điện thoại Zalo của Phòng Nhân sự.');
        }

        $tpl = !empty($hr['hr_zalo_msg_template'])
            ? $hr['hr_zalo_msg_template']
            : "🔔 [HỒ SƠ ỨNG TUYỂN MỚI - DRAGON LEGEND] Vị trí: {job_title} | Ứng viên: {candidate_name} ({candidate_phone}) | Email: {candidate_email} | Lời nhắn: {message} | Link CV: {cv_url}";

        $msg = str_replace(
            array('{job_title}', '{candidate_name}', '{candidate_phone}', '{candidate_email}', '{message}', '{cv_url}'),
            array(
                $app_data['job_title'] ?? '',
                $app_data['name'] ?? '',
                $app_data['phone'] ?? '',
                !empty($app_data['email']) ? $app_data['email'] : 'Không có',
                !empty($app_data['message']) ? $app_data['message'] : 'Không có',
                !empty($app_data['cv_url']) ? $app_data['cv_url'] : 'Không đính kèm'
            ),
            $tpl
        );

        if ($mode === 'simulation') {
            return array(
                'success' => true,
                'message' => sprintf('[Mô phỏng Zalo HR thành công] Tới SĐT (%s): "%s"', implode(', ', $phones), $msg),
            );
        }

        if ($mode === 'webhook') {
            $wh_url = trim($hr['hr_zalo_webhook_url'] ?? '');
            if (empty($wh_url)) {
                return array('success' => false, 'message' => 'Chưa cấu hình Webhook URL Tuyển dụng.');
            }
            $headers = array('Content-Type' => 'application/json');
            if (!empty($hr['hr_zalo_webhook_secret'])) {
                $headers['Authorization'] = 'Bearer ' . $hr['hr_zalo_webhook_secret'];
            }
            $resp = wp_remote_post($wh_url, array(
                'headers' => $headers,
                'body'    => wp_json_encode(array(
                    'event'       => 'hr_job_application',
                    'department'  => 'HR',
                    'phones'      => array_map(array(__CLASS__, 'normalize_phone'), $phones),
                    'message'     => $msg,
                    'application' => $app_data,
                )),
                'timeout' => 12,
            ));
            if (is_wp_error($resp)) {
                return array('success' => false, 'message' => 'Lỗi gọi Webhook Zalo HR: ' . $resp->get_error_message());
            }
            $code = wp_remote_retrieve_response_code($resp);
            return array(
                'success' => ($code >= 200 && $code < 300),
                'message' => sprintf('Webhook Zalo HR phản hồi HTTP %d', $code),
            );
        }

        if ($mode === 'zns') {
            $token = !empty($hr['hr_zalo_access_token']) ? trim($hr['hr_zalo_access_token']) : '';
            if (empty($token) && class_exists('DragonLegend_Zalo_Service')) {
                $core_s = DragonLegend_Zalo_Service::get_settings();
                $token  = trim($core_s['zalo_access_token'] ?? '');
            }
            $tpl_id = trim($hr['hr_zalo_template_id'] ?? '');
            if (empty($token) || empty($tpl_id)) {
                return array('success' => false, 'message' => 'Chưa cấu hình Zalo OA Access Token hoặc ZNS Template ID cho Nhân sự.');
            }
            $ok = 0;
            foreach ($phones as $p) {
                $resp = wp_remote_post('https://business.openapi.zalo.me/message/template', array(
                    'headers' => array('Content-Type' => 'application/json', 'access_token' => $token),
                    'body'    => wp_json_encode(array(
                        'phone'         => self::normalize_phone($p),
                        'template_id'   => $tpl_id,
                        'template_data' => array(
                            'candidate_name'  => $app_data['name'] ?? '',
                            'candidate_phone' => $app_data['phone'] ?? '',
                            'job_title'       => $app_data['job_title'] ?? '',
                            'apply_date'      => $app_data['date'] ?? date('d/m/Y'),
                        ),
                    )),
                    'timeout' => 15,
                ));
                if (!is_wp_error($resp)) {
                    $j = json_decode(wp_remote_retrieve_body($resp), true);
                    if (isset($j['error']) && $j['error'] === 0) $ok++;
                }
            }
            return array(
                'success' => ($ok > 0),
                'message' => $ok > 0 ? sprintf('Đã gửi ZNS Nhân sự tới %d/%d SĐT', $ok, count($phones)) : 'Gửi ZNS Nhân sự thất bại, vui lòng kiểm tra Token/Template ID.',
            );
        }

        return array('success' => false, 'message' => 'Chế độ Zalo không hợp lệ.');
    }

    /**
     * AJAX Test Telegram HR
     */
    public static function handle_test_hr_telegram() {
        check_ajax_referer('dl_hr_test_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error(array('message' => 'Không có quyền'));

        $hr = self::get_hr_settings();
        $bot_token = sanitize_text_field($_POST['bot_token'] ?? '');
        $chat_id   = sanitize_text_field($_POST['chat_id'] ?? '');

        $sample_app = array(
            'date'      => current_time('d/m/Y H:i'),
            'name'      => 'Nguyễn Văn A (Ứng viên Test)',
            'phone'     => '0987 654 321',
            'email'     => 'ungvien.test@gmail.com',
            'job_title' => 'Chuyên viên Lễ tân Khách sạn (Front Office)',
            'message'   => 'Đây là tin nhắn kiểm tra kết nối thông báo Tuyển dụng dành riêng cho Phòng Nhân sự Dragon Legend Hotel.',
            'cv_url'    => home_url('/tuyen-dung/'),
        );

        $res = self::send_hr_telegram_notification($sample_app, $hr, $bot_token, $chat_id);
        if ($res['success']) {
            wp_send_json_success($res);
        } else {
            wp_send_json_error($res);
        }
    }

    /**
     * AJAX Test Zalo HR
     */
    public static function handle_test_hr_zalo() {
        check_ajax_referer('dl_hr_test_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error(array('message' => 'Không có quyền'));

        $hr     = self::get_hr_settings();
        $phones = sanitize_text_field($_POST['phones'] ?? '');
        $mode   = sanitize_text_field($_POST['mode'] ?? '');

        $sample_app = array(
            'date'      => current_time('d/m/Y H:i'),
            'name'      => 'Nguyễn Văn A (Ứng viên Test)',
            'phone'     => '0987 654 321',
            'email'     => 'ungvien.test@gmail.com',
            'job_title' => 'Chuyên viên Lễ tân Khách sạn (Front Office)',
            'message'   => 'Kiểm tra kết nối Zalo Tuyển dụng Phòng Nhân sự.',
            'cv_url'    => home_url('/tuyen-dung/'),
        );

        $res = self::send_hr_zalo_notification($sample_app, $hr, $phones, $mode);
        if ($res['success']) {
            wp_send_json_success($res);
        } else {
            wp_send_json_error($res);
        }
    }

    /**
     * AJAX Test Email HR
     */
    public static function handle_test_hr_email() {
        check_ajax_referer('dl_hr_test_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error(array('message' => 'Không có quyền'));

        $hr = self::get_hr_settings();
        if (!empty($_POST['emails'])) {
            $hr['hr_email'] = sanitize_text_field($_POST['emails']);
        }
        $hr['hr_candidate_email_enabled'] = '0';

        $sample_app = array(
            'date'      => current_time('d/m/Y H:i'),
            'name'      => 'Nguyễn Văn A (Thử nghiệm Email Nhân sự)',
            'phone'     => '0987 654 321',
            'email'     => '',
            'job_title' => 'Nhân viên Lễ tân (Front Office) — Test Email HR',
            'message'   => 'Đây là email kiểm tra chức năng nhận hồ sơ ứng tuyển dành riêng cho Phòng Nhân sự Dragon Legend Hotel.',
            'cv_url'    => home_url('/tuyen-dung/'),
        );

        $sent = self::send_hr_email_notification($sample_app, $hr);
        if ($sent) {
            wp_send_json_success(array('message' => 'Đã gửi email hồ sơ mẫu thành công tới: ' . esc_html($hr['hr_email'])));
        } else {
            wp_send_json_error(array('message' => 'Gửi email thất bại. Vui lòng kiểm tra cấu hình SMTP trong mục Đơn đặt phòng -> Cấu hình Telegram, Zalo & Email.'));
        }
    }

    /**
     * Tạo dữ liệu mẫu khi kích hoạt plugin lần đầu
     */
    public static function create_sample_jobs() {
        $existing = get_posts(array('post_type' => 'dl_job', 'posts_per_page' => 1, 'post_status' => 'any'));
        if (!empty($existing)) {
            return;
        }

        // Tạo các bộ phận mẫu
        $depts = array(
            'fo'    => 'Bộ phận Tiền sảnh (Front Office)',
            'hk'    => 'Bộ phận Buồng phòng (Housekeeping)',
            'fb'    => 'Bộ phận Ẩm thực & Nhà hàng (F&B)',
            'sales' => 'Bộ phận Kinh doanh & Tiếp thị (Sales & Marketing)',
            'tech'  => 'Bộ phận Kỹ thuật & An ninh'
        );
        $dept_ids = array();
        foreach ($depts as $slug => $name) {
            $term = term_exists($name, 'dl_job_department');
            if (!$term) {
                $term = wp_insert_term($name, 'dl_job_department', array('slug' => $slug));
            }
            if (!is_wp_error($term)) {
                $dept_ids[$slug] = is_array($term) ? $term['term_id'] : $term;
            }
        }

        $sample_jobs = array(
            array(
                'title'        => 'Nhân Viên Lễ Tân Khách Sạn (Front Desk Receptionist)',
                'dept'         => 'fo',
                'status'       => 'urgent',
                'salary'       => '8.000.000 - 12.000.000 VNĐ + Service Charge',
                'quantity'     => '02 nhân sự',
                'type'         => 'Theo ca (Xoay ca linh hoạt)',
                'experience'   => 'Từ 01 năm kinh nghiệm khách sạn 3-4 sao',
                'deadline'     => '30/11/2026',
                'excerpt'      => 'Chào đón khách hàng, thực hiện thủ tục Check-in / Check-out chuyên nghiệp và hỗ trợ thông tin dịch vụ cho du khách lưu trú tại Dragon Legend Hotel.',
                'content'      => '<p><strong>Mô tả công việc chi tiết:</strong></p><ul><li>Đón tiếp khách hàng với thái độ niềm nở, chuyên nghiệp theo tiêu chuẩn khách sạn 4 sao.</li><li>Thực hiện quy trình nhận phòng (Check-in) và trả phòng (Check-out) nhanh chóng, chính xác trên phần mềm quản lý khách sạn.</li><li>Tư vấn, giới thiệu các dịch vụ tiện ích tại khách sạn (Nhà hàng Dragon Pearl, Spa, Bể bơi, Tour tham quan Vịnh Hạ Long).</li><li>Tiếp nhận và phối hợp xử lý các yêu cầu phát sinh của khách lưu trú trong ca trực.</li></ul>',
                'requirements' => "Nam/Nữ độ tuổi từ 20 - 30, ngoại hình ưa nhìn, tác phong lịch sự.\nTốt nghiệp Cao đẳng/Đại học chuyên ngành Quản trị Khách sạn, Du lịch, Ngoại ngữ hoặc tương đương.\nGiao tiếp tiếng Anh lưu loát (biết thêm tiếng Trung hoặc tiếng Hàn là lợi thế lớn).\nKỹ năng giao tiếp, xử lý tình huống tốt, trung thực và nhiệt huyết.",
                'benefits'     => "Thu nhập hấp dẫn: Lương cứng + Phụ cấp ca đêm + Thưởng phí dịch vụ (Service Charge) hàng tháng + Thưởng doanh số.\nHỗ trợ bữa ăn giữa ca miễn phí tại nhà ăn cán bộ nhân viên.\nĐược cấp phát đồng phục tiêu chuẩn và giặt là miễn phí.\nĐóng đầy đủ BHXH, BHYT, BHTN và hưởng 12 ngày phép năm theo Luật Lao động.\nThưởng các ngày Lễ, Tết, sinh nhật, lương tháng 13 và du lịch thường niên."
            ),
            array(
                'title'        => 'Chuyên Viên Kinh Doanh Khách Sạn (Sales Executive - TA/TO/MICE)',
                'dept'         => 'sales',
                'status'       => 'open',
                'salary'       => '10.000.000 - 18.000.000 VNĐ + Hoa hồng doanh số',
                'quantity'     => '02 nhân sự',
                'type'         => 'Toàn thời gian (Full-time)',
                'experience'   => ' tối thiểu 01 năm kinh nghiệm Sales Khách sạn / Du lịch',
                'deadline'     => '30/11/2026',
                'excerpt'      => 'Phát triển mạng lưới đối tác lữ hành (TA/TO), khách đoàn doanh nghiệp (MICE), tư vấn báo giá phòng nghỉ và dịch vụ hội thảo, tiệc gala tại khách sạn.',
                'content'      => '<p><strong>Mô tả công việc chi tiết:</strong></p><ul><li>Tìm kiếm, mở rộng và chăm sóc hệ thống khách hàng đại lý du lịch (TA/TO), các doanh nghiệp tổ chức hội nghị, sự kiện (MICE).</li><li>Xây dựng bảng báo giá, hợp đồng dịch vụ lưu trú, tiệc gala dinner và phòng hội thảo theo yêu cầu khách hàng.</li><li>Phối hợp chặt chẽ với các bộ phận Lễ tân, Nhà hàng, Bếp và Buồng phòng để đảm bảo chất lượng phục vụ các đoàn khách chu đáo nhất.</li><li>Thực hiện các kế hoạch kích cầu doanh thu theo từng mùa du lịch tại Hạ Long.</li></ul>',
                'requirements' => "Tốt nghiệp Cao đẳng/Đại học chuyên ngành Kinh tế, Quản trị Kinh doanh, Du lịch - Khách sạn.\nCó tối thiểu 01 năm kinh nghiệm ở vị trí Sales Khách sạn hoặc Lữ hành.\nKỹ năng đàm phán, thuyết trình và chăm sóc khách hàng chuyên nghiệp.\nNăng động, chủ động trong công việc và chịu được áp lực doanh số.",
                'benefits'     => "Lương cơ bản cạnh tranh + Thưởng % hoa hồng doanh số cao + Thưởng Service Charge.\nMôi trường làm việc 4 sao chuyên nghiệp, cơ hội thăng tiến lên Trưởng nhóm / Quản lý Kinh doanh.\nHỗ trợ ăn ca, phụ cấp điện thoại, công tác phí đầy đủ.\nĐầy đủ chế độ BHXH, BHYT, thưởng Lễ Tết và lương tháng 13."
            ),
            array(
                'title'        => 'Nhân Viên Phục Vụ Nhà Hàng & Sự Kiện (F&B Attendant)',
                'dept'         => 'fb',
                'status'       => 'open',
                'salary'       => '7.000.000 - 10.000.000 VNĐ + Tip & Service Charge',
                'quantity'     => '04 nhân sự',
                'type'         => 'Theo ca (Xoay ca linh hoạt)',
                'experience'   => 'Không yêu cầu kinh nghiệm (Sẽ được đào tạo)',
                'deadline'     => 'Tuyển liên tục',
                'excerpt'      => 'Phục vụ buffet sáng, thực đơn gọi món (À la carte) và các buổi tiệc Gala tại Nhà hàng Dragon Pearl theo tiêu chuẩn dịch vụ 4 sao.',
                'content'      => '<p><strong>Mô tả công việc chi tiết:</strong></p><ul><li>Chuẩn bị công cụ dụng cụ, sắp xếp bàn ăn và khu vực quầy Buffet gọn gàng, sạch sẽ trước giờ đón khách.</li><li>Đón tiếp, giới thiệu thực đơn và phục vụ đồ ăn, thức uống cho thực khách tại Nhà hàng Dragon Pearl.</li><li>Tham gia phục vụ các bữa tiệc hội nghị, tiệc cưới, Gala Dinner của khách đoàn.</li><li>Đảm bảo vệ sinh an toàn thực phẩm và tiêu chuẩn phục vụ nhà hàng khách sạn 4 sao.</li></ul>',
                'requirements' => "Nam/Nữ từ 18 - 32 tuổi, sức khỏe tốt, nhanh nhẹn, trung thực.\nThái độ lịch sự, thân thiện, có tinh thần trách nhiệm và hợp tác nhóm.\nChấp nhận sinh viên mới ra trường hoặc lao động chưa có kinh nghiệm (khách sạn sẽ đào tạo bài bản).",
                'benefits'     => "Thu nhập ổn định: Lương cơ bản + Thưởng Service Charge + Tiền Tip.\nĐược đào tạo nghiệp vụ bàn, bar tiêu chuẩn khách sạn 4 sao miễn phí.\nHỗ trợ ăn ca, đồng phục, đóng bảo hiểm đầy đủ khi ký hợp đồng chính thức."
            ),
            array(
                'title'        => 'Nhân Viên Buồng Phòng (Housekeeping Attendant)',
                'dept'         => 'hk',
                'status'       => 'open',
                'salary'       => '7.500.000 - 10.500.000 VNĐ + Thưởng năng suất',
                'quantity'     => '03 nhân sự',
                'type'         => 'Toàn thời gian (Full-time)',
                'experience'   => 'Ưu tiên có kinh nghiệm làm buồng phòng',
                'deadline'     => 'Tuyển liên tục',
                'excerpt'      => 'Thực hiện vệ sinh, làm phòng khách lưu trú và chuẩn bị phòng sạch đẹp, thơm tho theo đúng quy chuẩn 4 sao của Dragon Legend Hotel.',
                'content'      => '<p><strong>Mô tả công việc chi tiết:</strong></p><ul><li>Thực hiện dọn dẹp, thay đồ vải và vệ sinh phòng khách (phòng khách trả và phòng khách đang lưu trú) theo tiêu chuẩn của khách sạn.</li><li>Kiểm tra tình trạng trang thiết bị và minibar trong phòng khi khách làm thủ tục trả phòng (Check-out).</li><li>Bổ sung đầy đủ đồ dùng tiêu hao (Amenities), nước suối, trà, cà phê trong phòng khách.</li><li>Bảo quản trang thiết bị, dụng cụ làm việc và báo cáo kịp thời các hư hỏng cho bộ phận Kỹ thuật.</li></ul>',
                'requirements' => "Nam/Nữ sức khỏe tốt, chăm chỉ, cẩn thận, thật thà.\nƯu tiên ứng viên đã từng làm buồng phòng tại các khách sạn, khu nghỉ dưỡng.\nChưa có kinh nghiệm sẽ được Trưởng bộ phận hướng dẫn trực tiếp.",
                'benefits'     => "Lương cơ bản + Phụ cấp làm thêm phòng + Thưởng phí dịch vụ hàng tháng.\nBao ăn ca tại khách sạn, cấp phát đồng phục làm việc.\nChế độ BHXH, BHYT, phép năm, thưởng Lễ Tết đầy đủ và đúng hạn."
            )
        );

        foreach ($sample_jobs as $job) {
            $post_id = wp_insert_post(array(
                'post_title'   => $job['title'],
                'post_excerpt' => $job['excerpt'],
                'post_content' => $job['content'],
                'post_status'  => 'publish',
                'post_type'    => 'dl_job'
            ));
            if ($post_id && !is_wp_error($post_id)) {
                update_post_meta($post_id, '_dl_job_status', $job['status']);
                update_post_meta($post_id, '_dl_job_salary', $job['salary']);
                update_post_meta($post_id, '_dl_job_quantity', $job['quantity']);
                update_post_meta($post_id, '_dl_job_type', $job['type']);
                update_post_meta($post_id, '_dl_job_experience', $job['experience']);
                update_post_meta($post_id, '_dl_job_deadline', $job['deadline']);
                update_post_meta($post_id, '_dl_job_location', 'Dragon Legend Hotel - Đại lộ Hạ Long Marine, Bãi Cháy, Hạ Long');
                update_post_meta($post_id, '_dl_job_requirements', $job['requirements']);
                update_post_meta($post_id, '_dl_job_benefits', $job['benefits']);

                if (isset($dept_ids[$job['dept']])) {
                    wp_set_object_terms($post_id, intval($dept_ids[$job['dept']]), 'dl_job_department');
                }
            }
        }
    }
}

DragonLegend_Recruitment::init();

register_activation_hook(__FILE__, function() {
    DragonLegend_Recruitment::register_post_type_and_taxonomy();
    DragonLegend_Recruitment::create_sample_jobs();
    flush_rewrite_rules();
});

register_deactivation_hook(__FILE__, function() {
    flush_rewrite_rules();
});
