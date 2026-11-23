<?php
/**
 * Dragon Legend Hotel Core - Newsletter & Promo Subscription Handler
 * Tiếp nhận đăng ký nhận ưu đãi, lưu trữ khách hàng, tự động cấp mã giảm giá 10%,
 * xuất file Excel/CSV và cung cấp công cụ Gửi Email Khuyến Mãi Hàng Loạt (Broadcast Campaign)
 *
 * @package DragonLegendCore
 */

if (!defined('ABSPATH')) {
    exit;
}

class DragonLegend_Newsletter_Handler {

    const DEFAULT_COUPON = 'DRAGON2026';

    public static function init() {
        // AJAX Đăng ký nhận ưu đãi ngoài frontend
        add_action('wp_ajax_dl_subscribe_newsletter', array(__CLASS__, 'handle_subscribe'));
        add_action('wp_ajax_nopriv_dl_subscribe_newsletter', array(__CLASS__, 'handle_subscribe'));

        // Đăng ký menu Quản trị
        add_action('admin_menu', array(__CLASS__, 'register_admin_menu'));

        // Xử lý xuất file CSV
        add_action('admin_init', array(__CLASS__, 'handle_export_csv'));

        // AJAX Gửi email thử nghiệm
        add_action('wp_ajax_dl_send_test_broadcast', array(__CLASS__, 'handle_test_broadcast'));

        // AJAX Gửi email hàng loạt theo đợt (batch)
        add_action('wp_ajax_dl_send_broadcast_batch', array(__CLASS__, 'handle_broadcast_batch'));
    }

