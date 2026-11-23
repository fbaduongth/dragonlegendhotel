<?php
/**
 * Dragon Legend Hotel Core - Booking AJAX Handler & Notifications
 * Tích hợp gửi tin Zalo (Admin & Khách hàng) và Email HTML cao cấp cho Quản trị viên
 *
 * @package DragonLegendCore
 */

if (!defined('ABSPATH')) {
    exit;
}

class DragonLegend_Booking_Handler {

    public static function init() {
        add_action('wp_ajax_dl_submit_booking', array(__CLASS__, 'handle_booking_submission'));
        add_action('wp_ajax_nopriv_dl_submit_booking', array(__CLASS__, 'handle_booking_submission'));
        add_action('wp_ajax_dl_resend_booking_notifications', array(__CLASS__, 'handle_resend_notifications'));
    }

    /**
     * Xử lý AJAX Tiếp nhận đơn đặt phòng từ Website
     * Không dùng check_ajax_referer die(-1) để tương thích với Super Page Cache
     */
    public static function handle_booking_submission() {
        $guest_name  = isset($_POST['guest_name']) ? sanitize_text_field($_POST['guest_name']) : '';
        $guest_phone = isset($_POST['guest_phone']) ? sanitize_text_field($_POST['guest_phone']) : '';
        $guest_email = isset($_POST['guest_email']) ? sanitize_email($_POST['guest_email']) : '';
        $guest_note  = isset($_POST['guest_note']) ? sanitize_textarea_field($_POST['guest_note']) : '';

        if (empty($guest_name) || empty($guest_phone)) {
            wp_send_json_error(array(
                'message' => __('Vui lòng nhập họ tên và số điện thoại liên hệ.', 'dragonlegend-core')
            ));
        }

        $room_id_or_slug = isset($_POST['room_id']) ? sanitize_text_field($_POST['room_id']) : '';
        $checkin         = isset($_POST['checkin']) ? sanitize_text_field($_POST['checkin']) : date('Y-m-d');
        $checkout        = isset($_POST['checkout']) ? sanitize_text_field($_POST['checkout']) : date('Y-m-d', strtotime('+1 day'));
        $rooms           = isset($_POST['rooms']) ? max(1, intval($_POST['rooms'])) : 1;
        $adults          = isset($_POST['adults']) ? max(1, intval($_POST['adults'])) : 2;
        $children        = isset($_POST['children']) ? max(0, intval($_POST['children'])) : 0;
        $promo_code      = isset($_POST['promo_code']) ? strtoupper(sanitize_text_field($_POST['promo_code'])) : '';

        // Tính số đêm lưu trú
        $d1 = new DateTime($checkin);
        $d2 = new DateTime($checkout);
        $diff = $d1->diff($d2);
        $nights = max(1, $diff->days);

        // Lấy thông tin phòng
        $room_name = 'Superior Twin/ Double Room';
        $price_per_night = 1050000;

        if (is_numeric($room_id_or_slug) && intval($room_id_or_slug) > 0) {
            $room_post = get_post(intval($room_id_or_slug));
            if ($room_post && $room_post->post_type === 'dl_room') {
                $room_name = html_entity_decode($room_post->post_title, ENT_QUOTES, 'UTF-8');
                $saved_price = get_post_meta($room_post->ID, '_dl_room_price', true);
                if ($saved_price) {
                    $price_per_night = floatval($saved_price);
                }
            }
        } else {
            // Tìm theo slug hoặc name
            $args = array(
                'post_type'      => 'dl_room',
                'name'           => $room_id_or_slug,
                'posts_per_page' => 1,
            );
            $query = new WP_Query($args);
            if ($query->have_posts()) {
                $p = $query->posts[0];
                $room_name = html_entity_decode($p->post_title, ENT_QUOTES, 'UTF-8');
                $saved_price = get_post_meta($p->ID, '_dl_room_price', true);
                if ($saved_price) {
                    $price_per_night = floatval($saved_price);
                }
            }
        }

        // Tính toán chi phí
        $base_subtotal = $price_per_night * $nights * $rooms;
        $discount_amount = 0;
        $discount_percent = 0;
        $discount_type = 'percent';

        if (!empty($promo_code)) {
            $coupon = null;
            if (class_exists('DragonLegend_Coupons')) {
                $coupon = DragonLegend_Coupons::verify_coupon($promo_code, $base_subtotal, $room_id_or_slug);
            } elseif (class_exists('DragonLegend_Coupon_Manager')) {
                $coupon = DragonLegend_Coupon_Manager::verify_coupon($promo_code, $base_subtotal, $room_id_or_slug);
            }

            if ($coupon && !empty($coupon['valid'])) {
                $discount_type    = $coupon['discount_type'] ?? 'percent';
                $discount_amount  = floatval($coupon['discount_amount'] ?? 0);
                $discount_percent = intval($coupon['percent'] ?? ($discount_type === 'percent' ? ($coupon['discount_value'] ?? 0) : 0));

                // Ghi nhận lượt sử dụng vào CPT dl_coupon
                if (class_exists('DragonLegend_Coupons')) {
                    DragonLegend_Coupons::record_usage($promo_code);
                }
            }
        }

        $discount_amount = min($base_subtotal, max(0, $discount_amount));
        $after_discount  = max(0, $base_subtotal - $discount_amount);
        $tax_and_service = round($after_discount * 0.08); // 8% VAT & phí PV tính trên số tiền sau chiết khấu
        $grand_total     = $after_discount + $tax_and_service;

        // Sinh mã đặt phòng độc nhất
        $booking_code = 'DLH-' . rand(100000, 999999);

        // Tạo bài viết CPT dl_booking
        $post_id = wp_insert_post(array(
            'post_type'    => 'dl_booking',
            'post_title'   => $booking_code,
            'post_status'  => 'publish',
            'post_author'  => 1,
        ));

        if (is_wp_error($post_id)) {
            wp_send_json_error(array('message' => __('Không thể tạo đơn đặt phòng, vui lòng thử lại.', 'dragonlegend-core')));
        }

        // Lưu toàn bộ thông tin đơn đặt phòng vào Post Meta
        update_post_meta($post_id, '_dl_booking_guest_name', $guest_name);
        update_post_meta($post_id, '_dl_booking_guest_phone', $guest_phone);
        update_post_meta($post_id, '_dl_booking_guest_email', $guest_email);
        update_post_meta($post_id, '_dl_booking_guest_note', $guest_note);
        update_post_meta($post_id, '_dl_booking_room_id', $room_id_or_slug);
        update_post_meta($post_id, '_dl_booking_room_name', $room_name);
        update_post_meta($post_id, '_dl_booking_price_per_night', $price_per_night);
        update_post_meta($post_id, '_dl_booking_checkin', $checkin);
        update_post_meta($post_id, '_dl_booking_checkout', $checkout);
        update_post_meta($post_id, '_dl_booking_nights', $nights);
        update_post_meta($post_id, '_dl_booking_rooms', $rooms);
        update_post_meta($post_id, '_dl_booking_adults', $adults);
        update_post_meta($post_id, '_dl_booking_children', $children);
        update_post_meta($post_id, '_dl_booking_promo_code', $promo_code);
        update_post_meta($post_id, '_dl_booking_discount_type', $discount_type);
        update_post_meta($post_id, '_dl_booking_discount_percent', $discount_percent);
        update_post_meta($post_id, '_dl_booking_discount_amount', $discount_amount);
        update_post_meta($post_id, '_dl_booking_tax_service', $tax_and_service);
        update_post_meta($post_id, '_dl_booking_grand_total', $grand_total);
        update_post_meta($post_id, '_dl_booking_status', 'pending');

        $booking_data = array(
            'booking_id'       => $post_id,
            'booking_code'     => $booking_code,
            'guest_name'       => $guest_name,
            'guest_phone'      => $guest_phone,
            'guest_email'      => $guest_email,
            'guest_note'       => $guest_note,
            'room_name'        => $room_name,
            'checkin'          => $checkin,
            'checkout'         => $checkout,
            'nights'           => $nights,
            'rooms'            => $rooms,
            'adults'           => $adults,
            'children'         => $children,
            'promo_code'       => $promo_code,
            'discount_type'    => $discount_type,
            'discount_percent' => $discount_percent,
            'discount'         => $discount_amount,
            'grand_total'      => $grand_total,
        );

        // 1. GỬI TIN NHẮN ZALO & TELEGRAM (Khách hàng & Admin/Sale)
        if (class_exists('DragonLegend_Zalo_Service')) {
            DragonLegend_Zalo_Service::send_booking_notifications($post_id, $booking_data);
            DragonLegend_Zalo_Service::send_booking_telegram_notification($post_id, $booking_data);
        }

        // 2. GỬI EMAIL THÔNG BÁO CHO QUẢN TRỊ VIÊN & KHÁCH HÀNG
        self::send_notification_emails($post_id, $booking_code, $booking_data);

        wp_send_json_success(array(
            'booking_code' => $booking_code,
            'guest_name'   => $guest_name,
            'guest_phone'  => $guest_phone,
            'room_name'    => $room_name,
            'checkin'      => $checkin,
            'checkout'     => $checkout,
            'nights'       => $nights,
            'rooms'        => $rooms,
            'grand_total'  => number_format($grand_total, 0, ',', '.') . ' VNĐ',
            'message'      => __('Yêu cầu đặt phòng đã được ghi nhận thành công!', 'dragonlegend-core'),
        ));
    }

