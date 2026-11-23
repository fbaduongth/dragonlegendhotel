<?php
/**
 * Dragon Legend Hotel Core - Booking Notifications & Zalo Settings Page
 * Màn hình cấu hình thông báo Zalo & Email trong WP-Admin
 *
 * @package DragonLegendCore
 */

if (!defined('ABSPATH')) {
    exit;
}

class DragonLegend_Booking_Settings {

    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'add_settings_menu'));
        add_action('admin_init', array(__CLASS__, 'handle_save_settings'));
        add_action('wp_ajax_dl_test_zalo_notification', array(__CLASS__, 'handle_test_zalo'));
        add_action('wp_ajax_dl_test_telegram_notification', array(__CLASS__, 'handle_test_telegram'));
        add_action('wp_ajax_dl_test_email_notification', array(__CLASS__, 'handle_test_email'));
    }

    public static function add_settings_menu() {
        $inquiries = get_option('dl_contact_inquiries', array());
        $inq_count = is_array($inquiries) ? count($inquiries) : 0;
        $badge = $inq_count > 0 ? ' <span class="awaiting-mod">' . $inq_count . '</span>' : '';

        add_submenu_page(
            'edit.php?post_type=dl_booking',
            __('Tin Nhắn Liên Hệ & Tư Vấn', 'dragonlegend-core'),
            __('Tin nhắn Liên hệ', 'dragonlegend-core') . $badge,
            'manage_options',
            'dl-contact-inquiries',
            array(__CLASS__, 'render_contact_inquiries_page')
        );

        add_submenu_page(
            'edit.php?post_type=dl_booking',
            __('Cấu Hình Telegram, Zalo & Email (Đặt Phòng & Liên Hệ)', 'dragonlegend-core'),
            __('Cấu hình Telegram, Zalo & Email', 'dragonlegend-core'),
            'manage_options',
            'dl-booking-settings',
            array(__CLASS__, 'render_settings_page')
        );
    }

    /**
     * Màn hình xem danh sách khách hàng gửi Form Liên Hệ (/lien-he/)
     */
    public static function render_contact_inquiries_page() {
        if (isset($_GET['del_inq']) && check_admin_referer('dl_del_inquiry')) {
            $idx = intval($_GET['del_inq']);
            $inquiries = get_option('dl_contact_inquiries', array());
            if (isset($inquiries[$idx])) {
                unset($inquiries[$idx]);
                update_option('dl_contact_inquiries', array_values($inquiries), false);
                echo '<div class="notice notice-success is-dismissible"><p>Đã xóa tin nhắn liên hệ.</p></div>';
            }
        }

        $inquiries = get_option('dl_contact_inquiries', array());
        if (!is_array($inquiries)) $inquiries = array();
        $inquiries_rev = array_reverse($inquiries, true);
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">📩 Danh Sách Yêu Cầu Liên Hệ & Tư Vấn Từ Website</h1>
            <a href="<?php echo esc_url(admin_url('edit.php?post_type=dl_booking&page=dl-booking-settings')); ?>" class="page-title-action">⚙️ Cấu hình nhận thông báo Telegram / Zalo / Email</a>
            <hr class="wp-header-end">
            <p>Toàn bộ yêu cầu gửi từ trang <a href="<?php echo esc_url(home_url('/lien-he/')); ?>" target="_blank">Liên hệ (/lien-he/)</a> được lưu trữ tại đây và đồng thời gửi về Email, Telegram, Zalo của bộ phận Sale/Lễ tân.</p>
            <table class="wp-list-table widefat fixed striped" style="margin-top:15px;">
                <thead>
                    <tr>
                        <th style="width:140px;">Thời gian</th>
                        <th style="width:180px;">Khách hàng</th>
                        <th style="width:200px;">Số điện thoại / Email</th>
                        <th style="width:180px;">Chủ đề quan tâm</th>
                        <th>Nội dung yêu cầu</th>
                        <th style="width:90px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($inquiries_rev)) : ?>
                        <tr><td colspan="6">Chưa có tin nhắn liên hệ nào từ website.</td></tr>
                    <?php else : ?>
                        <?php foreach ($inquiries_rev as $idx => $inq) : ?>
                            <tr>
                                <td><?php echo esc_html($inq['date'] ?? ''); ?></td>
                                <td><strong><?php echo esc_html($inq['name'] ?? ''); ?></strong></td>
                                <td>
                                    📞 <a href="tel:<?php echo esc_attr($inq['phone'] ?? ''); ?>"><strong><?php echo esc_html($inq['phone'] ?? ''); ?></strong></a>
                                    <?php if (!empty($inq['phone'])) : ?>
                                        <a href="https://zalo.me/<?php echo esc_attr(DragonLegend_Zalo_Service::normalize_phone($inq['phone'])); ?>" target="_blank" style="margin-left:4px;font-size:11px;background:#0068ff;color:#fff;padding:1px 6px;border-radius:4px;text-decoration:none;">Zalo</a>
                                    <?php endif; ?>
                                    <br>
                                    <?php if (!empty($inq['email'])) : ?>
                                        ✉️ <a href="mailto:<?php echo esc_attr($inq['email']); ?>"><?php echo esc_html($inq['email']); ?></a>
                                    <?php endif; ?>
                                </td>
                                <td><span style="font-weight:600;color:#48100f;"><?php echo esc_html($inq['subject'] ?? ''); ?></span></td>
                                <td><?php echo nl2br(esc_html($inq['message'] ?? '')); ?></td>
                                <td>
                                    <a href="<?php echo esc_url(wp_nonce_url(admin_url('edit.php?post_type=dl_booking&page=dl-contact-inquiries&del_inq=' . $idx), 'dl_del_inquiry')); ?>" class="button button-small" onclick="return confirm('Xóa tin nhắn liên hệ này?');" style="color:#b91c1c;border-color:#fca5a5;">Xóa</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    /**
     * Xử lý lưu form cấu hình
     */
    public static function handle_save_settings() {
        if (!isset($_POST['dl_save_booking_settings'])) {
            return;
        }

        if (!check_admin_referer('dl_booking_settings_nonce', 'dl_booking_nonce')) {
            return;
        }

        if (!current_user_can('manage_options')) {
            wp_die(__('Bạn không có quyền thực hiện hành động này.', 'dragonlegend-core'));
        }

        $settings = array(
            'zalo_enabled'              => isset($_POST['zalo_enabled']) ? '1' : '0',
            'zalo_mode'                 => sanitize_text_field($_POST['zalo_mode'] ?? 'simulation'),
            'admin_zalo_phones'         => sanitize_text_field($_POST['admin_zalo_phones'] ?? ''),
            'zalo_oa_id'                => sanitize_text_field($_POST['zalo_oa_id'] ?? ''),
            'zalo_app_id'               => sanitize_text_field($_POST['zalo_app_id'] ?? ''),
            'zalo_secret_key'           => sanitize_text_field($_POST['zalo_secret_key'] ?? ''),
            'zalo_access_token'         => sanitize_textarea_field($_POST['zalo_access_token'] ?? ''),
            'zalo_refresh_token'        => sanitize_textarea_field($_POST['zalo_refresh_token'] ?? ''),
            'zns_template_customer'     => sanitize_text_field($_POST['zns_template_customer'] ?? ''),
            'zns_template_admin'        => sanitize_text_field($_POST['zns_template_admin'] ?? ''),
            'webhook_url'               => esc_url_raw($_POST['webhook_url'] ?? ''),
            'webhook_secret'            => sanitize_text_field($_POST['webhook_secret'] ?? ''),
            'zalo_customer_msg'         => wp_kses_post($_POST['zalo_customer_msg'] ?? ''),
            'zalo_admin_msg'            => wp_kses_post($_POST['zalo_admin_msg'] ?? ''),
            'zalo_notify_contact'       => isset($_POST['zalo_notify_contact']) ? '1' : '0',
            'zalo_contact_admin_msg'    => wp_kses_post($_POST['zalo_contact_admin_msg'] ?? ''),
            'telegram_enabled'          => isset($_POST['telegram_enabled']) ? '1' : '0',
            'telegram_bot_token'        => sanitize_text_field($_POST['telegram_bot_token'] ?? ''),
            'telegram_chat_id'          => sanitize_text_field($_POST['telegram_chat_id'] ?? ''),
            'telegram_notify_booking'   => isset($_POST['telegram_notify_booking']) ? '1' : '0',
            'telegram_notify_contact'   => isset($_POST['telegram_notify_contact']) ? '1' : '0',
            'email_enabled'             => isset($_POST['email_enabled']) ? '1' : '0',
            'admin_notification_emails' => sanitize_text_field($_POST['admin_notification_emails'] ?? ''),
            'email_subject_template'    => sanitize_text_field($_POST['email_subject_template'] ?? ''),
            'customer_email_enabled'    => isset($_POST['customer_email_enabled']) ? '1' : '0',
        );

        DragonLegend_Zalo_Service::save_settings($settings);

        // Lưu cấu hình Máy Chủ Mail (SMTP Server)
        if (class_exists('DragonLegend_SMTP_Service')) {
            $existing_smtp = DragonLegend_SMTP_Service::get_settings();
            $smtp_settings = array(
                'smtp_enabled'    => isset($_POST['smtp_enabled']) ? '1' : '0',
                'smtp_host'       => sanitize_text_field($_POST['smtp_host'] ?? 'smtp.gmail.com'),
                'smtp_port'       => intval($_POST['smtp_port'] ?? 465),
                'smtp_encryption' => sanitize_text_field($_POST['smtp_encryption'] ?? 'ssl'),
                'smtp_auth'       => isset($_POST['smtp_auth']) ? 'yes' : 'no',
                'smtp_username'   => sanitize_text_field($_POST['smtp_username'] ?? ''),
                'smtp_password'   => !empty($_POST['smtp_password']) ? $_POST['smtp_password'] : ($existing_smtp['smtp_password'] ?? ''),
                'smtp_from_email' => sanitize_email($_POST['smtp_from_email'] ?? get_option('admin_email')),
                'smtp_from_name'  => sanitize_text_field($_POST['smtp_from_name'] ?? 'Dragon Legend Hotel'),
            );
            DragonLegend_SMTP_Service::save_settings($smtp_settings);
        }

        wp_redirect(add_query_arg(array(
            'post_type' => 'dl_booking',
            'page'      => 'dl-booking-settings',
            'saved'     => '1'
        ), admin_url('edit.php')));
        exit;
    }

    /**
     * AJAX Test gửi Zalo
     */
    public static function handle_test_zalo() {
        check_ajax_referer('dl_test_notification_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Không có quyền truy cập'));
        }

        $phone = sanitize_text_field($_POST['phone'] ?? '');
        $msg   = sanitize_textarea_field($_POST['message'] ?? '');

        if (empty($phone)) {
            wp_send_json_error(array('message' => 'Vui lòng nhập số điện thoại nhận tin thử nghiệm!'));
        }

        $res = DragonLegend_Zalo_Service::send_test_message($phone, $msg);
        if ($res['success']) {
            wp_send_json_success(array('message' => $res['message']));
        } else {
            wp_send_json_error(array('message' => $res['message']));
        }
    }

    /**
     * AJAX Test gửi Telegram
     */
    public static function handle_test_telegram() {
        check_ajax_referer('dl_test_notification_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Không có quyền truy cập'));
        }

        $bot_token = sanitize_text_field($_POST['bot_token'] ?? '');
        $chat_id   = sanitize_text_field($_POST['chat_id'] ?? '');
        $msg       = sanitize_textarea_field($_POST['message'] ?? '');

        $res = DragonLegend_Zalo_Service::send_test_telegram($bot_token, $chat_id, $msg);
        if (!empty($res['success'])) {
            wp_send_json_success(array('message' => $res['message']));
        } else {
            wp_send_json_error(array('message' => $res['message']));
        }
    }

    /**
     * AJAX Test gửi Email (Hỗ trợ SMTP Test Connection & Debug Transcript)
     */
    public static function handle_test_email() {
        check_ajax_referer('dl_test_notification_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Không có quyền truy cập'));
        }

        $email = sanitize_email($_POST['email'] ?? '');
        if (empty($email)) {
            wp_send_json_error(array('message' => 'Vui lòng nhập địa chỉ email nhận thư thử nghiệm!'));
        }

        // 1. Nếu đang bật máy chủ SMTP, dùng bộ kiểm tra chuyên dụng kèm Debug Log
        if (class_exists('DragonLegend_SMTP_Service')) {
            $smtp_settings = DragonLegend_SMTP_Service::get_settings();
            if (!empty($smtp_settings['smtp_enabled'])) {
                $test_res = DragonLegend_SMTP_Service::test_smtp_connection($email);
                if ($test_res['success']) {
                    wp_send_json_success(array(
                        'message'   => $test_res['message'],
                        'debug_log' => $test_res['debug_log']
                    ));
                } else {
                    wp_send_json_error(array(
                        'message'   => $test_res['message'],
                        'debug_log' => $test_res['debug_log']
                    ));
                }
            }
        }

        // 2. Nếu SMTP đang tắt, thử nghiệm gửi qua hàm mail() mặc định của máy chủ
        $hotel_name = get_bloginfo('name') ?: 'Dragon Legend Hotel';
        $subject = sprintf('[%s] Thư thử nghiệm cấu hình thông báo đặt phòng (PHP Mail mặc định)', $hotel_name);
        
        $body = DragonLegend_Booking_Handler::build_admin_html_email('DLH-TEST', array(
            'guest_name'  => 'Khách Hàng Thử Nghiệm',
            'guest_phone' => '0987 654 321',
            'guest_email' => $email,
            'guest_note'  => 'Đây là bức thư kiểm tra kết nối hệ thống gửi email tự động (chế độ PHP mail() mặc định - Chưa bật SMTP).',
            'room_name'   => 'Superior Double / Twin Room',
            'checkin'     => date('Y-m-d'),
            'checkout'    => date('Y-m-d', strtotime('+1 day')),
            'nights'      => 1,
            'rooms'       => 1,
            'adults'      => 2,
            'children'    => 0,
            'promo_code'  => 'DRAGON2026',
            'discount'    => 95000,
            'grand_total' => 923400,
        ));

        $headers = array('Content-Type: text/html; charset=UTF-8');
        $sent = @wp_mail($email, $subject, $body, $headers);

        if ($sent) {
            wp_send_json_success(array(
                'message'   => sprintf('Đã gửi thư thử nghiệm thành công tới %s lúc %s qua hàm mail() mặc định của máy chủ!', $email, current_time('H:i d/m/Y')),
                'debug_log' => ''
            ));
        } else {
            wp_send_json_error(array(
                'message'   => sprintf('Không thể gửi email tới %s qua PHP mail() mặc định. Vui lòng BẬT máy chủ SMTP ở mục 5 và điền thông số máy chủ gửi thư để gửi thư ổn định.', $email),
                'debug_log' => ''
            ));
        }
    }

    /**
     * Render giao diện trang cấu hình
     */
    public static function render_settings_page() {
        $settings      = DragonLegend_Zalo_Service::get_settings();
        $smtp_settings = class_exists('DragonLegend_SMTP_Service') ? DragonLegend_SMTP_Service::get_settings() : array();
        $is_saved      = isset($_GET['saved']) && $_GET['saved'] === '1';
        $test_nonce    = wp_create_nonce('dl_test_notification_nonce');
        ?>
        <div class="wrap" style="max-width: 1100px; margin-top: 20px;">
            <div style="background: linear-gradient(135deg, #07111e 0%, #112338 100%); color: #fff; padding: 24px 30px; border-radius: 16px; margin-bottom: 25px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.2); border: 1px solid rgba(197, 168, 128, 0.3);">
                <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
                    <div>
                        <span style="font-size: 11px; text-transform: uppercase; letter-spacing: 2px; color: #c5a880; font-weight: 700; display: block; margin-bottom: 4px;">Hệ Thống Đặt Phòng & Liên Hệ Dragon Legend</span>
                        <h1 style="color: #fff; font-size: 24px; font-weight: 700; margin: 0; padding: 0;">Cấu Hình Thông Báo Telegram, Zalo & Email (Máy Chủ SMTP)</h1>
                        <p style="color: #94a3b8; font-size: 13px; margin: 6px 0 0 0;">Tự động gửi thông báo qua Telegram, Zalo và Email khi khách đặt phòng hoặc gửi yêu cầu liên hệ trên website.</p>
                    </div>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <span style="display: inline-block; padding: 6px 14px; background: rgba(197, 168, 128, 0.15); border: 1px solid rgba(197, 168, 128, 0.4); border-radius: 30px; color: #e2c499; font-size: 12px; font-weight: 600;">
                            Telegram: 
                            <strong style="color: <?php echo !empty($settings['telegram_enabled']) ? '#22c55e' : '#ef4444'; ?>;">
                                <?php echo !empty($settings['telegram_enabled']) ? '✓ Đang BẬT' : 'Đang tắt'; ?>
                            </strong>
                        </span>

                        <span style="display: inline-block; padding: 6px 14px; background: rgba(197, 168, 128, 0.15); border: 1px solid rgba(197, 168, 128, 0.4); border-radius: 30px; color: #e2c499; font-size: 12px; font-weight: 600;">
                            Zalo: 
                            <strong style="color: #fff;">
                                <?php 
                                if (empty($settings['zalo_enabled'])) {
                                    echo '<span style="color:#ef4444;">Đang tắt</span>';
                                } elseif ($settings['zalo_mode'] === 'simulation') {
                                    echo '<span style="color:#38bdf8;">Đang Mô Phỏng & Ghi Log</span>';
                                } elseif ($settings['zalo_mode'] === 'zns') {
                                    echo '<span style="color:#22c55e;">ZNS Cloud API</span>';
                                } else {
                                    echo '<span style="color:#a855f7;">Webhook Gateway</span>';
                                }
                                ?>
                            </strong>
                        </span>

                        <span style="display: inline-block; padding: 6px 14px; background: rgba(197, 168, 128, 0.15); border: 1px solid rgba(197, 168, 128, 0.4); border-radius: 30px; color: #e2c499; font-size: 12px; font-weight: 600;">
                            Máy chủ SMTP: 
                            <strong style="color: <?php echo !empty($smtp_settings['smtp_enabled']) ? '#22c55e' : '#94a3b8'; ?>;">
                                <?php echo !empty($smtp_settings['smtp_enabled']) ? '✓ Đang BẬT (' . esc_html($smtp_settings['smtp_host']) . ')' : '○ Đang TẮT (PHP Mail)'; ?>
                            </strong>
                        </span>
                    </div>
                </div>
            </div>

            <?php if ($is_saved) : ?>
                <div class="notice notice-success is-dismissible" style="border-radius: 8px; border-left-color: #22c55e;">
                    <p><strong><?php _e('Đã lưu thành công các thiết lập thông báo Telegram, Zalo & Email!', 'dragonlegend-core'); ?></strong></p>
                </div>
            <?php endif; ?>

            <form method="post" action="">
                <?php wp_nonce_field('dl_booking_settings_nonce', 'dl_booking_nonce'); ?>

                <!-- THẺ 1: NGƯỜI NHẬN QUẢN TRỊ & SALE -->
                <div class="postbox" style="border-radius: 12px; overflow: hidden; margin-bottom: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                    <div class="postbox-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 14px 20px;">
                        <h2 style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0;">1. Người Nhận Thông Báo Đặt Phòng & Liên Hệ (Admin & Nhân Viên Sale)</h2>
                    </div>
                    <div class="inside" style="padding: 20px;">
                        <table class="form-table" style="margin: 0;">
                            <tr>
                                <th scope="row" style="width: 250px;">
                                    <label for="admin_zalo_phones"><strong>Số điện thoại Zalo Admin/Sale:</strong></label>
                                </th>
                                <td>
                                    <input type="text" id="admin_zalo_phones" name="admin_zalo_phones" value="<?php echo esc_attr($settings['admin_zalo_phones']); ?>" class="regular-text" style="width: 100%; max-width: 450px;" placeholder="0987654321, 0912345678">
                                    <p class="description">Nhập số điện thoại Zalo của quản trị viên hoặc nhân viên sale được chỉ định tiếp nhận đơn đặt phòng & yêu cầu liên hệ. Có thể nhập nhiều số, ngăn cách bằng dấu phẩy (,).</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="admin_notification_emails"><strong>Email nhận thông báo (Đặt phòng & Liên hệ):</strong></label>
                                </th>
                                <td>
                                    <input type="text" id="admin_notification_emails" name="admin_notification_emails" value="<?php echo esc_attr($settings['admin_notification_emails']); ?>" class="regular-text" style="width: 100%; max-width: 450px;" placeholder="info@dragonlegendhotel.com, sales@dragonlegendhotel.com">
                                    <p class="description">Hộp thư nhận email thông báo khi có khách Đặt phòng, gửi Form Liên hệ hoặc Đăng ký nhận ưu đãi. Có thể nhập nhiều email cách nhau bằng dấu phẩy (,).</p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- THẺ 1B: CẤU HÌNH TELEGRAM BOT (ĐẶT PHÒNG & LIÊN HỆ) -->
                <div class="postbox" style="border-radius: 12px; overflow: hidden; margin-bottom: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); border: 1px solid #bae6fd;">
                    <div class="postbox-header" style="background: linear-gradient(to right, #f0f9ff, #e0f2fe); border-bottom: 1px solid #bae6fd; padding: 14px 20px; display: flex; justify-content: space-between; align-items: center;">
                        <h2 style="font-size: 15px; font-weight: 700; color: #0369a1; margin: 0;">✈️ 2. Cấu Hình Thông Báo Qua Telegram (Đặt Phòng & Liên Hệ)</h2>
                        <span style="font-size: 11px; background: #0284c7; color: #fff; padding: 3px 10px; border-radius: 20px; font-weight: 600;">Miễn phí 100% & Tức thì</span>
                    </div>
                    <div class="inside" style="padding: 20px;">
                        <table class="form-table" style="margin: 0;">
                            <tr>
                                <th scope="row" style="width: 250px;">
                                    <label for="telegram_enabled"><strong>Bật thông báo Telegram:</strong></label>
                                </th>
                                <td>
                                    <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 600;">
                                        <input type="checkbox" id="telegram_enabled" name="telegram_enabled" value="1" <?php checked($settings['telegram_enabled'] ?? '0', '1'); ?>>
                                        <span style="color: #0f172a;">Kích hoạt gửi tin nhắn thông báo qua Telegram Bot</span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label><strong>Sự kiện nhận thông báo:</strong></label>
                                </th>
                                <td>
                                    <div style="display: flex; gap: 24px; flex-wrap: wrap;">
                                        <label style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer;">
                                            <input type="checkbox" name="telegram_notify_booking" value="1" <?php checked($settings['telegram_notify_booking'] ?? '1', '1'); ?>>
                                            <span>🔔 Khi khách <strong>Đặt phòng</strong> trực tuyến</span>
                                        </label>
                                        <label style="display: inline-flex; align-items: center; gap: 6px; cursor: pointer;">
                                            <input type="checkbox" name="telegram_notify_contact" value="1" <?php checked($settings['telegram_notify_contact'] ?? '1', '1'); ?>>
                                            <span>📩 Khi khách gửi <strong>Form Liên hệ & Tư vấn</strong> (/lien-he/)</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="telegram_bot_token"><strong>Telegram Bot Token:</strong></label>
                                </th>
                                <td>
                                    <input type="text" id="telegram_bot_token" name="telegram_bot_token" value="<?php echo esc_attr($settings['telegram_bot_token'] ?? ''); ?>" class="regular-text" style="width: 100%; max-width: 500px; font-family: monospace;" placeholder="Ví dụ: 7123456789:AAHxyz1234567890abcdef...">
                                    <p class="description">Mã API Token của Bot lấy từ <code>@BotFather</code> trên Telegram.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="telegram_chat_id"><strong>Telegram Chat ID / Group ID:</strong></label>
                                </th>
                                <td>
                                    <input type="text" id="telegram_chat_id" name="telegram_chat_id" value="<?php echo esc_attr($settings['telegram_chat_id'] ?? ''); ?>" class="regular-text" style="width: 100%; max-width: 500px; font-family: monospace;" placeholder="Ví dụ: -1001234567890 hoặc 987654321">
                                    <p class="description">ID tài khoản cá nhân hoặc ID nhóm (Group Chat có dấu trừ <code>-</code> phía trước) của bộ phận Lễ tân / Sale. Có thể nhập nhiều ID cách nhau bằng dấu phẩy (,).</p>
                                </td>
                            </tr>
                        </table>

                        <div style="margin-top: 16px; padding: 14px 18px; background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 8px; font-size: 12.5px; color: #0c4a6e;">
                            <strong>💡 Hướng dẫn tạo Bot Telegram trong 1 phút:</strong>
                            <ol style="margin: 6px 0 0 0; padding-left: 18px; line-height: 1.6;">
                                <li>Mở Telegram, tìm <strong>@BotFather</strong> &rarr; Gõ lệnh <code>/newbot</code> &rarr; Đặt tên Bot và copy chuỗi <strong>Bot Token</strong> dán vào ô trên.</li>
                                <li>Thêm Bot vào nhóm Telegram của khách sạn (hoặc nhắn tin <code>/start</code> cho Bot) &rarr; Tìm <strong>@userinfobot</strong> hoặc <strong>@RawDataBot</strong> để lấy <strong>Chat ID</strong> (ví dụ: <code>-100192837465</code>) dán vào ô Chat ID.</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- THẺ 2: CẤU HÌNH ZALO (ZNS / WEBHOOK / SIMULATION) -->
                <div class="postbox" style="border-radius: 12px; overflow: hidden; margin-bottom: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                    <div class="postbox-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 14px 20px;">
                        <h2 style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0;">3. Cấu Hình Kênh Gửi Tin Nhắn Zalo (Đặt Phòng & Liên Hệ)</h2>
                    </div>
                    <div class="inside" style="padding: 20px;">
                        <table class="form-table" style="margin: 0;">
                            <tr>
                                <th scope="row" style="width: 250px;">
                                    <label for="zalo_enabled"><strong>Bật thông báo Zalo:</strong></label>
                                </th>
                                <td>
                                    <div style="display: flex; flex-direction: column; gap: 8px;">
                                        <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer;">
                                            <input type="checkbox" id="zalo_enabled" name="zalo_enabled" value="1" <?php checked($settings['zalo_enabled'], '1'); ?>>
                                            <span>Kích hoạt tự động gửi tin nhắn Zalo khi khách <strong>Đặt phòng</strong> thành công</span>
                                        </label>
                                        <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer;">
                                            <input type="checkbox" id="zalo_notify_contact" name="zalo_notify_contact" value="1" <?php checked($settings['zalo_notify_contact'] ?? '1', '1'); ?>>
                                            <span>Đồng thời gửi tin nhắn Zalo cho Admin/Sale khi khách gửi <strong>Form Liên hệ & Tư vấn</strong> (/lien-he/)</span>
                                        </label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="zalo_mode"><strong>Chế độ hoạt động Zalo:</strong></label>
                                </th>
                                <td>
                                    <select id="zalo_mode" name="zalo_mode" style="min-width: 320px;" onchange="toggleZaloModeFields(this.value)">
                                        <option value="simulation" <?php selected($settings['zalo_mode'], 'simulation'); ?>>🧪 Chế độ Mô phỏng & Ghi Log (Kiểm tra quy trình, không gọi API ngoài)</option>
                                        <option value="zns" <?php selected($settings['zalo_mode'], 'zns'); ?>>⚡ Zalo Notification Service (ZNS Cloud API chính thức)</option>
                                        <option value="webhook" <?php selected($settings['zalo_mode'], 'webhook'); ?>>🔗 Zalo Webhook / Gateway (Gửi qua bot riêng / n8n / Make / Chatbot)</option>
                                    </select>
                                    <p class="description">
                                        <em>Ghi chú: Khi chưa có token Zalo trả phí hoặc đang thử nghiệm giao diện, hãy giữ <strong>Chế độ Mô phỏng</strong> để hệ thống tạo đầy đủ nội dung tin nhắn và lưu log kiểm tra mà không bị lỗi mạng.</em>
                                    </p>
                                </td>
                            </tr>
                        </table>

                        <!-- PHẦN ZNS CLOUD -->
                        <div id="zalo_zns_section" style="margin-top: 15px; padding: 18px 20px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; display: <?php echo ($settings['zalo_mode'] === 'zns') ? 'block' : 'none'; ?>;">
                            <h4 style="margin: 0 0 12px 0; color: #166534; font-size: 14px;">Thông số Zalo Cloud & Official Account (ZNS)</h4>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <div>
                                    <label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Zalo App ID:</label>
                                    <input type="text" name="zalo_app_id" value="<?php echo esc_attr($settings['zalo_app_id']); ?>" class="widefat" placeholder="Ví dụ: 1234567890123456789">
                                </div>
                                <div>
                                    <label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Secret Key:</label>
                                    <input type="password" name="zalo_secret_key" value="<?php echo esc_attr($settings['zalo_secret_key']); ?>" class="widefat">
                                </div>
                                <div>
                                    <label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Template ID Khách hàng:</label>
                                    <input type="text" name="zns_template_customer" value="<?php echo esc_attr($settings['zns_template_customer']); ?>" class="widefat" placeholder="Ví dụ: 324892">
                                </div>
                                <div>
                                    <label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Template ID Admin/Sale:</label>
                                    <input type="text" name="zns_template_admin" value="<?php echo esc_attr($settings['zns_template_admin']); ?>" class="widefat" placeholder="Ví dụ: 324893">
                                </div>
                                <div style="grid-column: 1 / -1;">
                                    <label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Access Token (Zalo OA):</label>
                                    <textarea name="zalo_access_token" rows="2" class="widefat" placeholder="Dán Access Token Zalo OA vào đây..."><?php echo esc_textarea($settings['zalo_access_token']); ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- PHẦN WEBHOOK GATEWAY -->
                        <div id="zalo_webhook_section" style="margin-top: 15px; padding: 18px 20px; background: #faf5ff; border: 1px solid #e9d5ff; border-radius: 10px; display: <?php echo ($settings['zalo_mode'] === 'webhook') ? 'block' : 'none'; ?>;">
                            <h4 style="margin: 0 0 12px 0; color: #6b21a8; font-size: 14px;">Cấu hình Webhook Gateway (Zalo Bot / n8n / Make / Server Riêng)</h4>
                            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 15px;">
                                <div>
                                    <label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Webhook Endpoint URL:</label>
                                    <input type="url" name="webhook_url" value="<?php echo esc_attr($settings['webhook_url']); ?>" class="widefat" placeholder="https://webhook.yourserver.com/zalo-booking">
                                </div>
                                <div>
                                    <label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Secret / Token (Nếu có):</label>
                                    <input type="text" name="webhook_secret" value="<?php echo esc_attr($settings['webhook_secret']); ?>" class="widefat" placeholder="Bearer secret token">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- THẺ 3: MẪU TIN NHẮN ZALO -->
                <div class="postbox" style="border-radius: 12px; overflow: hidden; margin-bottom: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                    <div class="postbox-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 14px 20px;">
                        <h2 style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0;">4. Nội Dung Mẫu Tin Nhắn Zalo (Đặt Phòng & Liên Hệ)</h2>
                    </div>
                    <div class="inside" style="padding: 20px;">
                        <p style="font-size: 13px; color: #475569; margin-top: 0;">
                            Các biến có thể chèn tự động: <br>
                            <code>{guest_name}</code>, <code>{guest_phone}</code>, <code>{guest_email}</code>, <code>{booking_code}</code>, <code>{room_name}</code>, <code>{checkin}</code>, <code>{checkout}</code>, <code>{nights}</code>, <code>{rooms}</code>, <code>{adults}</code>, <code>{children}</code>, <code>{grand_total}</code>, <code>{guest_note}</code>, <code>{hotel_hotline}</code>, <code>{admin_link}</code>.
                        </p>

                        <div style="margin-bottom: 18px;">
                            <label for="zalo_customer_msg" style="display:block; font-weight:700; margin-bottom:6px; color:#1e293b;">
                                Tin nhắn gửi cho Khách hàng (khi Đặt phòng):
                            </label>
                            <textarea id="zalo_customer_msg" name="zalo_customer_msg" rows="3" class="widefat" style="border-radius: 8px; font-family: inherit; font-size: 13px;"><?php echo esc_textarea($settings['zalo_customer_msg']); ?></textarea>
                        </div>

                        <div style="margin-bottom: 18px;">
                            <label for="zalo_admin_msg" style="display:block; font-weight:700; margin-bottom:6px; color:#1e293b;">
                                Tin nhắn gửi cho Quản trị viên / Nhân viên Sale (khi có Đơn đặt phòng mới):
                            </label>
                            <textarea id="zalo_admin_msg" name="zalo_admin_msg" rows="3" class="widefat" style="border-radius: 8px; font-family: inherit; font-size: 13px;"><?php echo esc_textarea($settings['zalo_admin_msg']); ?></textarea>
                        </div>

                        <div>
                            <label for="zalo_contact_admin_msg" style="display:block; font-weight:700; margin-bottom:6px; color:#1e293b;">
                                Tin nhắn gửi cho Quản trị viên / Nhân viên Sale (khi khách gửi Form Liên hệ /lien-he/):
                            </label>
                            <textarea id="zalo_contact_admin_msg" name="zalo_contact_admin_msg" rows="3" class="widefat" style="border-radius: 8px; font-family: inherit; font-size: 13px;"><?php echo esc_textarea($settings['zalo_contact_admin_msg'] ?? "📩 [LIÊN HỆ MỚI TỪ WEBSITE] Khách hàng: {guest_name} ({guest_phone}) | Email: {guest_email} | Chủ đề: {subject} | Nội dung: {message}"); ?></textarea>
                            <p class="description">Biến hỗ trợ cho Form Liên hệ: <code>{guest_name}</code>, <code>{guest_phone}</code>, <code>{guest_email}</code>, <code>{subject}</code>, <code>{message}</code>.</p>
                        </div>
                    </div>
                </div>

                <!-- THẺ 4: CẤU HÌNH EMAIL QUẢN TRỊ -->
                <div class="postbox" style="border-radius: 12px; overflow: hidden; margin-bottom: 25px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                    <div class="postbox-header" style="background: #f8fafc; border-bottom: 1px solid #e2e8f0; padding: 14px 20px;">
                        <h2 style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0;">4. Cấu Hình Email Thông Báo Đặt Phòng</h2>
                    </div>
                    <div class="inside" style="padding: 20px;">
                        <table class="form-table" style="margin: 0;">
                            <tr>
                                <th scope="row" style="width: 250px;">
                                    <label for="email_enabled"><strong>Bật thông báo Email:</strong></label>
                                </th>
                                <td>
                                    <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer;">
                                        <input type="checkbox" id="email_enabled" name="email_enabled" value="1" <?php checked($settings['email_enabled'], '1'); ?>>
                                        <span>Gửi email HTML thương hiệu Dragon Legend Hotel cho quản trị viên khi có đơn mới</span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="customer_email_enabled"><strong>Gửi email cho khách hàng:</strong></label>
                                </th>
                                <td>
                                    <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer;">
                                        <input type="checkbox" id="customer_email_enabled" name="customer_email_enabled" value="1" <?php checked($settings['customer_email_enabled'], '1'); ?>>
                                        <span>Gửi thư xác nhận tiếp nhận đơn đặt phòng tới email của khách (nếu khách có điền email)</span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="email_subject_template"><strong>Tiêu đề thư cho quản trị:</strong></label>
                                </th>
                                <td>
                                    <input type="text" id="email_subject_template" name="email_subject_template" value="<?php echo esc_attr($settings['email_subject_template']); ?>" class="regular-text" style="width: 100%; max-width: 550px;">
                                    <p class="description">Hỗ trợ các biến <code>{booking_code}</code>, <code>{guest_name}</code>, <code>{room_name}</code>...</p>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- THẺ 5: CẤU HÌNH MÁY CHỦ MAIL (SMTP SERVER) -->
                <div class="postbox" style="border-radius: 12px; overflow: hidden; margin-bottom: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.06); border: 1px solid #cbd5e1;">
                    <div class="postbox-header" style="background: linear-gradient(to right, #f8fafc, #f1f5f9); border-bottom: 1px solid #cbd5e1; padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="font-size: 20px;">📧</span>
                            <h2 style="font-size: 16px; font-weight: 700; color: #07111e; margin: 0;">5. Cấu Hình Máy Chủ Gửi Thư (SMTP Mail Server)</h2>
                        </div>
                        <span style="font-size: 12px; background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 20px; font-weight: 600;">
                            Đảm bảo thư gửi đi 100% vào Inbox, không vào Spam
                        </span>
                    </div>
                    <div class="inside" style="padding: 22px;">
                        
                        <!-- Presets cấu hình nhanh -->
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px 18px; margin-bottom: 22px; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px;">
                            <span style="font-size: 13px; font-weight: 700; color: #334155;">⚡ Cài đặt nhanh theo nhà cung cấp (Presets):</span>
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                <button type="button" class="button" onclick="applySmtpPreset('gmail')" style="font-weight: 600;">🔴 Gmail / G-Suite</button>
                                <button type="button" class="button" onclick="applySmtpPreset('brevo')" style="font-weight: 600;">🔵 Brevo (Sendinblue)</button>
                                <button type="button" class="button" onclick="applySmtpPreset('mailgun')" style="font-weight: 600;">🟢 Mailgun</button>
                                <button type="button" class="button" onclick="applySmtpPreset('sendgrid')" style="font-weight: 600;">🟣 SendGrid</button>
                                <button type="button" class="button" onclick="applySmtpPreset('custom')" style="font-weight: 600;">⚪ Webmail tên miền riêng</button>
                            </div>
                        </div>

                        <table class="form-table" style="margin: 0;">
                            <tr>
                                <th scope="row" style="width: 250px;">
                                    <label for="smtp_enabled"><strong>Kích hoạt SMTP:</strong></label>
                                </th>
                                <td>
                                    <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 600;">
                                        <input type="checkbox" id="smtp_enabled" name="smtp_enabled" value="1" <?php checked(!empty($smtp_settings['smtp_enabled']), true); ?>>
                                        <span style="color: #0f172a;">Bật gửi toàn bộ email của website qua máy chủ SMTP này (Khuyên dùng)</span>
                                    </label>
                                    <p class="description">Khi bật, toàn bộ thư xác nhận đặt phòng, thư cho admin, đặt lại mật khẩu của WordPress sẽ gửi qua SMTP.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="smtp_host"><strong>Máy chủ SMTP (Host):</strong></label>
                                </th>
                                <td>
                                    <input type="text" id="smtp_host" name="smtp_host" value="<?php echo esc_attr($smtp_settings['smtp_host'] ?? 'smtp.gmail.com'); ?>" class="regular-text" style="width: 100%; max-width: 450px;" placeholder="Ví dụ: smtp.gmail.com hoặc smtp-relay.brevo.com">
                                    <p class="description">Địa chỉ IP hoặc tên miền máy chủ thư SMTP của nhà cung cấp.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="smtp_port"><strong>Cổng kết nối (Port):</strong></label>
                                </th>
                                <td>
                                    <input type="number" id="smtp_port" name="smtp_port" value="<?php echo esc_attr($smtp_settings['smtp_port'] ?? '465'); ?>" style="width: 120px; font-weight: 600;">
                                    <span style="margin-left: 10px; color: #64748b; font-size: 13px;">(Khuyên dùng: <code>465</code> cho SSL, <code>587</code> cho TLS)</span>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="smtp_encryption"><strong>Giao thức mã hóa:</strong></label>
                                </th>
                                <td>
                                    <select id="smtp_encryption" name="smtp_encryption" style="min-width: 250px; font-weight: 600;">
                                        <option value="ssl" <?php selected(($smtp_settings['smtp_encryption'] ?? 'ssl'), 'ssl'); ?>>SSL (Khuyên dùng với Cổng 465)</option>
                                        <option value="tls" <?php selected(($smtp_settings['smtp_encryption'] ?? ''), 'tls'); ?>>TLS / STARTTLS (Khuyên dùng với Cổng 587)</option>
                                        <option value="none" <?php selected(($smtp_settings['smtp_encryption'] ?? ''), 'none'); ?>>Không mã hóa (Cổng 25 - Không khuyên dùng)</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="smtp_auth"><strong>Xác thực tài khoản (Auth):</strong></label>
                                </th>
                                <td>
                                    <label style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer;">
                                        <input type="checkbox" id="smtp_auth" name="smtp_auth" value="yes" <?php checked(($smtp_settings['smtp_auth'] ?? 'yes'), 'yes'); ?>>
                                        <span>Yêu cầu xác thực tài khoản (Hầu hết máy chủ SMTP đều yêu cầu)</span>
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="smtp_username"><strong>Tên đăng nhập SMTP (User):</strong></label>
                                </th>
                                <td>
                                    <input type="text" id="smtp_username" name="smtp_username" value="<?php echo esc_attr($smtp_settings['smtp_username'] ?? ''); ?>" class="regular-text" style="width: 100%; max-width: 450px;" placeholder="Ví dụ: your-email@gmail.com hoặc user@dragonlegendhotel.com">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="smtp_password"><strong>Mật khẩu SMTP (Password):</strong></label>
                                </th>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 8px; max-width: 450px;">
                                        <input type="password" id="smtp_password" name="smtp_password" value="<?php echo esc_attr($smtp_settings['smtp_password'] ?? ''); ?>" class="regular-text" style="width: 100%;" placeholder="Nhập Mật khẩu ứng dụng 16 ký tự hoặc API Key">
                                        <button type="button" class="button" onclick="toggleSmtpPassword()" style="white-space: nowrap;">👁️ Hiện/Ẩn</button>
                                    </div>
                                    <p class="description">
                                        Nếu dùng Gmail cá nhân: Bạn phải dùng <strong>Mật khẩu ứng dụng (App Password)</strong> 16 chữ cái (không dùng mật khẩu đăng nhập Gmail thông thường).
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="smtp_from_email"><strong>Email người gửi mặc định (From):</strong></label>
                                </th>
                                <td>
                                    <input type="email" id="smtp_from_email" name="smtp_from_email" value="<?php echo esc_attr($smtp_settings['smtp_from_email'] ?? get_option('admin_email')); ?>" class="regular-text" style="width: 100%; max-width: 450px;" placeholder="reservation@dragonlegendhotel.com">
                                    <p class="description">Email xuất hiện ở mục "Người gửi" khi khách hàng hoặc admin nhận thư. (Nên trùng với email của tài khoản SMTP).</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label for="smtp_from_name"><strong>Tên người gửi hiển thị:</strong></label>
                                </th>
                                <td>
                                    <input type="text" id="smtp_from_name" name="smtp_from_name" value="<?php echo esc_attr($smtp_settings['smtp_from_name'] ?? 'Dragon Legend Hotel'); ?>" class="regular-text" style="width: 100%; max-width: 450px;">
                                </td>
                            </tr>
                        </table>

                        <!-- Hộp hướng dẫn nhanh Gmail & Brevo -->
                        <div style="margin-top: 20px; padding: 15px 20px; background: #fffbeb; border: 1px solid #fef3c7; border-radius: 8px; font-size: 13px; color: #92400e;">
                            <strong style="color: #b45309; display: flex; align-items: center; gap: 6px; margin-bottom: 6px;">
                                💡 Hướng dẫn lấy thông số nhanh cho người quản trị:
                            </strong>
                            <ul style="margin: 0; padding-left: 20px; line-height: 1.6;">
                                <li><strong>Cách dùng Gmail miễn phí:</strong> Vào <a href="https://myaccount.google.com/security" target="_blank" style="color:#b45309; text-decoration:underline;">Bảo mật tài khoản Google</a> &rarr; Bật <em>Xác minh 2 bước</em> &rarr; Vào mục <em>Mật khẩu ứng dụng</em> &rarr; Đặt tên "Dragon Legend Hotel" và copy mã 16 ký tự dán vào ô Mật khẩu SMTP ở trên.</li>
                                <li><strong>Cách dùng Brevo (Sendinblue) chuyên nghiệp (Miễn phí 300 thư/ngày):</strong> Đăng ký miễn phí tại <a href="https://www.brevo.com" target="_blank" style="color:#b45309; text-decoration:underline;">Brevo.com</a> &rarr; Vào mục <em>SMTP & API</em> &rarr; Tạo Master API Key dán vào mật khẩu với Host là <code>smtp-relay.brevo.com</code>, cổng <code>587</code> TLS.</li>
                            </ul>
                        </div>

                    </div>
                </div>

                <div style="margin-bottom: 30px;">
                    <button type="submit" name="dl_save_booking_settings" class="button button-primary button-large" style="padding: 8px 30px; font-weight: 700; background: #07111e; border-color: #07111e; font-size: 14px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.15);">
                        💾 Lưu Cấu Hình Toàn Bộ Hệ Thống
                    </button>
                </div>
            </form>

            <!-- THẺ 6: CÔNG CỤ THỬ NGHIỆM GỬI TIN & EMAIL -->
            <div class="postbox" style="border-radius: 12px; overflow: hidden; margin-bottom: 40px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); border: 1px solid #cbd5e1;">
                <div class="postbox-header" style="background: #f1f5f9; border-bottom: 1px solid #cbd5e1; padding: 14px 20px;">
                    <h2 style="font-size: 15px; font-weight: 700; color: #0f172a; margin: 0;">🛠️ Công Cụ Thử Nghiệm Kết Nối (Gửi Thử Telegram, Zalo & Email Trực Tiếp)</h2>
                </div>
                <div class="inside" style="padding: 22px;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
                        <!-- Test Telegram -->
                        <div style="background: #f0f9ff; border: 1px solid #bae6fd; border-radius: 10px; padding: 18px;">
                            <h3 style="margin: 0 0 10px 0; font-size: 14px; color: #0369a1; font-weight: 700;">✈️ Gửi thử tin nhắn Telegram</h3>
                            <p style="font-size: 12px; color: #475569; margin-top: 0;">Kiểm tra kết nối Bot Token & Chat ID vừa nhập phía trên (không cần lưu trước vẫn test được).</p>
                            <div style="margin-bottom: 10px;">
                                <label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Nội dung thử nghiệm:</label>
                                <input type="text" id="test_telegram_msg" value="" class="widefat" placeholder="Để trống sẽ gửi mẫu kiểm tra kết nối mặc định">
                            </div>
                            <button type="button" onclick="runTelegramTest()" id="btn_run_test_telegram" class="button button-secondary" style="font-weight: 600; border-radius: 6px; border-color: #0284c7; color: #0284c7;">
                                ✈️ Bấm Gửi Thử Telegram
                            </button>
                            <div id="test_telegram_result" style="margin-top: 10px; font-size: 12px; display: none;"></div>
                        </div>

                        <!-- Test Zalo -->
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px;">
                            <h3 style="margin: 0 0 10px 0; font-size: 14px; color: #0f172a; font-weight: 700;">📲 Gửi thử tin nhắn Zalo</h3>
                            <p style="font-size: 12px; color: #64748b; margin-top: 0;">Kiểm tra xem tin nhắn có gửi được tới số điện thoại thử nghiệm không theo chế độ đang chọn.</p>
                            <div style="margin-bottom: 10px;">
                                <label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">SĐT nhận thử nghiệm:</label>
                                <input type="text" id="test_zalo_phone" value="<?php echo esc_attr(explode(',', $settings['admin_zalo_phones'])[0] ?? '0987654321'); ?>" class="widefat" placeholder="0987 654 321">
                            </div>
                            <div style="margin-bottom: 12px;">
                                <label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Nội dung thử nghiệm:</label>
                                <input type="text" id="test_zalo_msg" value="Test thông báo đặt phòng từ Dragon Legend Hotel!" class="widefat">
                            </div>
                            <button type="button" onclick="runZaloTest()" id="btn_run_test_zalo" class="button button-secondary" style="font-weight: 600; border-radius: 6px;">
                                📲 Bấm Gửi Thử Zalo
                            </button>
                            <div id="test_zalo_result" style="margin-top: 10px; font-size: 12px; display: none;"></div>
                        </div>

                        <!-- Test Email -->
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 18px;">
                            <h3 style="margin: 0 0 10px 0; font-size: 14px; color: #0f172a; font-weight: 700;">✉️ Gửi thử Email Kiểm Tra Máy Chủ SMTP</h3>
                            <p style="font-size: 12px; color: #64748b; margin-top: 0;">Gửi mẫu thư kiểm tra kết nối để xác nhận máy chủ SMTP hoạt động hoàn hảo.</p>
                            <div style="margin-bottom: 10px;">
                                <label style="display:block; font-size:12px; font-weight:600; margin-bottom:4px;">Email nhận thử nghiệm:</label>
                                <input type="email" id="test_email_addr" value="<?php echo esc_attr(explode(',', $settings['admin_notification_emails'])[0] ?? get_option('admin_email')); ?>" class="widefat" placeholder="admin@hotel.com">
                            </div>
                            <div style="margin-bottom: 12px; font-size: 12px; color: #64748b;">
                                <em>Sau khi cấu hình thông số SMTP và bấm Lưu ở trên, hãy bấm nút dưới để test kết nối.</em>
                            </div>
                            <button type="button" onclick="runEmailTest()" id="btn_run_test_email" class="button button-secondary" style="font-weight: 600; border-radius: 6px;">
                                ✉️ Bấm Gửi Thử Email Kiểm Tra
                            </button>
                            <div id="test_email_result" style="margin-top: 10px; font-size: 12px; display: none;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
        function toggleZaloModeFields(mode) {
            const zns = document.getElementById('zalo_zns_section');
            const webhook = document.getElementById('zalo_webhook_section');
            if (zns) zns.style.display = (mode === 'zns') ? 'block' : 'none';
            if (webhook) webhook.style.display = (mode === 'webhook') ? 'block' : 'none';
        }

        function toggleSmtpPassword() {
            const pass = document.getElementById('smtp_password');
            if (pass) {
                pass.type = (pass.type === 'password') ? 'text' : 'password';
            }
        }

        function applySmtpPreset(type) {
            const host = document.getElementById('smtp_host');
            const port = document.getElementById('smtp_port');
            const enc  = document.getElementById('smtp_encryption');
            const auth = document.getElementById('smtp_auth');
            const user = document.getElementById('smtp_username');

            if (auth) auth.checked = true;

            if (type === 'gmail') {
                if (host) host.value = 'smtp.gmail.com';
                if (port) port.value = '465';
                if (enc) enc.value = 'ssl';
                if (user && !user.value) user.placeholder = 'your-email@gmail.com';
            } else if (type === 'brevo') {
                if (host) host.value = 'smtp-relay.brevo.com';
                if (port) port.value = '587';
                if (enc) enc.value = 'tls';
                if (user && !user.value) user.placeholder = 'your-login-email@example.com';
            } else if (type === 'mailgun') {
                if (host) host.value = 'smtp.mailgun.org';
                if (port) port.value = '587';
                if (enc) enc.value = 'tls';
                if (user && !user.value) user.placeholder = 'postmaster@your-domain.mailgun.org';
            } else if (type === 'sendgrid') {
                if (host) host.value = 'smtp.sendgrid.net';
                if (port) port.value = '587';
                if (enc) enc.value = 'tls';
                if (user) user.value = 'apikey';
            } else if (type === 'custom') {
                if (host) host.value = 'mail.dragonlegendhotel.com';
                if (port) port.value = '465';
                if (enc) enc.value = 'ssl';
                if (user && !user.value) user.placeholder = 'info@dragonlegendhotel.com';
            }
        }

        function runTelegramTest() {
            const botToken = document.getElementById('telegram_bot_token') ? document.getElementById('telegram_bot_token').value : '';
            const chatId = document.getElementById('telegram_chat_id') ? document.getElementById('telegram_chat_id').value : '';
            const message = document.getElementById('test_telegram_msg').value;
            const btn = document.getElementById('btn_run_test_telegram');
            const res = document.getElementById('test_telegram_result');

            btn.disabled = true;
            btn.innerText = 'Đang gửi Telegram...';
            res.style.display = 'block';
            res.innerHTML = '<span style="color:#64748b;">⏳ Đang kết nối Telegram API...</span>';

            const data = new FormData();
            data.append('action', 'dl_test_telegram_notification');
            data.append('nonce', '<?php echo esc_js($test_nonce); ?>');
            data.append('bot_token', botToken);
            data.append('chat_id', chatId);
            data.append('message', message);

            fetch(ajaxurl, { method: 'POST', body: data })
                .then(r => r.json())
                .then(resp => {
                    if (resp.success) {
                        res.innerHTML = '<div style="padding:10px; background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; border-radius:6px;"><strong>✓ Thành công:</strong> ' + resp.data.message + '</div>';
                    } else {
                        res.innerHTML = '<div style="padding:10px; background:#fef2f2; border:1px solid #fecaca; color:#991b1b; border-radius:6px;"><strong>✕ Lỗi:</strong> ' + resp.data.message + '</div>';
                    }
                })
                .catch(err => {
                    res.innerHTML = '<div style="padding:10px; background:#fef2f2; border:1px solid #fecaca; color:#991b1b; border-radius:6px;"><strong>✕ Lỗi kết nối:</strong> ' + err.message + '</div>';
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerText = '✈️ Bấm Gửi Thử Telegram';
                });
        }

        function runZaloTest() {
            const phone = document.getElementById('test_zalo_phone').value;
            const message = document.getElementById('test_zalo_msg').value;
            const btn = document.getElementById('btn_run_test_zalo');
            const res = document.getElementById('test_zalo_result');

            btn.disabled = true;
            btn.innerText = 'Đang gửi test...';
            res.style.display = 'block';
            res.innerHTML = '<span style="color:#64748b;">⏳ Đang thực hiện kết nối Zalo...</span>';

            const data = new FormData();
            data.append('action', 'dl_test_zalo_notification');
            data.append('nonce', '<?php echo esc_js($test_nonce); ?>');
            data.append('phone', phone);
            data.append('message', message);

            fetch(ajaxurl, { method: 'POST', body: data })
                .then(r => r.json())
                .then(resp => {
                    if (resp.success) {
                        res.innerHTML = '<div style="padding:10px; background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; border-radius:6px;"><strong>✓ Thành công:</strong> ' + resp.data.message + '</div>';
                    } else {
                        res.innerHTML = '<div style="padding:10px; background:#fef2f2; border:1px solid #fecaca; color:#991b1b; border-radius:6px;"><strong>✕ Lỗi:</strong> ' + resp.data.message + '</div>';
                    }
                })
                .catch(err => {
                    res.innerHTML = '<div style="padding:10px; background:#fef2f2; border:1px solid #fecaca; color:#991b1b; border-radius:6px;"><strong>✕ Lỗi kết nối:</strong> ' + err.message + '</div>';
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerText = '📲 Bấm Gửi Thử Zalo';
                });
        }

        function runEmailTest() {
            const email = document.getElementById('test_email_addr').value;
            const btn = document.getElementById('btn_run_test_email');
            const res = document.getElementById('test_email_result');

            btn.disabled = true;
            btn.innerText = 'Đang kiểm tra kết nối SMTP...';
            res.style.display = 'block';
            res.innerHTML = '<span style="color:#64748b;">⏳ Đang bắt tay và kết nối máy chủ mail...</span>';

            const data = new FormData();
            data.append('action', 'dl_test_email_notification');
            data.append('nonce', '<?php echo esc_js($test_nonce); ?>');
            data.append('email', email);

            fetch(ajaxurl, { method: 'POST', body: data })
                .then(r => r.json())
                .then(resp => {
                    let debugHtml = '';
                    if (resp.data && resp.data.debug_log) {
                        debugHtml = '<details style="margin-top:8px;"><summary style="cursor:pointer; font-weight:bold; color:#475569;">📋 Xem nhật ký gỡ lỗi kết nối SMTP (Debug Transcript)</summary><pre style="background:#0f172a; color:#38bdf8; padding:12px; border-radius:6px; font-size:11px; max-height:220px; overflow-y:auto; margin-top:6px; white-space:pre-wrap;">' + resp.data.debug_log + '</pre></details>';
                    }

                    if (resp.success) {
                        res.innerHTML = '<div style="padding:12px; background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; border-radius:6px;"><strong>✓ Thành công:</strong> ' + resp.data.message + debugHtml + '</div>';
                    } else {
                        res.innerHTML = '<div style="padding:12px; background:#fef2f2; border:1px solid #fecaca; color:#991b1b; border-radius:6px;"><strong>✕ Lỗi kết nối / gửi thư:</strong> ' + resp.data.message + debugHtml + '</div>';
                    }
                })
                .catch(err => {
                    res.innerHTML = '<div style="padding:12px; background:#fef2f2; border:1px solid #fecaca; color:#991b1b; border-radius:6px;"><strong>✕ Lỗi kết nối:</strong> ' + err.message + '</div>';
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerText = '✉️ Bấm Gửi Thử Email Kiểm Tra';
                });
        }
        </script>
        <?php
    }
}
