<?php
/**
 * Dragon Legend Hotel Core - Zalo Notification Service
 * Xử lý gửi tin nhắn Zalo tới Admin/Sale và Khách hàng (Hỗ trợ ZNS, Webhook & Mô phỏng Log)
 *
 * @package DragonLegendCore
 */

if (!defined('ABSPATH')) {
    exit;
}

class DragonLegend_Zalo_Service {

    const OPTION_KEY = 'dl_booking_notification_settings';

    /**
     * Lấy toàn bộ cấu hình thông báo
     */
    public static function get_settings() {
        $defaults = array(
            'zalo_enabled'              => '1',
            'zalo_mode'                 => 'simulation', // 'simulation' | 'zns' | 'webhook'
            'admin_zalo_phones'         => '0987654321',
            'zalo_oa_id'                => '',
            'zalo_app_id'               => '',
            'zalo_secret_key'           => '',
            'zalo_access_token'         => '',
            'zalo_refresh_token'        => '',
            'zns_template_customer'     => '324892',
            'zns_template_admin'        => '324893',
            'webhook_url'               => '',
            'webhook_secret'            => '',
            'zalo_customer_msg'         => "Kính gửi Quý khách {guest_name}, Dragon Legend Hotel Hạ Long xin xác nhận đã tiếp nhận yêu cầu đặt phòng mã {booking_code} ({room_name}, {checkin} đến {checkout}, {rooms} phòng). Lễ tân khách sạn sẽ liên hệ Quý khách qua SĐT {guest_phone} trong ít phút để hoàn tất xác nhận. Hotline hỗ trợ 24/7: {hotel_hotline}.",
            'zalo_admin_msg'            => "🔔 [ĐƠN ĐẶT PHÒNG MỚI] Mã: {booking_code} | Khách: {guest_name} ({guest_phone}) | Phòng: {room_name} ({rooms} phòng) | Thời gian: {checkin} - {checkout} ({nights} đêm) | Khách: {adults} lớn, {children} trẻ | Tổng tiền: {grand_total} | Ghi chú: {guest_note} | Xem đơn: {admin_link}",
            'zalo_notify_contact'       => '1',
            'zalo_contact_admin_msg'    => "📩 [LIÊN HỆ MỚI TỪ WEBSITE] Khách hàng: {guest_name} ({guest_phone}) | Email: {guest_email} | Chủ đề: {subject} | Nội dung: {message}",
            // Cấu hình Telegram Bot (Đặt phòng & Liên hệ)
            'telegram_enabled'          => '0',
            'telegram_bot_token'        => '',
            'telegram_chat_id'          => '',
            'telegram_notify_booking'   => '1',
            'telegram_notify_contact'   => '1',
            // Cấu hình Email
            'email_enabled'             => '1',
            'admin_notification_emails' => get_option('admin_email') . (function_exists('dragonlegend_get_info') && dragonlegend_get_info('email') && dragonlegend_get_info('email') !== get_option('admin_email') ? ', ' . dragonlegend_get_info('email') : ''),
            'email_subject_template'    => '[Dragon Legend Hotel] Đơn đặt phòng mới: {booking_code} - {guest_name}',
            'customer_email_enabled'    => '1',
        );

        $saved = get_option(self::OPTION_KEY, array());
        if (!is_array($saved)) {
            $saved = array();
        }

        return wp_parse_args($saved, $defaults);
    }

    /**
     * Lưu cấu hình thông báo
     */
    public static function save_settings($settings) {
        return update_option(self::OPTION_KEY, $settings);
    }

    /**
     * Chuẩn hóa số điện thoại Việt Nam sang định dạng quốc tế (84...)
     */
    public static function normalize_phone($phone) {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (empty($phone)) {
            return '';
        }

        if (strpos($phone, '84') === 0) {
            return $phone;
        }

        if (strpos($phone, '0') === 0) {
            return '84' . substr($phone, 1);
        }

        return '84' . $phone;
    }