    /**
     * Gửi email HTML thông báo đặt phòng
     */
    public static function send_notification_emails($post_id, $code, $data) {
        $settings = class_exists('DragonLegend_Zalo_Service') 
            ? DragonLegend_Zalo_Service::get_settings() 
            : array('email_enabled' => '1', 'admin_notification_emails' => get_option('admin_email'));

        if (empty($settings['email_enabled'])) {
            if (class_exists('DragonLegend_Zalo_Service')) {
                DragonLegend_Zalo_Service::log_booking_notification($post_id, 'Email', 'Bỏ qua (Tính năng Email đang tắt)');
            }
            update_post_meta($post_id, '_dl_booking_email_admin_status', 'disabled');
            return false;
        }

        $admin_emails_str = !empty($settings['admin_notification_emails']) ? $settings['admin_notification_emails'] : get_option('admin_email');
        $admin_emails = array_filter(array_map('trim', explode(',', $admin_emails_str)));

        if (empty($admin_emails)) {
            $admin_emails = array(get_option('admin_email'));
        }

        // 1. Gửi Email cho Quản trị viên
        $hotel_name = get_bloginfo('name') ?: 'Dragon Legend Hotel';
        $subject_tpl = !empty($settings['email_subject_template']) 
            ? $settings['email_subject_template'] 
            : sprintf('[%s] Đơn đặt phòng mới: {booking_code} - {guest_name}', $hotel_name);
        
        $subject = str_replace(
            array('{booking_code}', '{guest_name}', '{room_name}', '{hotel_name}'),
            array($code, $data['guest_name'], $data['room_name'], $hotel_name),
            $subject_tpl
        );

        $html_body = self::build_admin_html_email($code, $data);
        $headers = array('Content-Type: text/html; charset=UTF-8');

        $sent = @wp_mail($admin_emails, $subject, $html_body, $headers);

        update_post_meta($post_id, '_dl_booking_email_admin_status', $sent ? 'success' : 'failed');
        if (class_exists('DragonLegend_Zalo_Service')) {
            DragonLegend_Zalo_Service::log_booking_notification(
                $post_id, 
                'Email Quản trị', 
                $sent ? sprintf('Đã gửi thư thông báo tới: %s', implode(', ', $admin_emails)) : 'Gửi thư thất bại (wp_mail trả về false)'
            );
        }

        // 2. Gửi Email cho Khách hàng (nếu khách có điền email)
        if (!empty($data['guest_email']) && !empty($settings['customer_email_enabled'])) {
            $guest_subject = sprintf('Xác nhận yêu cầu đặt phòng tại %s - Mã đơn: %s', $hotel_name, $code);
            $guest_body = self::build_customer_html_email($code, $data);
            $guest_sent = @wp_mail($data['guest_email'], $guest_subject, $guest_body, $headers);

            update_post_meta($post_id, '_dl_booking_email_guest_status', $guest_sent ? 'success' : 'failed');
            if (class_exists('DragonLegend_Zalo_Service')) {
                DragonLegend_Zalo_Service::log_booking_notification(
                    $post_id, 
                    'Email Khách hàng', 
                    $guest_sent ? sprintf('Đã gửi thư xác nhận tới khách: %s', $data['guest_email']) : 'Gửi thư cho khách thất bại'
                );
            }
        }

        return $sent;
    }