    /**
     * Đăng ký trang quản trị Gửi Email Hàng Loạt
     */
    public static function register_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=dl_booking',
            __('Gửi Email Khuyến Mãi Hàng Loạt', 'dragonlegend-core'),
            __('Gửi Mail Hàng Loạt', 'dragonlegend-core'),
            'manage_options',
            'dl-broadcast-email',
            array(__CLASS__, 'render_broadcast_page')
        );
    }

    /**
     * Xử lý AJAX Tiếp nhận đăng ký nhận ưu đãi từ Frontend
     */
    public static function handle_subscribe() {
        if (isset($_POST['nonce'])) {
            check_ajax_referer('dragonlegend_nonce', 'nonce');
        }

        $email = isset($_POST['email']) ? sanitize_email(trim($_POST['email'])) : '';

        if (empty($email) || !is_email($email)) {
            wp_send_json_error(array(
                'message' => __('Vui lòng nhập địa chỉ email hợp lệ (ví dụ: email@domain.com).', 'dragonlegend-core')
            ));
        }

        $welcome_coupon = class_exists('DragonLegend_Coupons') 
            ? DragonLegend_Coupons::get_welcome_coupon() 
            : array('code' => self::DEFAULT_COUPON, 'formatted_discount' => '10%');
        $coupon_code = $welcome_coupon['code'] ?? self::DEFAULT_COUPON;
        $discount_desc = $welcome_coupon['formatted_discount'] ?? '10%';

        $existing = get_posts(array(
            'post_type'      => 'dl_subscriber',
            'post_status'    => 'any',
            'title'          => $email,
            'posts_per_page' => 1,
            'fields'         => 'ids',
        ));

        if (!empty($existing)) {
            wp_send_json_success(array(
                'already_subscribed' => true,
                'coupon'             => $coupon_code,
                'message'            => sprintf(
                    __('Email này đã được đăng ký trước đó. Mã ưu đãi của bạn là <strong class="text-amber-300 font-mono tracking-wider">%s</strong> (%s).', 'dragonlegend-core'),
                    $coupon_code,
                    $discount_desc
                )
            ));
        }

        $post_id = wp_insert_post(array(
            'post_title'  => $email,
            'post_type'   => 'dl_subscriber',
            'post_status' => 'publish',
        ));

        if (is_wp_error($post_id)) {
            wp_send_json_error(array(
                'message' => __('Có lỗi xảy ra khi lưu thông tin. Vui lòng thử lại sau ít phút.', 'dragonlegend-core')
            ));
        }

        $client_ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : '';
        update_post_meta($post_id, '_subscriber_ip', $client_ip);
        update_post_meta($post_id, '_subscriber_date', current_time('mysql'));
        update_post_meta($post_id, '_coupon_code', $coupon_code);

        $mail_sent = self::send_welcome_email($email, $coupon_code);
        if ($mail_sent) {
            update_post_meta($post_id, '_welcome_email_sent', '1');
        } else {
            update_post_meta($post_id, '_welcome_email_sent', '0');
        }

        self::notify_admin_new_subscriber($email, $client_ip);

        wp_send_json_success(array(
            'coupon'  => $coupon_code,
            'message' => sprintf(
                __('Đăng ký thành công! Mã ưu đãi của bạn là <strong class="text-amber-300 font-mono tracking-wider">%s</strong> (%s). Chúng tôi cũng đã gửi chi tiết vào email của bạn.', 'dragonlegend-core'),
                $coupon_code,
                $discount_desc
            )
        ));
    }

    /**
     * Gửi email HTML chứa mã ưu đãi cho khách hàng vừa đăng ký
     */
    public static function send_welcome_email($to_email, $coupon_code) {
        $site_name = get_bloginfo('name');
        $subject   = '[' . $site_name . '] Mã Ưu Đãi Độc Quyền Dành Cho Bạn - ' . $coupon_code;

        $coupon_info = class_exists('DragonLegend_Coupons') 
            ? DragonLegend_Coupons::verify_coupon($coupon_code, 1000000) 
            : array('formatted_discount' => '10%');
        $discount_label = $coupon_info['formatted_discount'] ?? '10%';

        $intro = 'Cảm ơn Quý khách đã quan tâm và đăng ký nhận thông tin ưu đãi từ ' . $site_name . '.';
        $content = '<p>Chúng tôi trân trọng gửi tặng Quý khách mã khuyến mãi độc quyền giảm ngay <strong style="color:#fbbf24;">' . esc_html($discount_label) . '</strong> khi đặt phòng trực tiếp trên website của khách sạn:</p>';

        $message = self::build_broadcast_email_html($subject, $intro, $content, $coupon_code, 'Đặt Phòng Ngay', home_url('/'));
        $headers = array('Content-Type: text/html; charset=UTF-8');

        return wp_mail($to_email, $subject, $message, $headers);
    }

    /**
     * Gửi email thông báo cho Ban Quản Trị khách sạn khi có khách mới đăng ký
     */
    public static function notify_admin_new_subscriber($subscriber_email, $client_ip) {
        $admin_emails = array();
        if (class_exists('DragonLegend_Zalo_Service')) {
            $settings = DragonLegend_Zalo_Service::get_settings();
            if (!empty($settings['admin_notification_emails'])) {
                $emails = explode(',', $settings['admin_notification_emails']);
                foreach ($emails as $em) {
                    $em = trim($em);
                    if (is_email($em)) {
                        $admin_emails[] = $em;
                    }
                }
            }
        }

        if (empty($admin_emails)) {
            $admin_emails[] = get_option('admin_email');
        }

        $welcome_coupon = class_exists('DragonLegend_Coupons') 
            ? DragonLegend_Coupons::get_welcome_coupon() 
            : array('code' => self::DEFAULT_COUPON, 'formatted_discount' => '10%');
        $c_code = $welcome_coupon['code'] ?? self::DEFAULT_COUPON;
        $c_disc = $welcome_coupon['formatted_discount'] ?? '10%';

        $subject = '[Dragon Legend] Khách hàng mới đăng ký nhận ưu đãi: ' . $subscriber_email;
        $admin_url = admin_url('edit.php?post_type=dl_subscriber');

        $message = '<!DOCTYPE html>
<html lang="vi">
<head><meta charset="utf-8"></head>
<body style="font-family:Arial, sans-serif; background:#f4f6f8; padding:20px; color:#333;">
  <div style="max-width:550px; margin:0 auto; background:#fff; border-radius:8px; padding:25px; border:1px solid #ddd;">
    <h2 style="color:#07111e; margin-top:0;">Đăng Ký Nhận Bản Tin Ưu Đãi Mới</h2>
    <p>Hệ thống website vừa ghi nhận một khách hàng đăng ký nhận ưu đãi:</p>
    <table style="width:100%; border-collapse:collapse; margin:20px 0;">
      <tr>
        <td style="padding:8px; border-bottom:1px solid #eee; font-weight:bold; width:140px;">Email khách:</td>
        <td style="padding:8px; border-bottom:1px solid #eee; color:#1d4ed8; font-weight:bold;">' . esc_html($subscriber_email) . '</td>
      </tr>
      <tr>
        <td style="padding:8px; border-bottom:1px solid #eee; font-weight:bold;">Thời gian:</td>
        <td style="padding:8px; border-bottom:1px solid #eee;">' . esc_html(current_time('H:i - d/m/Y')) . '</td>
      </tr>
      <tr>
        <td style="padding:8px; border-bottom:1px solid #eee; font-weight:bold;">Mã ưu đãi đã cấp:</td>
        <td style="padding:8px; border-bottom:1px solid #eee;"><code style="background:#f1f5f9; padding:2px 6px; border-radius:4px; font-weight:bold;">' . esc_html($c_code) . '</code> (' . esc_html($c_disc) . ')</td>
      </tr>
      <tr>
        <td style="padding:8px; border-bottom:1px solid #eee; font-weight:bold;">Địa chỉ IP:</td>
        <td style="padding:8px; border-bottom:1px solid #eee;">' . esc_html($client_ip ? $client_ip : '—') . '</td>
      </tr>
    </table>
    <p style="margin-top:25px;">
      <a href="' . esc_url($admin_url) . '" style="display:inline-block; background:#07111e; color:#c5a880; padding:10px 20px; border-radius:5px; text-decoration:none; font-weight:bold;">
        Xem Danh Sách Khách Đăng Ký
      </a>
    </p>
  </div>
</body>
</html>';

        $headers = array('Content-Type: text/html; charset=UTF-8');
        foreach (array_unique($admin_emails) as $admin_email) {
            @wp_mail($admin_email, $subject, $message, $headers);
        }

        // Gửi thông báo qua Telegram nếu đang kích hoạt
        if (class_exists('DragonLegend_Zalo_Service')) {
            $settings = DragonLegend_Zalo_Service::get_settings();
            if (!empty($settings['telegram_enabled']) && $settings['telegram_enabled'] === '1') {
                $tg_msg = "🎁 <b>[ĐĂNG KÝ NHẬN ƯU ĐÃI MỚI - DRAGON LEGEND]</b>\n"
                        . "✉️ <b>Email khách hàng:</b> " . esc_html($subscriber_email) . "\n"
                        . "🎟️ <b>Mã ưu đãi đã cấp:</b> <code>" . esc_html($c_code) . "</code> (" . esc_html($c_disc) . ")\n"
                        . "🕒 <b>Thời gian:</b> " . esc_html(current_time('H:i d/m/Y'));
                DragonLegend_Zalo_Service::send_telegram_message($tg_msg);
            }
        }
    }

    /**
     * Xuất danh sách khách hàng ra file CSV (Hỗ trợ chuẩn tiếng Việt UTF-8 BOM cho Excel)
     */
    public static function handle_export_csv() {
        if (!isset($_GET['action']) || $_GET['action'] !== 'dl_export_subscribers_csv') {
            return;
        }

        if (!current_user_can('manage_options')) {
            wp_die(__('Bạn không có quyền thực hiện thao tác này.', 'dragonlegend-core'));
        }

        check_admin_referer('dl_export_subscribers_nonce');

        $subscribers = get_posts(array(
            'post_type'      => 'dl_subscriber',
            'post_status'    => 'any',
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ));

        $filename = 'dragonlegend-subscribers-' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // Ghi UTF-8 BOM để Excel hiển thị tiếng Việt không bị lỗi font
        fputs($output, "\xEF\xBB\xBF");

        // Dòng tiêu đề cột
        fputcsv($output, array('ID', 'Email Khách Hàng', 'Mã Ưu Đãi Đã Cấp', 'Ngày Đăng Ký', 'Địa Chỉ IP', 'Trạng Thái Thư'));

        foreach ($subscribers as $sub) {
            $coupon = get_post_meta($sub->ID, '_coupon_code', true);
            $ip = get_post_meta($sub->ID, '_subscriber_ip', true);
            $sent = get_post_meta($sub->ID, '_welcome_email_sent', true);
            $mail_status = ($sent === '1' || $sent === 1) ? 'Đã gửi mã voucher' : 'Chưa gửi';

            fputcsv($output, array(
                $sub->ID,
                $sub->post_title,
                $coupon ? $coupon : self::DEFAULT_COUPON,
                $sub->post_date,
                $ip ? $ip : '',
                $mail_status
            ));
        }

        fclose($output);
        exit;
    }

    /**
     * Xây dựng mã HTML cho email chiến dịch thương hiệu Dragon Legend
     */
    public static function build_broadcast_email_html($subject, $intro, $content, $coupon = '', $cta_text = '', $cta_url = '') {
        $site_name = get_bloginfo('name');
        $site_url  = home_url('/');
        if (empty($cta_url)) $cta_url = $site_url;
        if (empty($cta_text)) $cta_text = 'Xem Ưu Đãi & Đặt Phòng Ngay';

        $logo_url = function_exists('dl_get_content') 
            ? dl_get_content('header_logo_url', 'https://dragonlegendhotel.com/wp-content/uploads/2015/07/logoweb-1.png')
            : 'https://dragonlegendhotel.com/wp-content/uploads/2015/07/logoweb-1.png';

        $hotline = function_exists('dragonlegend_get_info') ? dragonlegend_get_info('hotline') : '0203 3518 999';
        $address = function_exists('dragonlegend_get_info') ? dragonlegend_get_info('address') : 'Bãi Cháy, TP. Hạ Long, Quảng Ninh';

        $voucher_html = '';
        if (!empty($coupon)) {
            $voucher_html = '
            <div style="background:#07111e; border:2px dashed #c5a880; border-radius:12px; padding:22px 20px; text-align:center; margin:28px 0; box-shadow:inset 0 2px 10px rgba(0,0,0,0.4);">
              <span style="display:block; font-size:11px; color:#94a3b8; text-transform:uppercase; letter-spacing:1.5px; margin-bottom:8px; font-weight:600;">Mã Khuyến Mãi Áp Dụng</span>
              <span style="font-size:26px; font-weight:bold; color:#c5a880; letter-spacing:4px; font-family:monospace; display:inline-block; background:rgba(197,168,128,0.15); padding:8px 22px; border-radius:8px; border:1px solid #c5a880;">' . esc_html($coupon) . '</span>
              <span style="display:block; font-size:12px; color:#38bdf8; margin-top:10px; font-weight:500;">✓ Nhập mã này khi đặt phòng trực tuyến để nhận ưu đãi</span>
            </div>';
        }

        $html = '<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <title>' . esc_html($subject) . '</title>
</head>
<body style="margin:0; padding:30px 15px; background-color:#07111e; font-family:-apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif; color:#cbd5e1;">
  <div style="max-width:620px; margin:0 auto; background-color:#0b1a2d; border:1px solid rgba(197, 168, 128, 0.4); border-radius:16px; overflow:hidden; box-shadow:0 12px 40px rgba(0,0,0,0.6);">
    
    <!-- Header -->
    <div style="background:linear-gradient(135deg, #07111e 0%, #152945 100%); padding:35px 25px; text-align:center; border-bottom:2px solid #c5a880;">
      <img src="' . esc_url($logo_url) . '" alt="Dragon Legend Hotel" style="height:48px; width:auto; margin-bottom:12px;">
      <h1 style="color:#ffffff; font-size:22px; margin:0; text-transform:uppercase; letter-spacing:1px; font-weight:700;">' . esc_html($site_name) . '</h1>
      <p style="color:#c5a880; font-size:11px; text-transform:uppercase; letter-spacing:2px; margin:6px 0 0 0; font-weight:600;">Tiêu chuẩn 4 Sao Quốc Tế ★★★★</p>
    </div>

    <!-- Body -->
    <div style="padding:35px 30px; font-size:14.5px; line-height:1.7; color:#e2e8f0;">
      ' . (!empty($intro) ? '<p style="font-size:15px; margin-top:0; color:#ffffff; font-weight:600;">' . nl2br(esc_html($intro)) . '</p>' : '') . '
      
      <div style="margin:20px 0; color:#cbd5e1;">
        ' . wpautop($content) . '
      </div>

      ' . $voucher_html . '

      <!-- Action Button -->
      <div style="text-align:center; margin:32px 0 24px 0;">
        <a href="' . esc_url($cta_url) . '" style="display:inline-block; background:linear-gradient(135deg, #d97706, #c5a880); color:#07111e; font-weight:bold; font-size:14px; text-decoration:none; padding:13px 36px; border-radius:30px; text-transform:uppercase; letter-spacing:1px; box-shadow:0 4px 15px rgba(217, 119, 6, 0.4);">
          ' . esc_html($cta_text) . ' →
        </a>
      </div>

      <hr style="border:none; border-top:1px solid rgba(255,255,255,0.12); margin:32px 0 22px 0;">

      <!-- Footer Info -->
      <p style="font-size:12px; color:#94a3b8; margin:0; line-height:1.6;">
        <strong style="color:#ffffff;">Dragon Legend Hotel Hạ Long</strong><br>
        📍 Địa chỉ: ' . esc_html($address) . '<br>
        📞 Hotline hỗ trợ: ' . esc_html($hotline) . '<br>
        🌐 Website: ' . esc_html(home_url()) . '<br>
        <span style="font-size:11px; color:#64748b; margin-top:6px; display:inline-block;">Quý khách nhận được email này vì đã đăng ký nhận thông tin ưu đãi tại website Dragon Legend Hotel.</span>
      </p>
    </div>

  </div>
</body>
</html>';

        return $html;
    }

    /**
     * AJAX Gửi email thử nghiệm (Test Broadcast Send)
     */
    public static function handle_test_broadcast() {
        check_ajax_referer('dl_broadcast_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Bạn không có quyền thực hiện.'));
        }

        $test_email = sanitize_email($_POST['test_email'] ?? '');
        if (empty($test_email) || !is_email($test_email)) {
            wp_send_json_error(array('message' => 'Vui lòng nhập địa chỉ email thử nghiệm hợp lệ.'));
        }

        $subject  = sanitize_text_field($_POST['subject'] ?? '[Dragon Legend Hotel] Bản Tin Ưu Đãi Thử Nghiệm');
        $intro    = sanitize_textarea_field($_POST['intro'] ?? '');
        $content  = wp_kses_post($_POST['content'] ?? '');
        $coupon   = sanitize_text_field($_POST['coupon'] ?? '');
        $cta_text = sanitize_text_field($_POST['cta_text'] ?? 'Đặt Phòng Ngay');
        $cta_url  = esc_url_raw($_POST['cta_url'] ?? home_url('/'));

        $body = self::build_broadcast_email_html($subject, $intro, $content, $coupon, $cta_text, $cta_url);
        $headers = array('Content-Type: text/html; charset=UTF-8');

        $sent = wp_mail($test_email, $subject, $body, $headers);

        if ($sent) {
            wp_send_json_success(array('message' => 'Email thử nghiệm đã được gửi thành công tới: ' . $test_email));
        } else {
            wp_send_json_error(array('message' => 'Gửi email thử nghiệm thất bại. Vui lòng kiểm tra lại cấu hình máy chủ SMTP.'));
        }
    }

    /**
     * AJAX Gửi email hàng loạt theo đợt (Batch Processing)
     */
    public static function handle_broadcast_batch() {
        check_ajax_referer('dl_broadcast_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Bạn không có quyền thực hiện.'));
        }

        $offset     = isset($_POST['offset']) ? max(0, intval($_POST['offset'])) : 0;
        $batch_size = isset($_POST['batch_size']) ? max(1, min(20, intval($_POST['batch_size']))) : 5;

        $subject  = sanitize_text_field($_POST['subject'] ?? '[Dragon Legend Hotel] Bản Tin Ưu Đãi');
        $intro    = sanitize_textarea_field($_POST['intro'] ?? '');
        $content  = wp_kses_post($_POST['content'] ?? '');
        $coupon   = sanitize_text_field($_POST['coupon'] ?? '');
        $cta_text = sanitize_text_field($_POST['cta_text'] ?? 'Đặt Phòng Ngay');
        $cta_url  = esc_url_raw($_POST['cta_url'] ?? home_url('/'));

        // Đếm tổng số khách hàng đăng ký
        $total_query = new WP_Query(array(
            'post_type'      => 'dl_subscriber',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'fields'         => 'ids',
        ));
        $total_subscribers = $total_query->found_posts;

        if ($total_subscribers === 0) {
            wp_send_json_error(array('message' => 'Hiện chưa có khách hàng nào đăng ký nhận ưu đãi trong hệ thống.'));
        }

        // Lấy danh sách đợt gửi hiện tại
        $subscribers = get_posts(array(
            'post_type'      => 'dl_subscriber',
            'post_status'    => 'publish',
            'posts_per_page' => $batch_size,
            'offset'         => $offset,
            'orderby'        => 'ID',
            'order'          => 'ASC',
        ));

        if (empty($subscribers)) {
            wp_send_json_success(array(
                'done'       => true,
                'sent_count' => 0,
                'total'      => $total_subscribers,
                'message'    => 'Đã hoàn tất gửi email cho toàn bộ danh sách khách hàng!'
            ));
        }

        $body = self::build_broadcast_email_html($subject, $intro, $content, $coupon, $cta_text, $cta_url);
        $headers = array('Content-Type: text/html; charset=UTF-8');

        $sent_in_batch   = 0;
        $failed_in_batch = 0;
        $sent_emails     = array();

        foreach ($subscribers as $sub) {
            $email = trim($sub->post_title);
            if (is_email($email)) {
                $res = wp_mail($email, $subject, $body, $headers);
                if ($res) {
                    $sent_in_batch++;
                    $sent_emails[] = $email;
                    update_post_meta($sub->ID, '_last_broadcast_sent', current_time('mysql'));
                } else {
                    $failed_in_batch++;
                }
            } else {
                $failed_in_batch++;
            }
        }

        $next_offset = $offset + count($subscribers);
        $done = ($next_offset >= $total_subscribers);

        wp_send_json_success(array(
            'done'            => $done,
            'offset'          => $next_offset,
            'sent_in_batch'   => $sent_in_batch,
            'failed_in_batch' => $failed_in_batch,
            'sent_emails'     => $sent_emails,
            'total'           => $total_subscribers,
            'progress'        => min(100, round(($next_offset / $total_subscribers) * 100)),
        ));
    }

    /**
     * Render giao diện trang quản trị Gửi Email Hàng Loạt
     */
    public static function render_broadcast_page() {
        $total_subscribers = wp_count_posts('dl_subscriber')->publish ?? 0;
        $export_url = wp_nonce_url(admin_url('admin.php?action=dl_export_subscribers_csv'), 'dl_export_subscribers_nonce');

        $smtp_info = 'Chưa cấu hình';
        if (class_exists('DragonLegend_SMTP_Service')) {
            $smtp = DragonLegend_SMTP_Service::get_settings();
            if (!empty($smtp['smtp_enabled']) && $smtp['smtp_enabled'] === '1') {
                $smtp_info = esc_html($smtp['smtp_from_email']) . ' (' . esc_html($smtp['smtp_host']) . ':' . esc_html($smtp['smtp_port']) . ')';
            }
        }

        $default_subject = '[Dragon Legend Hotel] Tri Ân Kỳ Nghỉ - Nhận Ưu Đãi Độc Quyền Dành Cho Bạn';
        $default_intro   = 'Kính chào Quý khách, Dragon Legend Hotel Hạ Long xin trân trọng gửi tới Quý khách chương trình ưu đãi đặc biệt trong tháng:';
        $default_content = '<p>Tận hưởng không gian nghỉ dưỡng hoàng gia 4 sao tại trung tâm Bãi Cháy cùng hồ bơi vô cực ngắm trọn kỳ quan Vịnh Hạ Long.</p>
<p>Nhập ngay mã khuyến mãi bên dưới khi đặt phòng trực tuyến để nhận đặc quyền giảm giá độc quyền dành riêng cho khách hàng thân thiết!</p>';
        ?>
        <div class="wrap" style="max-width: 1200px; margin-top: 20px;">
            
            <!-- Header Banner -->
            <div style="background: linear-gradient(135deg, #07111e 0%, #152945 100%); color: #fff; padding: 24px 30px; border-radius: 12px; margin-bottom: 25px; border-left: 6px solid #c5a880; box-shadow: 0 4px 20px rgba(0,0,0,0.15); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <div>
                    <h1 style="color: #fff; font-size: 24px; font-weight: 700; margin: 0 0 6px 0;">Gửi Email Khuyến Mãi Hàng Loạt (Broadcast Campaign)</h1>
                    <p style="color: #94a3b8; margin: 0; font-size: 13px;">Gửi thông điệp tri ân, combo nghỉ dưỡng và mã voucher tới toàn bộ khách hàng đã đăng ký nhận tin.</p>
                </div>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <a href="<?php echo esc_url($export_url); ?>" class="button button-primary" style="background: #10b981; border-color: #059669; font-weight: 600; padding: 6px 14px; height: auto;">
                        <span class="dashicons dashicons-download" style="vertical-align: text-top; margin-top: 1px;"></span> Xuất File Excel (CSV)
                    </a>
                    <a href="<?php echo esc_url(admin_url('edit.php?post_type=dl_subscriber')); ?>" class="button button-secondary" style="font-weight: 600; padding: 6px 14px; height: auto;">
                        <span class="dashicons dashicons-groups" style="vertical-align: text-top; margin-top: 1px;"></span> Xem Danh Sách (<?php echo intval($total_subscribers); ?>)
                    </a>
                </div>
            </div>

            <!-- Stats Bar -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 15px; margin-bottom: 25px;">
                <div style="background: #fff; padding: 18px 20px; border-radius: 10px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                    <div style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 700;">Tổng Khách Hàng Đăng Ký</div>
                    <div style="font-size: 28px; font-weight: 800; color: #07111e; margin-top: 4px;"><?php echo intval($total_subscribers); ?> <span style="font-size: 14px; font-weight: normal; color: #10b981;">Email</span></div>
                </div>
                <div style="background: #fff; padding: 18px 20px; border-radius: 10px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                    <div style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 700;">Máy Chủ Gửi Thư (SMTP)</div>
                    <div style="font-size: 13.5px; font-weight: 700; color: #07111e; margin-top: 8px; word-break: break-all;">
                        <span class="dashicons dashicons-yes-alt" style="color: #10b981; vertical-align: middle;"></span> <?php echo $smtp_info; ?>
                    </div>
                </div>
                <div style="background: #fff; padding: 18px 20px; border-radius: 10px; border: 1px solid #e2e8f0; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                    <div style="font-size: 12px; color: #64748b; text-transform: uppercase; font-weight: 700;">Cơ Chế Gửi Thư An Toàn</div>
                    <div style="font-size: 13.5px; font-weight: 600; color: #475569; margin-top: 8px;">
                        Gửi chia nhỏ 5 email/lần có thanh tiến trình %
                    </div>
                </div>
            </div>

            <!-- Main Work Area (2 Columns) -->
            <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 25px; align-items: flex-start;">
                
                <!-- Left Column: Compose Form -->
                <div style="background: #fff; padding: 25px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                    <h3 style="margin-top: 0; font-size: 16px; font-weight: 700; color: #07111e; padding-bottom: 12px; border-bottom: 2px solid #f1f5f9; display: flex; align-items: center; gap: 8px;">
                        <span class="dashicons dashicons-edit" style="color: #c5a880;"></span> Soạn Thảo Nội Dung Chiến Dịch
                    </h3>

                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #1e293b;">Tiêu đề Email (Subject): <span style="color:#ef4444;">*</span></label>
                        <input type="text" id="bc-subject" value="<?php echo esc_attr($default_subject); ?>" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
                    </div>

                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #1e293b;">Lời mở đầu (Dòng chữ nổi bật):</label>
                        <textarea id="bc-intro" rows="2" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px;"><?php echo esc_textarea($default_intro); ?></textarea>
                    </div>

                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #1e293b;">Nội dung chi tiết chương trình:</label>
                        <?php 
                        wp_editor($default_content, 'bc_content', array(
                            'textarea_rows' => 8,
                            'media_buttons' => true,
                            'teeny'         => false,
                            'quicktags'     => true,
                        )); 
                        ?>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 16px;">
                        <div>
                            <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #1e293b;">Mã voucher đính kèm:</label>
                            <input type="text" id="bc-coupon" list="available-coupons-list" value="DRAGON2026" placeholder="VD: SUMMER20" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; font-family: monospace; font-weight: bold; text-transform: uppercase;">
                            <datalist id="available-coupons-list">
                                <?php
                                $active_coupons = class_exists('DragonLegend_Coupons') ? DragonLegend_Coupons::get_active_coupons() : array();
                                foreach ($active_coupons as $ac) {
                                    echo '<option value="' . esc_attr($ac['code']) . '">' . esc_html($ac['code'] . ' - Giảm ' . $ac['formatted'] . ' (' . $ac['description'] . ')') . '</option>';
                                }
                                ?>
                            </datalist>
                            <span style="font-size: 11px; color: #64748b;">Gõ mã hoặc chọn từ danh sách mã ưu đãi đang kích hoạt.</span>
                        </div>
                        <div>
                            <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #1e293b;">Tên nút bấm (CTA):</label>
                            <input type="text" id="bc-cta-text" value="Xem Chi Tiết & Đặt Phòng Ngay" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
                        </div>
                    </div>

                    <div style="margin-bottom: 16px;">
                        <label style="display: block; font-weight: 600; font-size: 13px; margin-bottom: 6px; color: #1e293b;">Đường dẫn nút bấm (Link URL):</label>
                        <input type="url" id="bc-cta-url" value="<?php echo esc_url(home_url('/')); ?>" style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px;">
                    </div>
                </div>

                <!-- Right Column: Test Send & Mass Send Controls -->
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    
                    <!-- Box 1: Test Email -->
                    <div style="background: #fff; padding: 22px; border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                        <h4 style="margin: 0 0 10px 0; font-size: 15px; font-weight: 700; color: #07111e; display: flex; align-items: center; gap: 8px;">
                            <span class="dashicons dashicons-email" style="color: #2563eb;"></span> Bước 1: Gửi Thử Nghiệm Duyệt Mẫu
                        </h4>
                        <p style="font-size: 12.5px; color: #64748b; margin-bottom: 14px;">Gửi thử một bản đến hòm thư riêng để kiểm tra màu sắc, logo và câu chữ thực tế trước khi phát tán diện rộng.</p>
                        
                        <div style="margin-bottom: 12px;">
                            <input type="email" id="bc-test-email" value="<?php echo esc_attr(get_option('admin_email')); ?>" placeholder="Nhập email nhận bản thử" style="width: 100%; padding: 9px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 13.5px;">
                        </div>

                        <button type="button" id="bc-test-btn" class="button" style="width: 100%; padding: 8px; font-weight: 600; border-color: #2563eb; color: #2563eb; display: flex; align-items: center; justify-content: center; gap: 6px;">
                            <span class="dashicons dashicons-share-alt" style="margin-top: 1px;"></span> Gửi Bản Thử Nghiệm
                        </button>
                        <div id="bc-test-feedback" style="display: none; margin-top: 10px; padding: 10px 12px; border-radius: 6px; font-size: 12.5px; line-height: 1.4;"></div>
                    </div>

                    <!-- Box 2: Mass Broadcast Execution -->
                    <div style="background: #fff; padding: 24px; border-radius: 12px; border: 2px solid #c5a880; box-shadow: 0 4px 15px rgba(197, 168, 128, 0.15);">
                        <h4 style="margin: 0 0 10px 0; font-size: 16px; font-weight: 700; color: #07111e; display: flex; align-items: center; gap: 8px;">
                            <span class="dashicons dashicons-megaphone" style="color: #d97706;"></span> Bước 2: Bắt Đầu Phát Tán Hàng Loạt
                        </h4>
                        <p style="font-size: 13px; color: #475569; margin-bottom: 16px;">
                            Sẵn sàng gửi tới <strong style="color: #07111e; font-size: 15px;"><?php echo intval($total_subscribers); ?></strong> khách hàng đã đăng ký nhận tin.
                        </p>

                        <!-- Progress Bar Box -->
                        <div id="bc-progress-wrap" style="display: none; margin-bottom: 16px;">
                            <div style="display: flex; justify-content: space-between; font-size: 12px; font-weight: 700; color: #07111e; margin-bottom: 6px;">
                                <span>Tiến trình gửi thư:</span>
                                <span id="bc-progress-text">0%</span>
                            </div>
                            <div style="width: 100%; height: 16px; background: #e2e8f0; border-radius: 8px; overflow: hidden; position: relative;">
                                <div id="bc-progress-bar" style="width: 0%; height: 100%; background: linear-gradient(90deg, #d97706, #10b981); transition: width 0.3s ease;"></div>
                            </div>
                        </div>

                        <!-- Live Log Box -->
                        <div id="bc-log-box" style="display: none; max-height: 160px; overflow-y: auto; background: #07111e; color: #38bdf8; font-family: monospace; font-size: 11px; padding: 12px; border-radius: 8px; margin-bottom: 16px; line-height: 1.5;"></div>

                        <button type="button" id="bc-start-btn" class="button button-primary" style="width: 100%; background: linear-gradient(135deg, #07111e 0%, #152945 100%); border-color: #c5a880; color: #c5a880; font-weight: 700; font-size: 14px; padding: 12px; height: auto; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 4px 12px rgba(7, 17, 30, 0.25);">
                            🚀 Bắt Đầu Gửi Cho Tất Cả (<?php echo intval($total_subscribers); ?> Khách)
                        </button>
                    </div>

                </div>

            </div>

        </div>

        <script>
        jQuery(document).ready(function($) {
            const nonce = '<?php echo wp_create_nonce("dl_broadcast_nonce"); ?>';

            // Helper lấy nội dung từ WP Editor
            function getContent() {
                if (typeof tinyMCE !== 'undefined' && tinyMCE.get('bc_content')) {
                    return tinyMCE.get('bc_content').getContent();
                }
                return $('#bc_content').val();
            }

            // 1. Gửi Thử Nghiệm (Test Send)
            $('#bc-test-btn').on('click', function(e) {
                e.preventDefault();
                const btn = $(this);
                const feedback = $('#bc-test-feedback');
                const testEmail = $('#bc-test-email').val().trim();

                if (!testEmail) {
                    alert('Vui lòng nhập địa chỉ email thử nghiệm.');
                    return;
                }

                btn.prop('disabled', true).text('Đang gửi thử...');
                feedback.hide();

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'dl_send_test_broadcast',
                        nonce: nonce,
                        test_email: testEmail,
                        subject: $('#bc-subject').val(),
                        intro: $('#bc-intro').val(),
                        content: getContent(),
                        coupon: $('#bc-coupon').val(),
                        cta_text: $('#bc-cta-text').val(),
                        cta_url: $('#bc-cta-url').val(),
                    },
                    success: function(res) {
                        feedback.show();
                        if (res.success) {
                            feedback.css({ background: '#ecfdf5', color: '#065f46', border: '1px solid #a7f3d0' })
                                    .html('✓ ' + res.data.message);
                        } else {
                            feedback.css({ background: '#fef2f2', color: '#991b1b', border: '1px solid #fecaca' })
                                    .html('✕ ' + res.data.message);
                        }
                    },
                    error: function() {
                        feedback.show().css({ background: '#fef2f2', color: '#991b1b', border: '1px solid #fecaca' })
                                .html('✕ Không thể kết nối tới máy chủ. Vui lòng thử lại.');
                    },
                    complete: function() {
                        btn.prop('disabled', false).html('<span class="dashicons dashicons-share-alt" style="margin-top:1px;"></span> Gửi Bản Thử Nghiệm');
                    }
                });
            });

            // 2. Gửi Hàng Loạt (Mass Batch Sending)
            let currentOffset = 0;
            let totalSent = 0;
            let totalFailed = 0;
            const batchSize = 5;

            function log(msg) {
                const box = $('#bc-log-box');
                const time = new Date().toLocaleTimeString();
                box.append('[' + time + '] ' + msg + '<br>');
                box.scrollTop(box[0].scrollHeight);
            }

            function sendBatch() {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'dl_send_broadcast_batch',
                        nonce: nonce,
                        offset: currentOffset,
                        batch_size: batchSize,
                        subject: $('#bc-subject').val(),
                        intro: $('#bc-intro').val(),
                        content: getContent(),
                        coupon: $('#bc-coupon').val(),
                        cta_text: $('#bc-cta-text').val(),
                        cta_url: $('#bc-cta-url').val(),
                    },
                    success: function(res) {
                        if (res.success) {
                            const data = res.data;
                            totalSent += data.sent_in_batch;
                            totalFailed += data.failed_in_batch;

                            if (data.sent_emails && data.sent_emails.length) {
                                log('✓ Đã gửi thành công cho: ' + data.sent_emails.join(', '));
                            }
                            if (data.failed_in_batch > 0) {
                                log('⚠ Gửi thất bại: ' + data.failed_in_batch + ' email');
                            }

                            // Cập nhật thanh tiến trình
                            $('#bc-progress-bar').css('width', data.progress + '%');
                            $('#bc-progress-text').text(data.progress + '% (' + data.offset + '/' + data.total + ')');

                            if (!data.done && data.offset < data.total) {
                                currentOffset = data.offset;
                                setTimeout(sendBatch, 800); // Tạm dừng 800ms giữa các batch
                            } else {
                                log('🎉 HOÀN THÀNH TOÀN BỘ CHIẾN DỊCH! Tổng gửi: ' + totalSent + ' thành công, ' + totalFailed + ' lỗi.');
                                alert('🎉 Chúc mừng! Đã hoàn thành gửi email hàng loạt tới ' + totalSent + ' khách hàng.');
                                $('#bc-start-btn').prop('disabled', false).text('Gửi Lại Chiến Dịch Khác');
                            }
                        } else {
                            log('✕ Lỗi: ' + (res.data ? res.data.message : 'Có lỗi xảy ra'));
                            alert(res.data ? res.data.message : 'Có lỗi xảy ra.');
                            $('#bc-start-btn').prop('disabled', false).text('Thử Gửi Lại');
                        }
                    },
                    error: function() {
                        log('✕ Lỗi kết nối mạng trong quá trình gửi batch.');
                        $('#bc-start-btn').prop('disabled', false).text('Thử Gửi Lại');
                    }
                });
            }

            $('#bc-start-btn').on('click', function(e) {
                e.preventDefault();
                const total = <?php echo intval($total_subscribers); ?>;

                if (total === 0) {
                    alert('Chưa có khách hàng nào đăng ký nhận ưu đãi.');
                    return;
                }

                if (!confirm('Bạn có chắc chắn muốn gửi email chiến dịch này tới toàn bộ ' + total + ' khách hàng không?')) {
                    return;
                }

                $(this).prop('disabled', true).text('Đang tiến hành gửi email hàng loạt...');
                $('#bc-progress-wrap').slideDown();
                $('#bc-log-box').slideDown().html('');

                currentOffset = 0;
                totalSent = 0;
                totalFailed = 0;

                log('Bắt đầu khởi chạy chiến dịch gửi email...');
                sendBatch();
            });
        });
        </script>
        <?php
    }
}