    /**
     * Định dạng số điện thoại hiển thị đẹp mắt (0987 654 321)
     */
    public static function format_phone_display($phone) {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strpos($phone, '84') === 0) {
            $phone = '0' . substr($phone, 2);
        }
        if (strlen($phone) === 10) {
            return substr($phone, 0, 4) . ' ' . substr($phone, 4, 3) . ' ' . substr($phone, 7, 3);
        }
        return $phone;
    }

    /**
     * Thay thế placeholders vào nội dung tin nhắn
     */
    public static function replace_placeholders($template, $data) {
        $hotline = function_exists('dragonlegend_get_info') ? dragonlegend_get_info('hotline') : '0203 3518 999';
        $hotel_name = get_bloginfo('name') ?: 'Dragon Legend Hotel';

        $checkin_fmt = !empty($data['checkin']) ? date_i18n('d/m/Y', strtotime($data['checkin'])) : '';
        $checkout_fmt = !empty($data['checkout']) ? date_i18n('d/m/Y', strtotime($data['checkout'])) : '';

        $grand_total_str = isset($data['grand_total']) 
            ? (is_numeric($data['grand_total']) ? number_format(floatval($data['grand_total']), 0, ',', '.') . ' VNĐ' : $data['grand_total'])
            : '0 VNĐ';

        $admin_link = !empty($data['booking_id']) 
            ? admin_url('post.php?post=' . intval($data['booking_id']) . '&action=edit')
            : admin_url('edit.php?post_type=dl_booking');

        $replacements = array(
            '{guest_name}'    => isset($data['guest_name']) ? $data['guest_name'] : '',
            '{guest_phone}'   => isset($data['guest_phone']) ? self::format_phone_display($data['guest_phone']) : '',
            '{guest_email}'   => isset($data['guest_email']) ? $data['guest_email'] : '',
            '{booking_code}'  => isset($data['booking_code']) ? $data['booking_code'] : '',
            '{room_name}'     => isset($data['room_name']) ? html_entity_decode($data['room_name'], ENT_QUOTES, 'UTF-8') : '',
            '{checkin}'       => $checkin_fmt ?: (isset($data['checkin']) ? $data['checkin'] : ''),
            '{checkout}'      => $checkout_fmt ?: (isset($data['checkout']) ? $data['checkout'] : ''),
            '{nights}'        => isset($data['nights']) ? $data['nights'] : '1',
            '{rooms}'         => isset($data['rooms']) ? $data['rooms'] : '1',
            '{adults}'        => isset($data['adults']) ? $data['adults'] : '2',
            '{children}'      => isset($data['children']) ? $data['children'] : '0',
            '{grand_total}'   => $grand_total_str,
            '{guest_note}'    => !empty($data['guest_note']) ? $data['guest_note'] : 'Không có ghi chú',
            '{hotel_hotline}' => $hotline,
            '{hotel_name}'    => $hotel_name,
            '{admin_link}'    => $admin_link,
        );

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    /**
     * Gửi toàn bộ thông báo Zalo khi có đơn đặt phòng
     * 1. Gửi cho Khách hàng
     * 2. Gửi cho Admin/Sale được chỉ định
     */
    public static function send_booking_notifications($booking_id, $data) {
        $settings = self::get_settings();
        if (empty($settings['zalo_enabled'])) {
            self::log_booking_notification($booking_id, 'Zalo', 'Bỏ qua (Tính năng Zalo đang tắt)');
            update_post_meta($booking_id, '_dl_booking_zalo_guest_status', 'disabled');
            update_post_meta($booking_id, '_dl_booking_zalo_admin_status', 'disabled');
            return false;
        }

        $data['booking_id'] = $booking_id;

        // 1. Gửi tin nhắn Zalo cho Khách hàng
        $guest_result = self::send_message_to_guest($data, $settings);
        update_post_meta($booking_id, '_dl_booking_zalo_guest_status', $guest_result['status']);
        self::log_booking_notification(
            $booking_id, 
            'Zalo Khách hàng', 
            sprintf('[%s - %s] %s', strtoupper($guest_result['mode']), $guest_result['status'], $guest_result['message'])
        );

        // 2. Gửi tin nhắn Zalo cho Admin / Sale được chỉ định
        $admin_result = self::send_message_to_admin($data, $settings);
        update_post_meta($booking_id, '_dl_booking_zalo_admin_status', $admin_result['status']);
        self::log_booking_notification(
            $booking_id, 
            'Zalo Admin/Sale', 
            sprintf('[%s - %s] %s', strtoupper($admin_result['mode']), $admin_result['status'], $admin_result['message'])
        );

        return array(
            'guest' => $guest_result,
            'admin' => $admin_result,
        );
    }

    /**
     * Gửi tin nhắn Zalo tới Khách hàng qua SĐT đăng ký
     */
    public static function send_message_to_guest($data, $settings) {
        $phone_raw = isset($data['guest_phone']) ? $data['guest_phone'] : '';
        $phone_norm = self::normalize_phone($phone_raw);

        if (empty($phone_norm)) {
            return array(
                'status'  => 'failed',
                'mode'    => $settings['zalo_mode'],
                'message' => 'Số điện thoại khách hàng không hợp lệ: ' . $phone_raw,
            );
        }

        $message_text = self::replace_placeholders($settings['zalo_customer_msg'], $data);
        $mode = $settings['zalo_mode'];

        if ($mode === 'simulation') {
            return array(
                'status'  => 'simulated',
                'mode'    => 'simulation',
                'message' => sprintf('Gửi thành công tới khách hàng %s (%s): "%s"', $data['guest_name'], self::format_phone_display($phone_raw), $message_text),
            );
        }

        if ($mode === 'zns') {
            return self::send_zns_request(
                $phone_norm, 
                $settings['zns_template_customer'], 
                array(
                    'customer_name' => $data['guest_name'],
                    'booking_code'  => $data['booking_code'],
                    'room_name'     => $data['room_name'],
                    'checkin'       => $data['checkin'],
                    'checkout'      => $data['checkout'],
                    'total_amount'  => number_format(floatval($data['grand_total']), 0, ',', '.') . ' đ',
                    'hotline'       => function_exists('dragonlegend_get_info') ? dragonlegend_get_info('hotline') : '0203 3518 999',
                ),
                $settings
            );
        }

        if ($mode === 'webhook') {
            return self::send_webhook_request(
                array(
                    'event'        => 'customer_booking_notification',
                    'recipient'    => 'guest',
                    'phone'        => $phone_norm,
                    'phone_display'=> self::format_phone_display($phone_raw),
                    'message'      => $message_text,
                    'booking'      => $data,
                ),
                $settings
            );
        }

        return array(
            'status'  => 'failed',
            'mode'    => $mode,
            'message' => 'Chế độ Zalo không được hỗ trợ: ' . $mode,
        );
    }

    /**
     * Gửi tin nhắn Zalo tới Admin / Sale
     */
    public static function send_message_to_admin($data, $settings) {
        $admin_phones_str = !empty($settings['admin_zalo_phones']) ? $settings['admin_zalo_phones'] : '';
        $phones = array_filter(array_map('trim', explode(',', $admin_phones_str)));

        if (empty($phones)) {
            return array(
                'status'  => 'failed',
                'mode'    => $settings['zalo_mode'],
                'message' => 'Chưa cấu hình số điện thoại Zalo của Admin/Sale',
            );
        }

        $message_text = self::replace_placeholders($settings['zalo_admin_msg'], $data);
        $mode = $settings['zalo_mode'];

        if ($mode === 'simulation') {
            return array(
                'status'  => 'simulated',
                'mode'    => 'simulation',
                'message' => sprintf('Gửi thành công tới Admin/Sale (%s): "%s"', implode(', ', $phones), $message_text),
            );
        }

        if ($mode === 'webhook') {
            return self::send_webhook_request(
                array(
                    'event'        => 'admin_booking_notification',
                    'recipient'    => 'admin_sales',
                    'phones'       => array_map(array(__CLASS__, 'normalize_phone'), $phones),
                    'message'      => $message_text,
                    'booking'      => $data,
                ),
                $settings
            );
        }

        if ($mode === 'zns') {
            // Với ZNS, gửi tin nhắn template tới từng số điện thoại admin
            $success_cnt = 0;
            $errors = array();

            foreach ($phones as $p) {
                $p_norm = self::normalize_phone($p);
                $res = self::send_zns_request(
                    $p_norm,
                    $settings['zns_template_admin'],
                    array(
                        'customer_name' => $data['guest_name'],
                        'customer_phone'=> $data['guest_phone'],
                        'booking_code'  => $data['booking_code'],
                        'room_name'     => $data['room_name'],
                        'checkin'       => $data['checkin'],
                        'checkout'      => $data['checkout'],
                        'total_amount'  => number_format(floatval($data['grand_total']), 0, ',', '.') . ' đ',
                        'note'          => !empty($data['guest_note']) ? $data['guest_note'] : 'Không',
                    ),
                    $settings
                );
                if ($res['status'] === 'success') {
                    $success_cnt++;
                } else {
                    $errors[] = $p . ': ' . $res['message'];
                }
            }

            if ($success_cnt > 0) {
                return array(
                    'status'  => 'success',
                    'mode'    => 'zns',
                    'message' => sprintf('Đã gửi ZNS tới %d/%d số Admin/Sale thành công', $success_cnt, count($phones)),
                );
            }

            return array(
                'status'  => 'failed',
                'mode'    => 'zns',
                'message' => 'Lỗi gửi ZNS Admin: ' . implode('; ', $errors),
            );
        }

        return array(
            'status'  => 'failed',
            'mode'    => $mode,
            'message' => 'Chế độ Zalo không được hỗ trợ',
        );
    }

    /**
     * Gọi API ZNS (Zalo Notification Service) chính thức qua Zalo Cloud
     */
    private static function send_zns_request($phone, $template_id, $template_data, $settings) {
        $access_token = !empty($settings['zalo_access_token']) ? trim($settings['zalo_access_token']) : '';
        if (empty($access_token)) {
            return array(
                'status'  => 'failed',
                'mode'    => 'zns',
                'message' => 'Chưa cấu hình Zalo Access Token trong cài đặt',
            );
        }

        $endpoint = 'https://business.openapi.zalo.me/message/template';
        $payload = array(
            'phone'         => $phone,
            'template_id'   => $template_id,
            'template_data' => $template_data,
            'tracking_id'   => isset($template_data['booking_code']) ? $template_data['booking_code'] : 'DLH-' . time(),
        );

        $response = wp_remote_post($endpoint, array(
            'headers' => array(
                'Content-Type' => 'application/json',
                'access_token' => $access_token,
            ),
            'body'    => wp_json_encode($payload),
            'timeout' => 15,
        ));

        if (is_wp_error($response)) {
            return array(
                'status'  => 'failed',
                'mode'    => 'zns',
                'message' => 'Lỗi kết nối ZNS: ' . $response->get_error_message(),
            );
        }

        $body = wp_remote_retrieve_body($response);
        $json = json_decode($body, true);

        if (isset($json['error']) && $json['error'] === 0) {
            return array(
                'status'  => 'success',
                'mode'    => 'zns',
                'message' => 'Gửi ZNS thành công tới ' . $phone,
                'data'    => $json,
            );
        }

        $err_msg = isset($json['message']) ? $json['message'] : 'Mã lỗi: ' . ($json['error'] ?? 'unknown');
        return array(
            'status'  => 'failed',
            'mode'    => 'zns',
            'message' => 'ZNS trả về lỗi: ' . $err_msg,
            'data'    => $json,
        );
    }

    /**
     * Gọi Webhook Endpoint (Zalo Bot Gateway / n8n / Make / Chatbot)
     */
    private static function send_webhook_request($payload, $settings) {
        $webhook_url = !empty($settings['webhook_url']) ? trim($settings['webhook_url']) : '';
        if (empty($webhook_url)) {
            return array(
                'status'  => 'failed',
                'mode'    => 'webhook',
                'message' => 'Chưa cấu hình URL Webhook trong trang cài đặt',
            );
        }

        $headers = array('Content-Type' => 'application/json');
        if (!empty($settings['webhook_secret'])) {
            $headers['X-Webhook-Secret'] = $settings['webhook_secret'];
            $headers['Authorization'] = 'Bearer ' . $settings['webhook_secret'];
        }

        $response = wp_remote_post($webhook_url, array(
            'headers' => $headers,
            'body'    => wp_json_encode($payload),
            'timeout' => 12,
        ));

        if (is_wp_error($response)) {
            return array(
                'status'  => 'failed',
                'mode'    => 'webhook',
                'message' => 'Lỗi gọi Webhook: ' . $response->get_error_message(),
            );
        }

        $code = wp_remote_retrieve_response_code($response);
        if ($code >= 200 && $code < 300) {
            return array(
                'status'  => 'success',
                'mode'    => 'webhook',
                'message' => sprintf('Webhook tiếp nhận thành công (HTTP %d)', $code),
            );
        }

        return array(
            'status'  => 'failed',
            'mode'    => 'webhook',
            'message' => sprintf('Webhook trả về mã lỗi HTTP %d', $code),
        );
    }

    /**
     * Gửi tin nhắn thử nghiệm (Test Tool)
     */
    public static function send_test_message($target_phone, $test_message = '') {
        $settings = self::get_settings();
        $target_norm = self::normalize_phone($target_phone);

        if (empty($target_norm)) {
            return array('success' => false, 'message' => 'Số điện thoại không hợp lệ');
        }

        $msg = !empty($test_message) ? $test_message : 'Đây là tin nhắn thử nghiệm thông báo từ hệ thống Dragon Legend Hotel lúc ' . current_time('H:i d/m/Y');
        $mode = $settings['zalo_mode'];

        if ($mode === 'simulation') {
            return array(
                'success' => true,
                'message' => sprintf('[Mô phỏng thành công] Tin nhắn test tới %s (%s): "%s"', $target_phone, $target_norm, $msg),
            );
        }

        if ($mode === 'webhook') {
            $res = self::send_webhook_request(array(
                'event'        => 'test_notification',
                'phone'        => $target_norm,
                'phone_display'=> $target_phone,
                'message'      => $msg,
            ), $settings);

            return array(
                'success' => ($res['status'] === 'success'),
                'message' => $res['message'],
            );
        }

        if ($mode === 'zns') {
            $res = self::send_zns_request(
                $target_norm,
                $settings['zns_template_customer'],
                array(
                    'customer_name' => 'Khách Hàng Test',
                    'booking_code'  => 'DLH-TEST',
                    'room_name'     => 'Phòng Superior Thử Nghiệm',
                    'checkin'       => date('d/m/Y'),
                    'checkout'      => date('d/m/Y', strtotime('+1 day')),
                    'total_amount'  => '1.000.000 đ',
                    'hotline'       => function_exists('dragonlegend_get_info') ? dragonlegend_get_info('hotline') : '0203 3518 999',
                ),
                $settings
            );

            return array(
                'success' => ($res['status'] === 'success'),
                'message' => $res['message'],
            );
        }

        return array('success' => false, 'message' => 'Chế độ Zalo không hợp lệ');
    }

    /**
     * Ghi nhật ký vào post meta của đơn đặt phòng
     */
    public static function log_booking_notification($booking_id, $channel, $detail) {
        $logs = get_post_meta($booking_id, '_dl_booking_notification_logs', true);
        if (!is_array($logs)) {
            $logs = array();
        }

        $logs[] = array(
            'time'    => current_time('mysql'),
            'channel' => $channel,
            'detail'  => $detail,
        );

        update_post_meta($booking_id, '_dl_booking_notification_logs', $logs);
    }

    /**
     * Gửi tin nhắn qua Telegram Bot API (Hỗ trợ nhiều Chat ID cách nhau bằng dấu phẩy)
     */
    public static function send_telegram_message($message, $bot_token = '', $chat_ids_str = '') {
        $settings = self::get_settings();
        if (empty($bot_token)) {
            $bot_token = !empty($settings['telegram_bot_token']) ? trim($settings['telegram_bot_token']) : '';
        }
        if (empty($chat_ids_str)) {
            $chat_ids_str = !empty($settings['telegram_chat_id']) ? trim($settings['telegram_chat_id']) : '';
        }

        if (empty($bot_token) || empty($chat_ids_str)) {
            return array(
                'success' => false,
                'status'  => 'failed',
                'message' => 'Chưa cấu hình đầy đủ Telegram Bot Token hoặc Chat ID.',
            );
        }

        $chat_ids = array_filter(array_map('trim', explode(',', $chat_ids_str)));
        if (empty($chat_ids)) {
            return array(
                'success' => false,
                'status'  => 'failed',
                'message' => 'Chat ID Telegram không hợp lệ.',
            );
        }

        $endpoint = 'https://api.telegram.org/bot' . $bot_token . '/sendMessage';
        $success_count = 0;
        $errors = array();

        foreach ($chat_ids as $chat_id) {
            $response = wp_remote_post($endpoint, array(
                'headers' => array('Content-Type' => 'application/json; charset=utf-8'),
                'body'    => wp_json_encode(array(
                    'chat_id'                  => $chat_id,
                    'text'                     => $message,
                    'parse_mode'               => 'HTML',
                    'disable_web_page_preview' => true,
                )),
                'timeout' => 15,
            ));

            if (is_wp_error($response)) {
                $errors[] = $chat_id . ': ' . $response->get_error_message();
                continue;
            }

            $body = wp_remote_retrieve_body($response);
            $json = json_decode($body, true);

            if (!empty($json['ok'])) {
                $success_count++;
            } else {
                $desc = isset($json['description']) ? $json['description'] : 'Lỗi không xác định từ Telegram API';
                $errors[] = $chat_id . ': ' . $desc;
            }
        }

        if ($success_count > 0) {
            return array(
                'success' => true,
                'status'  => 'success',
                'message' => sprintf('Đã gửi Telegram thành công tới %d/%d Chat ID (%s)', $success_count, count($chat_ids), implode(', ', $chat_ids)),
            );
        }

        return array(
            'success' => false,
            'status'  => 'failed',
            'message' => 'Lỗi gửi Telegram: ' . implode(' | ', $errors),
        );
    }

    /**
     * Gửi thông báo Telegram khi có đơn đặt phòng mới
     */
    public static function send_booking_telegram_notification($booking_id, $data) {
        $settings = self::get_settings();
        if (empty($settings['telegram_enabled']) || $settings['telegram_enabled'] !== '1' || (isset($settings['telegram_notify_booking']) && $settings['telegram_notify_booking'] === '0')) {
            self::log_booking_notification($booking_id, 'Telegram', 'Bỏ qua (Tính năng Telegram đang tắt)');
            update_post_meta($booking_id, '_dl_booking_telegram_admin_status', 'disabled');
            return false;
        }

        $checkin_fmt  = !empty($data['checkin']) ? date_i18n('d/m/Y', strtotime($data['checkin'])) : '';
        $checkout_fmt = !empty($data['checkout']) ? date_i18n('d/m/Y', strtotime($data['checkout'])) : '';
        $grand_total_str = isset($data['grand_total']) 
            ? (is_numeric($data['grand_total']) ? number_format(floatval($data['grand_total']), 0, ',', '.') . ' VNĐ' : $data['grand_total'])
            : '0 VNĐ';
        $admin_link = !empty($booking_id) 
            ? admin_url('post.php?post=' . intval($booking_id) . '&action=edit')
            : admin_url('edit.php?post_type=dl_booking');
        $zalo_link = 'https://zalo.me/' . self::normalize_phone($data['guest_phone'] ?? '');

        $lines = array(
            "🔔 <b>[ĐƠN ĐẶT PHÒNG MỚI - DRAGON LEGEND HOTEL]</b>",
            "━━━━━━━━━━━━━━━━━━",
            "🔖 <b>Mã đặt phòng:</b> <code>" . esc_html($data['booking_code'] ?? '') . "</code>",
            "👤 <b>Khách hàng:</b> " . esc_html($data['guest_name'] ?? ''),
            "📞 <b>Điện thoại:</b> " . esc_html(self::format_phone_display($data['guest_phone'] ?? '')) . " (<a href=\"" . esc_url($zalo_link) . "\">Chat Zalo</a>)",
            "✉️ <b>Email:</b> " . esc_html(!empty($data['guest_email']) ? $data['guest_email'] : 'Không cung cấp'),
            "🏨 <b>Hạng phòng:</b> <b>" . esc_html($data['room_name'] ?? '') . "</b>",
            "🛏️ <b>Số lượng:</b> " . intval($data['rooms'] ?? 1) . " phòng | 👥 " . intval($data['adults'] ?? 2) . " người lớn, " . intval($data['children'] ?? 0) . " trẻ em",
            "📅 <b>Lưu trú:</b> " . esc_html($checkin_fmt) . " ➔ " . esc_html($checkout_fmt) . " (" . intval($data['nights'] ?? 1) . " đêm)",
        );

        if (!empty($data['promo_code'])) {
            $lines[] = "🎟️ <b>Mã ưu đãi:</b> <code>" . esc_html($data['promo_code']) . "</code>";
        }

        $lines[] = "💰 <b>Tổng cộng:</b> <b>" . esc_html($grand_total_str) . "</b>";
        $lines[] = "📝 <b>Ghi chú:</b> " . esc_html(!empty($data['guest_note']) ? $data['guest_note'] : 'Không có');
        $lines[] = "━━━━━━━━━━━━━━━━━━";
        $lines[] = "👉 <a href=\"" . esc_url($admin_link) . "\">Xem & Xử lý đơn phòng trong WP-Admin</a>";

        $message = implode("\n", $lines);
        $res = self::send_telegram_message($message);

        update_post_meta($booking_id, '_dl_booking_telegram_admin_status', $res['status']);
        self::log_booking_notification($booking_id, 'Telegram Admin/Sale', $res['message']);

        return $res;
    }

    /**
     * Gửi thông báo qua Telegram & Zalo khi khách gửi Form Liên Hệ (/lien-he/)
     */
    public static function send_contact_notifications($contact_data) {
        $settings = self::get_settings();
        $results  = array();

        $name    = $contact_data['name'] ?? '';
        $phone   = $contact_data['phone'] ?? '';
        $email   = !empty($contact_data['email']) ? $contact_data['email'] : 'Không cung cấp';
        $subject = $contact_data['subject'] ?? 'Liên hệ từ website';
        $message = $contact_data['message'] ?? '';
        $time    = current_time('H:i d/m/Y');

        // 1. Gửi thông báo Telegram nếu bật
        if (!empty($settings['telegram_enabled']) && $settings['telegram_enabled'] === '1' && (!isset($settings['telegram_notify_contact']) || $settings['telegram_notify_contact'] === '1')) {
            $zalo_link = 'https://zalo.me/' . self::normalize_phone($phone);
            $tg_lines = array(
                "📩 <b>[LIÊN HỆ & TƯ VẤN MỚI - DRAGON LEGEND HOTEL]</b>",
                "━━━━━━━━━━━━━━━━━━",
                "👤 <b>Họ và tên:</b> " . esc_html($name),
                "📞 <b>Điện thoại:</b> " . esc_html(self::format_phone_display($phone)) . " (<a href=\"" . esc_url($zalo_link) . "\">Chat Zalo</a>)",
                "✉️ <b>Email:</b> " . esc_html($email),
                "📌 <b>Nhu cầu tư vấn:</b> <b>" . esc_html($subject) . "</b>",
                "📝 <b>Nội dung yêu cầu:</b>\n" . esc_html($message),
                "━━━━━━━━━━━━━━━━━━",
                "🕒 <i>Thời gian gửi: " . esc_html($time) . "</i>",
            );
            $results['telegram'] = self::send_telegram_message(implode("\n", $tg_lines));
        }

        // 2. Gửi thông báo Zalo tới Admin/Sale nếu bật
        if (!empty($settings['zalo_enabled']) && $settings['zalo_enabled'] === '1' && (!isset($settings['zalo_notify_contact']) || $settings['zalo_notify_contact'] === '1')) {
            $admin_phones_str = !empty($settings['admin_zalo_phones']) ? $settings['admin_zalo_phones'] : '';
            $phones = array_filter(array_map('trim', explode(',', $admin_phones_str)));

            $tpl = !empty($settings['zalo_contact_admin_msg']) 
                ? $settings['zalo_contact_admin_msg'] 
                : "📩 [LIÊN HỆ MỚI TỪ WEBSITE] Khách hàng: {guest_name} ({guest_phone}) | Email: {guest_email} | Chủ đề: {subject} | Nội dung: {message}";

            $zalo_msg = str_replace(
                array('{guest_name}', '{guest_phone}', '{guest_email}', '{subject}', '{message}'),
                array($name, self::format_phone_display($phone), $email, $subject, $message),
                $tpl
            );

            $mode = $settings['zalo_mode'] ?? 'simulation';
            if ($mode === 'simulation') {
                $results['zalo'] = array(
                    'status'  => 'simulated',
                    'message' => sprintf('Đã mô phỏng gửi Zalo Liên hệ tới Admin (%s): %s', implode(', ', $phones), $zalo_msg),
                );
            } elseif ($mode === 'webhook') {
                $results['zalo'] = self::send_webhook_request(array(
                    'event'     => 'admin_contact_notification',
                    'recipient' => 'admin_sales',
                    'phones'    => array_map(array(__CLASS__, 'normalize_phone'), $phones),
                    'message'   => $zalo_msg,
                    'contact'   => $contact_data,
                ), $settings);
            } elseif ($mode === 'zns' && !empty($phones)) {
                foreach ($phones as $p) {
                    $p_norm = self::normalize_phone($p);
                    $results['zalo'] = self::send_zns_request(
                        $p_norm,
                        $settings['zns_template_admin'],
                        array(
                            'customer_name'  => $name,
                            'customer_phone' => $phone,
                            'booking_code'   => 'LIEN-HE',
                            'room_name'      => $subject,
                            'checkin'        => date('d/m/Y'),
                            'checkout'       => date('d/m/Y'),
                            'total_amount'   => 'Tư vấn dịch vụ',
                            'note'           => mb_substr($message, 0, 100),
                        ),
                        $settings
                    );
                }
            }
        }

        return $results;
    }

    /**
     * Gửi tin nhắn Telegram thử nghiệm từ trang Cài đặt
     */
    public static function send_test_telegram($bot_token = '', $chat_id = '', $custom_msg = '') {
        $hotel_name = get_bloginfo('name') ?: 'Dragon Legend Hotel';
        $text = !empty($custom_msg) 
            ? $custom_msg 
            : "✅ <b>[KẾT NỐI TELEGRAM THÀNH CÔNG]</b>\nHệ thống thông báo tự động (Đặt phòng & Liên hệ) của <b>" . esc_html($hotel_name) . "</b> đã được liên kết thành công lúc " . current_time('H:i:s d/m/Y') . "!";
        return self::send_telegram_message($text, $bot_token, $chat_id);
    }
}
