<?php
/**
 * Dragon Legend Hotel Core - SMTP Mail Server Service
 * Tích hợp & cấu hình máy chủ gửi thư SMTP (Gmail, Brevo, SendGrid, Mailgun, cPanel Webmail...)
 *
 * @package DragonLegendCore
 */

if (!defined('ABSPATH')) {
    exit;
}

class DragonLegend_SMTP_Service {

    const OPTION_KEY = 'dl_smtp_settings';

    /**
     * Biến tạm lưu vết nhật ký gỡ lỗi khi gửi thư thử nghiệm
     *
     * @var string
     */
    private static $test_debug_log = '';

    /**
     * Khởi tạo service & đăng ký các hook WordPress
     */
    public static function init() {
        // Cấu hình PHPMailer trước khi gửi
        add_action('phpmailer_init', array(__CLASS__, 'configure_phpmailer'), 999);

        // Chuẩn hóa From Email & From Name
        add_filter('wp_mail_from', array(__CLASS__, 'filter_mail_from'), 999);
        add_filter('wp_mail_from_name', array(__CLASS__, 'filter_mail_from_name'), 999);

        // Bắt lỗi khi gửi thư thất bại
        add_action('wp_mail_failed', array(__CLASS__, 'handle_mail_failed'));
    }

    /**
     * Lấy các thiết lập cấu hình SMTP
     */
    public static function get_settings() {
        $defaults = array(
            'smtp_enabled'    => '0', // '0' | '1'
            'smtp_host'       => 'smtp.gmail.com',
            'smtp_port'       => '465',
            'smtp_encryption' => 'ssl', // 'ssl' | 'tls' | 'none'
            'smtp_auth'       => 'yes', // 'yes' | 'no'
            'smtp_username'   => '',
            'smtp_password'   => '',
            'smtp_from_email' => get_option('admin_email'),
            'smtp_from_name'  => 'Dragon Legend Hotel',
        );

        $saved = get_option(self::OPTION_KEY, array());
        if (!is_array($saved)) {
            $saved = array();
        }

        return wp_parse_args($saved, $defaults);
    }

    /**
     * Lưu cấu hình SMTP
     */
    public static function save_settings($settings) {
        return update_option(self::OPTION_KEY, $settings);
    }

    /**
     * Cấu hình đối tượng PHPMailer của WordPress
     *
     * @param PHPMailer\PHPMailer\PHPMailer $phpmailer
     */
    public static function configure_phpmailer($phpmailer) {
        $settings = self::get_settings();

        // Nếu tính năng SMTP chưa được bật thì giữ nguyên thiết lập mặc định của máy chủ
        if (empty($settings['smtp_enabled'])) {
            return;
        }

        // Thiết lập gửi qua giao thức SMTP
        $phpmailer->isSMTP();
        $phpmailer->Host = sanitize_text_field($settings['smtp_host']);
        $phpmailer->Port = intval($settings['smtp_port']) ?: 587;
        $phpmailer->Timeout = 15; // Giới hạn thời gian kết nối 15 giây

        // Mã hóa SSL/TLS
        $encryption = strtolower($settings['smtp_encryption']);
        if ($encryption === 'ssl') {
            $phpmailer->SMTPSecure = 'ssl';
        } elseif ($encryption === 'tls') {
            $phpmailer->SMTPSecure = 'tls';
        } else {
            $phpmailer->SMTPSecure = '';
            $phpmailer->SMTPAutoTLS = false;
        }

        // Tùy chọn SSL an toàn tương thích mọi môi trường máy chủ
        $phpmailer->SMTPOptions = array(
            'ssl' => array(
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true,
            ),
        );

        // Xác thực người dùng (Authentication)
        if ($settings['smtp_auth'] === 'yes') {
            $phpmailer->SMTPAuth = true;
            $phpmailer->Username = sanitize_text_field($settings['smtp_username']);
            $phpmailer->Password = $settings['smtp_password'];
        } else {
            $phpmailer->SMTPAuth = false;
        }

        // Thiết lập người gửi (From)
        $from_email = !empty($settings['smtp_from_email']) ? sanitize_email($settings['smtp_from_email']) : '';
        $from_name  = !empty($settings['smtp_from_name']) ? sanitize_text_field($settings['smtp_from_name']) : 'Dragon Legend Hotel';

        if (!empty($from_email) && is_email($from_email)) {
            try {
                $phpmailer->setFrom($from_email, $from_name, false);
            } catch (Exception $e) {
                // Bỏ qua lỗi From nếu đã được set
            }
        }

        // Bật debug logger nếu đang thực hiện gửi thử nghiệm
        if (self::$test_debug_log !== null) {
            $phpmailer->SMTPDebug = 2; // Bắt cả thông điệp gửi (Client) và phản hồi từ Server
            $phpmailer->Debugoutput = function($str, $level) {
                self::$test_debug_log .= trim($str) . "\n";
            };
        }
    }

    /**
     * Bộ lọc địa chỉ email người gửi
     */
    public static function filter_mail_from($original_email) {
        $settings = self::get_settings();
        if (!empty($settings['smtp_enabled']) && !empty($settings['smtp_from_email']) && is_email($settings['smtp_from_email'])) {
            return sanitize_email($settings['smtp_from_email']);
        }
        return $original_email;
    }