    /**
     * Tạo mẫu thư HTML thương hiệu cao cấp cho Quản trị viên
     */
    public static function build_admin_html_email($code, $data) {
        $hotel_name = get_bloginfo('name') ?: 'Dragon Legend Hotel';
        $hotel_phone = function_exists('dragonlegend_get_info') ? dragonlegend_get_info('hotline') : '0203 3518 999';
        $hotel_address = function_exists('dragonlegend_get_info') ? dragonlegend_get_info('address') : 'Đại lộ Hạ Long Marine, Bãi Cháy, TP. Hạ Long';
        $logo_url = 'https://dragonlegendhotel.com/wp-content/uploads/2015/07/logoweb-1.png';

        $admin_url = !empty($data['booking_id']) 
            ? admin_url('post.php?post=' . intval($data['booking_id']) . '&action=edit')
            : admin_url('edit.php?post_type=dl_booking');

        $checkin_fmt = !empty($data['checkin']) ? date_i18n('d/m/Y', strtotime($data['checkin'])) : '';
        $checkout_fmt = !empty($data['checkout']) ? date_i18n('d/m/Y', strtotime($data['checkout'])) : '';

        $grand_total_str = is_numeric($data['grand_total']) 
            ? number_format(floatval($data['grand_total']), 0, ',', '.') . ' VNĐ' 
            : $data['grand_total'];

        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="vi">
        <head>
          <meta charset="UTF-8">
          <meta name="viewport" content="width=device-width, initial-scale=1.0">
          <title>Thông báo đơn đặt phòng mới</title>
        </head>
        <body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #334155; line-height: 1.6;">
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f1f5f9; padding: 30px 15px;">
            <tr>
              <td align="center">
                <table role="presentation" width="100%" style="max-width: 650px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;" cellspacing="0" cellpadding="0">
                  
                  <!-- HEADER LUXURY NAVY & GOLD -->
                  <tr>
                    <td style="background: linear-gradient(135deg, #07111e 0%, #0d1e33 100%); padding: 30px; text-align: center; border-bottom: 3px solid #c5a880;">
                      <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($hotel_name); ?>" width="160" style="max-height: 48px; width: auto; margin-bottom: 12px; display: inline-block;">
                      <span style="display: block; font-size: 11px; text-transform: uppercase; letter-spacing: 2px; color: #c5a880; font-weight: 700;">HỆ THỐNG ĐẶT PHÒNG TRỰC TUYẾN</span>
                      <h1 style="color: #ffffff; font-size: 22px; font-weight: 700; margin: 6px 0 0 0;">🔔 CÓ ĐƠN ĐẶT PHÒNG MỚI</h1>
                    </td>
                  </tr>

                  <!-- BANNER MÃ ĐƠN -->
                  <tr>
                    <td style="background-color: #fffbeb; padding: 16px 30px; border-bottom: 1px solid #fef3c7; text-align: center;">
                      <span style="font-size: 12px; color: #92400e; font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">Mã đặt phòng:</span>
                      <span style="display: inline-block; font-family: monospace; font-size: 20px; font-weight: 700; color: #07111e; background-color: #fef3c7; padding: 4px 14px; border-radius: 6px; margin-left: 8px; border: 1px solid #fde68a;">
                        <?php echo esc_html($code); ?>
                      </span>
                    </td>
                  </tr>

                  <!-- BODY CONTENT -->
                  <tr>
                    <td style="padding: 30px;">
                      <p style="margin-top: 0; font-size: 14px; color: #475569;">
                        Kính gửi Ban Quản Trị & Bộ Phận Đặt Phòng <strong><?php echo esc_html($hotel_name); ?></strong>,<br>
                        Hệ thống vừa ghi nhận một yêu cầu đặt phòng mới từ website với thông tin chi tiết dưới đây:
                      </p>

                      <!-- BẢNG 1: THÔNG TIN KHÁCH HÀNG -->
                      <h3 style="font-size: 15px; color: #07111e; border-left: 3px solid #c5a880; padding-left: 10px; margin: 24px 0 12px 0; text-transform: uppercase; letter-spacing: 0.5px;">1. Thông Tin Khách Hàng</h3>
                      <table width="100%" cellspacing="0" cellpadding="8" style="font-size: 13px; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 20px;">
                        <tr style="background-color: #f8fafc;">
                          <td width="35%" style="color: #64748b; font-weight: 600;">Họ và tên:</td>
                          <td style="color: #0f172a; font-weight: 700; font-size: 14px;"><?php echo esc_html($data['guest_name']); ?></td>
                        </tr>
                        <tr>
                          <td style="color: #64748b; font-weight: 600;">Số điện thoại:</td>
                          <td>
                            <a href="tel:<?php echo esc_attr($data['guest_phone']); ?>" style="color: #0284c7; font-weight: 700; text-decoration: none;">
                              📞 <?php echo esc_html($data['guest_phone']); ?>
                            </a>
                            <a href="https://zalo.me/<?php echo esc_attr(DragonLegend_Zalo_Service::normalize_phone($data['guest_phone'])); ?>" target="_blank" style="margin-left: 10px; display: inline-block; padding: 2px 8px; background: #0068ff; color: #fff; border-radius: 4px; font-size: 11px; text-decoration: none; font-weight: 600;">Chat Zalo</a>
                          </td>
                        </tr>
                        <tr style="background-color: #f8fafc;">
                          <td style="color: #64748b; font-weight: 600;">Địa chỉ Email:</td>
                          <td><?php echo !empty($data['guest_email']) ? '<a href="mailto:' . esc_attr($data['guest_email']) . '" style="color: #0284c7; text-decoration: none;">' . esc_html($data['guest_email']) . '</a>' : '<span style="color:#94a3b8; font-style:italic;">Không cung cấp</span>'; ?></td>
                        </tr>
                        <tr>
                          <td style="color: #64748b; font-weight: 600;">Ghi chú đặc biệt:</td>
                          <td style="color: #d97706; font-style: italic;"><?php echo !empty($data['guest_note']) ? esc_html($data['guest_note']) : 'Không có ghi chú nào'; ?></td>
                        </tr>
                      </table>

                      <!-- BẢNG 2: THÔNG TIN ĐẶT PHÒNG -->
                      <h3 style="font-size: 15px; color: #07111e; border-left: 3px solid #c5a880; padding-left: 10px; margin: 24px 0 12px 0; text-transform: uppercase; letter-spacing: 0.5px;">2. Chi Tiết Đặt Phòng & Doanh Thu</h3>
                      <table width="100%" cellspacing="0" cellpadding="8" style="font-size: 13px; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 24px;">
                        <tr style="background-color: #f8fafc;">
                          <td width="35%" style="color: #64748b; font-weight: 600;">Hạng phòng:</td>
                          <td style="color: #07111e; font-weight: 700;"><?php echo esc_html($data['room_name']); ?></td>
                        </tr>
                        <tr>
                          <td style="color: #64748b; font-weight: 600;">Số lượng phòng:</td>
                          <td><strong><?php echo intval($data['rooms']); ?></strong> phòng</td>
                        </tr>
                        <tr style="background-color: #f8fafc;">
                          <td style="color: #64748b; font-weight: 600;">Thời gian lưu trú:</td>
                          <td>
                            <strong><?php echo esc_html($checkin_fmt); ?></strong> ➔ <strong><?php echo esc_html($checkout_fmt); ?></strong>
                            <span style="color: #c5a880; font-weight: 600; margin-left: 6px;">(<?php echo intval($data['nights']); ?> đêm)</span>
                          </td>
                        </tr>
                        <tr>
                          <td style="color: #64748b; font-weight: 600;">Số lượng khách:</td>
                          <td><?php echo intval($data['adults']); ?> Người lớn <?php if (!empty($data['children'])) echo ', ' . intval($data['children']) . ' Trẻ em'; ?></td>
                        </tr>
                        <?php if (!empty($data['promo_code'])) : 
                          $disc_type = $data['discount_type'] ?? 'percent';
                          $disc_amt  = floatval($data['discount'] ?? 0);
                        ?>
                        <tr style="background-color: #f0fdf4;">
                          <td style="color: #166534; font-weight: 600;">Mã ưu đãi áp dụng:</td>
                          <td style="color: #166534; font-weight: 700;">
                            <?php echo esc_html($data['promo_code']); ?> 
                            (Giảm: -<?php echo number_format($disc_amt, 0, ',', '.'); ?> VNĐ<?php if ($disc_type === 'percent' && !empty($data['discount_percent'])) echo ' ~ ' . intval($data['discount_percent']) . '%'; ?>)
                          </td>
                        </tr>
                        <?php endif; ?>
                        <tr style="background-color: #f8fafc;">
                          <td style="color: #64748b; font-weight: 700; font-size: 14px;">Tổng tiền dự kiến:</td>
                          <td style="color: #dc2626; font-size: 17px; font-weight: 800;">
                            <?php echo esc_html($grand_total_str); ?>
                            <span style="font-size: 11px; color: #64748b; font-weight: normal; display: block;">(Đã gồm 8% VAT & phí phục vụ)</span>
                          </td>
                        </tr>
                      </table>

                      <!-- NÚT CTA DUYỆT ĐƠN -->
                      <div style="text-align: center; margin: 30px 0 10px 0;">
                        <a href="<?php echo esc_url($admin_url); ?>" target="_blank" style="display: inline-block; background: linear-gradient(135deg, #c5a880 0%, #b59265 100%); color: #07111e; font-weight: 700; font-size: 14px; text-transform: uppercase; letter-spacing: 1px; padding: 14px 30px; text-decoration: none; border-radius: 30px; box-shadow: 0 4px 15px rgba(197, 168, 128, 0.4);">
                          👉 Xem & Xử Lý Đơn Phòng Trong WP-Admin
                        </a>
                      </div>

                    </td>
                  </tr>

                  <!-- FOOTER -->
                  <tr>
                    <td style="background-color: #f8fafc; padding: 20px 30px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 12px; color: #64748b;">
                      <p style="margin: 0 0 4px 0; font-weight: 600; color: #07111e;"><?php echo esc_html($hotel_name); ?></p>
                      <p style="margin: 0 0 4px 0;">Địa chỉ: <?php echo esc_html($hotel_address); ?> | Hotline: <?php echo esc_html($hotel_phone); ?></p>
                      <p style="margin: 0; color: #94a3b8; font-size: 11px;">Thư thông báo tự động từ hệ thống website lúc <?php echo current_time('H:i d/m/Y'); ?>.</p>
                    </td>
                  </tr>

                </table>
              </td>
            </tr>
          </table>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }

    /**
     * Tạo mẫu thư HTML xác nhận gửi cho Khách hàng
     */
    public static function build_customer_html_email($code, $data) {
        $hotel_name = get_bloginfo('name') ?: 'Dragon Legend Hotel';
        $hotel_phone = function_exists('dragonlegend_get_info') ? dragonlegend_get_info('hotline') : '0203 3518 999';
        $hotel_address = function_exists('dragonlegend_get_info') ? dragonlegend_get_info('address') : 'Đại lộ Hạ Long Marine, Bãi Cháy, TP. Hạ Long';
        $logo_url = 'https://dragonlegendhotel.com/wp-content/uploads/2015/07/logoweb-1.png';

        $checkin_fmt = !empty($data['checkin']) ? date_i18n('d/m/Y', strtotime($data['checkin'])) : '';
        $checkout_fmt = !empty($data['checkout']) ? date_i18n('d/m/Y', strtotime($data['checkout'])) : '';

        $grand_total_str = is_numeric($data['grand_total']) 
            ? number_format(floatval($data['grand_total']), 0, ',', '.') . ' VNĐ' 
            : $data['grand_total'];

        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="vi">
        <head>
          <meta charset="UTF-8">
          <title>Xác nhận yêu cầu đặt phòng</title>
        </head>
        <body style="margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #334155; line-height: 1.6;">
          <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f1f5f9; padding: 30px 15px;">
            <tr>
              <td align="center">
                <table role="presentation" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.06); border: 1px solid #e2e8f0;" cellspacing="0" cellpadding="0">
                  <tr>
                    <td style="background: linear-gradient(135deg, #07111e 0%, #0d1e33 100%); padding: 30px; text-align: center; border-bottom: 3px solid #c5a880;">
                      <img src="<?php echo esc_url($logo_url); ?>" alt="<?php echo esc_attr($hotel_name); ?>" width="150" style="max-height: 45px; width: auto; margin-bottom: 10px; display: inline-block;">
                      <h1 style="color: #ffffff; font-size: 20px; font-weight: 700; margin: 4px 0 0 0;">YÊU CẦU ĐẶT PHÒNG THÀNH CÔNG</h1>
                    </td>
                  </tr>
                  <tr>
                    <td style="padding: 30px;">
                      <p style="margin-top: 0; font-size: 15px;">Kính gửi Quý khách <strong><?php echo esc_html($data['guest_name']); ?></strong>,</p>
                      <p style="font-size: 14px; color: #475569;">
                        <strong><?php echo esc_html($hotel_name); ?></strong> xin chân thành cảm ơn Quý khách đã tin tưởng và lựa chọn khách sạn cho kỳ nghỉ tại kỳ quan Vịnh Hạ Long. Yêu cầu của Quý khách đã được tiếp nhận với mã đặt phòng:
                      </p>
                      <div style="background-color: #fffbeb; border: 1px dashed #f59e0b; padding: 12px; text-align: center; border-radius: 8px; margin: 18px 0;">
                        <span style="font-size: 11px; color: #b45309; text-transform: uppercase; letter-spacing: 1px; display: block;">Mã đặt phòng của Quý khách:</span>
                        <span style="font-size: 22px; font-weight: 800; color: #07111e; letter-spacing: 2px;"><?php echo esc_html($code); ?></span>
                      </div>
                      <table width="100%" cellspacing="0" cellpadding="8" style="font-size: 13px; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 20px;">
                        <tr style="background-color: #f8fafc;">
                          <td width="40%" style="color: #64748b;">Hạng phòng:</td>
                          <td style="font-weight: 700; color: #07111e;"><?php echo esc_html($data['room_name']); ?> (<?php echo intval($data['rooms']); ?> phòng)</td>
                        </tr>
                        <tr>
                          <td style="color: #64748b;">Thời gian:</td>
                          <td>Từ <strong><?php echo esc_html($checkin_fmt); ?></strong> đến <strong><?php echo esc_html($checkout_fmt); ?></strong> (<?php echo intval($data['nights']); ?> đêm)</td>
                        </tr>
                        <?php if (!empty($data['promo_code'])) : 
                          $c_disc_type = $data['discount_type'] ?? 'percent';
                          $c_disc_amt  = floatval($data['discount'] ?? 0);
                        ?>
                        <tr style="background-color: #f0fdf4;">
                          <td style="color: #166534;">Mã ưu đãi áp dụng:</td>
                          <td style="color: #166534; font-weight: 700;">
                            <?php echo esc_html($data['promo_code']); ?> 
                            (-<?php echo number_format($c_disc_amt, 0, ',', '.'); ?> VNĐ<?php if ($c_disc_type === 'percent' && !empty($data['discount_percent'])) echo ' ~ ' . intval($data['discount_percent']) . '%'; ?>)
                          </td>
                        </tr>
                        <?php endif; ?>
                        <tr style="background-color: #f8fafc;">
                          <td style="color: #64748b;">Tổng tiền dự kiến:</td>
                          <td style="color: #dc2626; font-size: 16px; font-weight: 800;"><?php echo esc_html($grand_total_str); ?></td>
                        </tr>
                      </table>
                      <div style="background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 12px 16px; font-size: 13px; color: #166534; margin-bottom: 20px;">
                        💡 <em>Bộ phận Lễ tân & Đặt phòng sẽ liên hệ qua số điện thoại <strong><?php echo esc_html($data['guest_phone']); ?></strong> trong ít phút để hoàn tất xác nhận và hướng dẫn nhận phòng.</em>
                      </div>
                      <p style="font-size: 13px; color: #64748b; margin-bottom: 0;">
                        Nếu cần hỗ trợ khẩn cấp, xin vui lòng gọi ngay Hotline 24/7: <a href="tel:<?php echo esc_attr($hotel_phone); ?>" style="color: #0284c7; font-weight: 700; text-decoration: none;"><?php echo esc_html($hotel_phone); ?></a>.
                      </p>
                    </td>
                  </tr>
                  <tr>
                    <td style="background-color: #f8fafc; padding: 15px; border-top: 1px solid #e2e8f0; text-align: center; font-size: 11px; color: #94a3b8;">
                      <?php echo esc_html($hotel_name); ?> — <?php echo esc_html($hotel_address); ?>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }

    /**
     * AJAX Gửi lại thông báo Zalo / Email từ màn hình Quản trị
     */
    public static function handle_resend_notifications() {
        check_ajax_referer('dl_booking_metabox_nonce', 'nonce');

        if (!current_user_can('edit_posts')) {
            wp_send_json_error(array('message' => 'Không có quyền thực hiện'));
        }

        $post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
        $channel = sanitize_text_field($_POST['channel'] ?? 'all'); // 'zalo' | 'email' | 'all'

        if (!$post_id || get_post_type($post_id) !== 'dl_booking') {
            wp_send_json_error(array('message' => 'Đơn đặt phòng không tồn tại'));
        }

        $booking_post = get_post($post_id);
        $code = $booking_post->post_title;

        $data = array(
            'booking_id'   => $post_id,
            'booking_code' => $code,
            'guest_name'   => get_post_meta($post_id, '_dl_booking_guest_name', true),
            'guest_phone'  => get_post_meta($post_id, '_dl_booking_guest_phone', true),
            'guest_email'  => get_post_meta($post_id, '_dl_booking_guest_email', true),
            'guest_note'   => get_post_meta($post_id, '_dl_booking_guest_note', true),
            'room_name'    => get_post_meta($post_id, '_dl_booking_room_name', true),
            'checkin'      => get_post_meta($post_id, '_dl_booking_checkin', true),
            'checkout'     => get_post_meta($post_id, '_dl_booking_checkout', true),
            'nights'       => get_post_meta($post_id, '_dl_booking_nights', true),
            'rooms'        => get_post_meta($post_id, '_dl_booking_rooms', true),
            'adults'       => get_post_meta($post_id, '_dl_booking_adults', true),
            'children'     => get_post_meta($post_id, '_dl_booking_children', true),
            'promo_code'       => get_post_meta($post_id, '_dl_booking_promo_code', true),
            'discount_type'    => get_post_meta($post_id, '_dl_booking_discount_type', true) ?: 'percent',
            'discount_percent' => get_post_meta($post_id, '_dl_booking_discount_percent', true),
            'discount'         => get_post_meta($post_id, '_dl_booking_discount_amount', true),
            'grand_total'      => get_post_meta($post_id, '_dl_booking_grand_total', true),
        );

        $results = array();

        if ($channel === 'zalo' || $channel === 'all') {
            if (class_exists('DragonLegend_Zalo_Service')) {
                $z_res = DragonLegend_Zalo_Service::send_booking_notifications($post_id, $data);
                $results['zalo'] = 'Đã gửi lại Zalo';
            }
        }

        if ($channel === 'telegram' || $channel === 'all') {
            if (class_exists('DragonLegend_Zalo_Service')) {
                $t_res = DragonLegend_Zalo_Service::send_booking_telegram_notification($post_id, $data);
                $results['telegram'] = 'Đã gửi lại Telegram';
            }
        }

        if ($channel === 'email' || $channel === 'all') {
            $e_res = self::send_notification_emails($post_id, $code, $data);
            $results['email'] = $e_res ? 'Đã gửi lại Email' : 'Gửi lại Email thất bại';
        }

        wp_send_json_success(array(
            'message' => 'Đã thực hiện gửi lại thông báo thành công!',
            'details' => $results,
        ));
    }
}