    /**
     * Bộ lọc tên người gửi
     */
    public static function filter_mail_from_name($original_name) {
        $settings = self::get_settings();
        if (!empty($settings['smtp_enabled']) && !empty($settings['smtp_from_name'])) {
            return sanitize_text_field($settings['smtp_from_name']);
        }
        return $original_name;
    }

    /**
     * Bắt lỗi khi wp_mail gặp sự cố
     */
    public static function handle_mail_failed($wp_error) {
        if (is_wp_error($wp_error)) {
            $error_message = $wp_error->get_error_message();
            $error_data    = $wp_error->get_error_data();
            
            // Lưu lại lỗi gần nhất để hiển thị nếu cần
            update_option('dl_smtp_last_error', array(
                'time'    => current_time('mysql'),
                'message' => $error_message,
                'data'    => $error_data,
            ));
        }
    }

    /**
     * Thực hiện gửi thư thử nghiệm và thu thập nhật ký kết nối (Debug Transcript)
     *
     * @param string $to_email
     * @return array
     */
    public static function test_smtp_connection($to_email) {
        if (!is_email($to_email)) {
            return array(
                'success'   => false,
                'message'   => 'Địa chỉ email người nhận không hợp lệ: ' . esc_html($to_email),
                'debug_log' => '',
            );
        }

        $settings = self::get_settings();
        if (empty($settings['smtp_enabled'])) {
            return array(
                'success'   => false,
                'message'   => 'Tính năng SMTP đang ở trạng thái TẮT. Vui lòng bật "Kích hoạt gửi thư qua SMTP" và Lưu cấu hình trước khi thử nghiệm.',
                'debug_log' => '',
            );
        }

        // Đặt biến debug log rỗng để bắt đầu thu thập
        self::$test_debug_log = '';

        $hotel_name = get_bloginfo('name') ?: 'Dragon Legend Hotel';
        $subject = sprintf('[%s] Thư kiểm tra kết nối máy chủ gửi thư SMTP', $hotel_name);

        $body = '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 25px; border: 1px solid #e2e8f0; border-radius: 12px; background: #ffffff;">
            <div style="text-align: center; margin-bottom: 20px;">
                <h2 style="color: #07111e; margin: 0 0 6px 0;">Dragon Legend Hotel Hạ Long</h2>
                <span style="color: #c5a880; font-weight: bold; font-size: 13px; text-transform: uppercase; letter-spacing: 1px;">Kiểm Tra Kết Nối SMTP Thành Công</span>
            </div>
            <div style="background: #f8fafc; padding: 18px; border-radius: 8px; border-left: 4px solid #10b981; margin-bottom: 20px;">
                <p style="margin: 0; color: #15803d; font-weight: bold; font-size: 15px;">✓ Kết nối tới máy chủ SMTP thành công!</p>
                <p style="margin: 6px 0 0 0; color: #475569; font-size: 13px;">Hệ thống gửi thư của website đã giao tiếp hoàn hảo với máy chủ mail của bạn.</p>
            </div>
            <table style="width: 100%; border-collapse: collapse; font-size: 13px; margin-bottom: 20px;">
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 8px 0; color: #64748b; width: 140px;">Máy chủ (Host):</td>
                    <td style="padding: 8px 0; font-weight: 600; color: #1e293b;">' . esc_html($settings['smtp_host']) . '</td>
                </tr>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 8px 0; color: #64748b;">Cổng (Port):</td>
                    <td style="padding: 8px 0; font-weight: 600; color: #1e293b;">' . esc_html($settings['smtp_port']) . ' (' . strtoupper($settings['smtp_encryption']) . ')</td>
                </tr>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 8px 0; color: #64748b;">Tài khoản gửi:</td>
                    <td style="padding: 8px 0; font-weight: 600; color: #1e293b;">' . esc_html($settings['smtp_username'] ?: 'Không xác thực') . '</td>
                </tr>
                <tr style="border-bottom: 1px solid #f1f5f9;">
                    <td style="padding: 8px 0; color: #64748b;">Email gửi đi:</td>
                    <td style="padding: 8px 0; font-weight: 600; color: #1e293b;">' . esc_html($settings['smtp_from_email']) . '</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #64748b;">Thời gian kiểm tra:</td>
                    <td style="padding: 8px 0; font-weight: 600; color: #1e293b;">' . current_time('H:i:s d/m/Y') . '</td>
                </tr>
            </table>
            <p style="font-size: 12px; color: #94a3b8; text-align: center; margin: 0;">
                Email này được gửi tự động từ hệ thống quản trị Dragon Legend Hotel.
            </p>
        </div>
        ';

        $headers = array('Content-Type: text/html; charset=UTF-8');

        // Bắt đầu gửi thư
        $sent = @wp_mail($to_email, $subject, $body, $headers);

        $debug_log = self::$test_debug_log;
        self::$test_debug_log = null; // Đặt lại sau khi gửi xong

        if ($sent) {
            return array(
                'success'   => true,
                'message'   => sprintf('Đã gửi thư kiểm tra thành công tới %s lúc %s!', esc_html($to_email), current_time('H:i d/m/Y')),
                'debug_log' => $debug_log,
            );
        } else {
            $last_error = get_option('dl_smtp_last_error');
            $err_msg = 'Không thể gửi thư qua máy chủ SMTP.';
            if (!empty($last_error['message'])) {
                $err_msg .= ' Chi tiết lỗi: ' . $last_error['message'];
            }

            return array(
                'success'   => false,
                'message'   => $err_msg,
                'debug_log' => $debug_log,
            );
        }
    }
}
