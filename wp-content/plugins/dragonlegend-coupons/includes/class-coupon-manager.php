<?php
/**
 * Dragon Legend Hotel - Coupon Validation & Calculation Engine
 *
 * @package DragonLegendCoupons
 */

if (!defined('ABSPATH')) {
    exit;
}

class DragonLegend_Coupons {

    public static function init() {
        add_action('wp_ajax_dl_validate_coupon', array(__CLASS__, 'ajax_validate_coupon'));
        add_action('wp_ajax_nopriv_dl_validate_coupon', array(__CLASS__, 'ajax_validate_coupon'));
    }

    /**
     * Tìm kiếm bài viết mã ưu đãi theo Code
     */
    public static function get_coupon_post_by_code($code) {
        $code = strtoupper(trim($code));
        if (empty($code)) {
            return null;
        }

        // 1. Tìm theo meta _dl_coupon_code
        $posts = get_posts(array(
            'post_type'      => 'dl_coupon',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'meta_query'     => array(
                array(
                    'key'     => '_dl_coupon_code',
                    'value'   => $code,
                    'compare' => '='
                )
            )
        ));

        if (!empty($posts)) {
            return $posts[0];
        }

        // 2. Tìm theo post_title
        $posts = get_posts(array(
            'post_type'      => 'dl_coupon',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'title'          => $code
        ));

        if (!empty($posts)) {
            return $posts[0];
        }

        return null;
    }

    /**
     * Xác minh và tính toán chiết khấu của mã ưu đãi
     *
     * @param string $code Mã ưu đãi
     * @param float  $subtotal Tổng tiền phòng tạm tính
     * @param mixed  $room_id_or_slug ID hoặc slug của hạng phòng đang chọn
     * @return array Kết quả xác thực và chi tiết mức giảm
     */
    public static function verify_coupon($code, $subtotal = 0, $room_id_or_slug = 0) {
        $code = strtoupper(trim($code));

        if (empty($code)) {
            return array(
                'valid'   => false,
                'message' => __('Vui lòng nhập mã ưu đãi.', 'dragonlegend-coupons')
            );
        }

        $subtotal = floatval($subtotal);
        $post = self::get_coupon_post_by_code($code);

        // Nạp dữ liệu từ CPT hoặc dữ liệu fallback mặc định nếu database chưa có
        if ($post) {
            $post_id      = $post->ID;
            $status       = get_post_meta($post_id, '_dl_coupon_status', true);
            if ($status === '') $status = 'active';
            $type         = get_post_meta($post_id, '_dl_coupon_type', true) ?: 'percent';
            $value        = floatval(get_post_meta($post_id, '_dl_coupon_value', true));
            $max_discount = floatval(get_post_meta($post_id, '_dl_coupon_max_discount', true));
            $min_spend    = floatval(get_post_meta($post_id, '_dl_coupon_min_spend', true));
            $usage_limit  = intval(get_post_meta($post_id, '_dl_coupon_usage_limit', true));
            $usage_count  = intval(get_post_meta($post_id, '_dl_coupon_usage_count', true));
            $start_date   = get_post_meta($post_id, '_dl_coupon_start_date', true);
            $expiry_date  = get_post_meta($post_id, '_dl_coupon_expiry_date', true);
            $description  = get_post_meta($post_id, '_dl_coupon_description', true);
            $rooms        = get_post_meta($post_id, '_dl_coupon_rooms', true);
            if (!is_array($rooms)) $rooms = array();
        } else {
            // Fallback sang danh sách mã tĩnh mặc định nếu chưa lưu vào CPT
            $fallback = self::get_fallback_coupon($code);
            if (!$fallback) {
                return array(
                    'valid'   => false,
                    'message' => __('Mã ưu đãi không hợp lệ hoặc không tồn tại.', 'dragonlegend-coupons')
                );
            }
            $post_id      = 0;
            $status       = !empty($fallback['active']) ? 'active' : 'inactive';
            $type         = isset($fallback['type']) ? $fallback['type'] : 'percent';
            $value        = floatval($fallback['value'] ?? $fallback['percent'] ?? 10);
            $max_discount = floatval($fallback['max_discount'] ?? 0);
            $min_spend    = floatval($fallback['min_spend'] ?? 0);
            $usage_limit  = intval($fallback['usage_limit'] ?? 0);
            $usage_count  = 0;
            $start_date   = '';
            $expiry_date  = '';
            $description  = $fallback['label'] ?? '';
            $rooms        = array();
        }

        // 1. Kiểm tra trạng thái kích hoạt
        if ($status !== 'active') {
            return array(
                'valid'   => false,
                'message' => __('Mã ưu đãi hiện đang tạm ngưng áp dụng.', 'dragonlegend-coupons')
            );
        }

        // 2. Kiểm tra thời hạn hiệu lực
        $today = date('Y-m-d');
        if (!empty($start_date) && $start_date > $today) {
            return array(
                'valid'   => false,
                'message' => sprintf(__('Mã ưu đãi sẽ bắt đầu có hiệu lực từ ngày %s.', 'dragonlegend-coupons'), date('d/m/Y', strtotime($start_date)))
            );
        }

        if (!empty($expiry_date) && $expiry_date < $today) {
            return array(
                'valid'   => false,
                'message' => sprintf(__('Mã ưu đãi đã hết hạn sử dụng vào ngày %s.', 'dragonlegend-coupons'), date('d/m/Y', strtotime($expiry_date)))
            );
        }

        // 3. Kiểm tra giới hạn lượt dùng
        if ($usage_limit > 0 && $usage_count >= $usage_limit) {
            return array(
                'valid'   => false,
                'message' => __('Mã ưu đãi đã hết số lượt sử dụng tối đa.', 'dragonlegend-coupons')
            );
        }

        // 4. Kiểm tra giới hạn theo phòng
        if (!empty($rooms) && !empty($room_id_or_slug)) {
            $room_ids_int = array_map('intval', (array) $rooms);
            $matched = false;
            if (is_numeric($room_id_or_slug) && in_array(intval($room_id_or_slug), $room_ids_int, true)) {
                $matched = true;
            } else {
                // Tìm room post theo slug hoặc ID
                $r_post = get_page_by_path(sanitize_title($room_id_or_slug), OBJECT, 'dl_room');
                if (!$r_post && is_numeric($room_id_or_slug)) {
                    $r_post = get_post(intval($room_id_or_slug));
                }
                if ($r_post && in_array(intval($r_post->ID), $room_ids_int, true)) {
                    $matched = true;
                }
            }
            if (!$matched) {
                return array(
                    'valid'   => false,
                    'message' => __('Mã ưu đãi này không áp dụng cho hạng phòng bạn đang chọn.', 'dragonlegend-coupons')
                );
            }
        }

        // 5. Kiểm tra đơn hàng tối thiểu (nếu đã có subtotal > 0)
        if ($min_spend > 0 && $subtotal > 0 && $subtotal < $min_spend) {
            return array(
                'valid'   => false,
                'message' => sprintf(__('Đơn đặt phòng tối thiểu từ %s VNĐ để áp dụng mã này (Hiện tại: %s VNĐ).', 'dragonlegend-coupons'), number_format($min_spend, 0, ',', '.'), number_format($subtotal, 0, ',', '.'))
            );
        }

        // 6. Tính toán số tiền chiết khấu
        $discount_amount = 0;
        if ($type === 'percent') {
            if ($subtotal > 0) {
                $discount_amount = round(($subtotal * $value) / 100);
                if ($max_discount > 0 && $discount_amount > $max_discount) {
                    $discount_amount = $max_discount;
                }
            }
            $formatted_discount = $value . '%';
            if ($max_discount > 0) {
                $formatted_discount .= ' (tối đa ' . number_format($max_discount, 0, ',', '.') . 'đ)';
            }
        } else {
            // Giảm theo số tiền cố định VNĐ
            $discount_amount = ($subtotal > 0) ? min($subtotal, $value) : $value;
            $formatted_discount = number_format($value, 0, ',', '.') . ' VNĐ';
        }

        $label = !empty($description) ? $description : ($type === 'percent' ? "Giảm {$value}% giá phòng" : "Giảm " . number_format($value, 0, ',', '.') . " VNĐ");

        return array(
            'valid'              => true,
            'post_id'            => $post_id,
            'code'               => $code,
            'discount_type'      => $type, // 'percent' hoặc 'fixed'
            'discount_value'     => $value,
            'discount_amount'    => $discount_amount,
            'formatted_discount' => $formatted_discount,
            'max_discount'       => $max_discount,
            'min_spend'          => $min_spend,
            'percent'            => ($type === 'percent') ? intval($value) : 0, // tương thích ngược
            'label'              => $label,
            'message'            => sprintf(__('Áp dụng thành công: %s (%s)', 'dragonlegend-coupons'), $label, $formatted_discount)
        );
    }

    /**
     * Tăng số lượt sử dụng của mã ưu đãi
     */
    public static function record_usage($code) {
        $post = self::get_coupon_post_by_code($code);
        if ($post) {
            $current = intval(get_post_meta($post->ID, '_dl_coupon_usage_count', true));
            update_post_meta($post->ID, '_dl_coupon_usage_count', $current + 1);
            return true;
        }
        return false;
    }

    /**
     * Lấy danh sách các mã ưu đãi đang kích hoạt
     */
    public static function get_active_coupons() {
        $posts = get_posts(array(
            'post_type'      => 'dl_coupon',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'meta_query'     => array(
                array(
                    'key'     => '_dl_coupon_status',
                    'value'   => 'active',
                    'compare' => '='
                )
            ),
            'orderby'        => 'title',
            'order'          => 'ASC'
        ));

        $results = array();
        foreach ($posts as $p) {
            $code = get_post_meta($p->ID, '_dl_coupon_code', true) ?: $p->post_title;
            $type = get_post_meta($p->ID, '_dl_coupon_type', true) ?: 'percent';
            $val  = floatval(get_post_meta($p->ID, '_dl_coupon_value', true));
            $desc = get_post_meta($p->ID, '_dl_coupon_description', true);

            $results[$code] = array(
                'id'          => $p->ID,
                'code'        => $code,
                'type'        => $type,
                'value'       => $val,
                'description' => $desc,
                'formatted'   => ($type === 'percent') ? "{$val}%" : number_format($val, 0, ',', '.') . " VNĐ"
            );
        }

        return $results;
    }

    /**
     * Lấy mã chào mừng mặc định cho form đăng ký email footer
     */
    public static function get_welcome_coupon() {
        $coupon = self::verify_coupon('DRAGON2026', 1000000);
        if ($coupon['valid']) {
            return $coupon;
        }

        // Lấy mã đầu tiên đang kích hoạt nếu DRAGON2026 không còn
        $active = self::get_active_coupons();
        if (!empty($active)) {
            $first = reset($active);
            return self::verify_coupon($first['code'], 1000000);
        }

        return array(
            'code'               => 'DRAGON2026',
            'discount_type'      => 'percent',
            'discount_value'     => 10,
            'formatted_discount' => '10%',
            'label'              => 'Giảm 10% tri ân khách hàng trực tuyến'
        );
    }

    /**
     * Dữ liệu fallback dự phòng
     */
    private static function get_fallback_coupon($code) {
        $fallbacks = array(
            'DRAGON2026' => array(
                'type'    => 'percent',
                'value'   => 10,
                'label'   => 'Giảm 10% tri ân khách hàng trực tuyến',
                'active'  => true
            ),
            'SUMMER20' => array(
                'type'         => 'percent',
                'value'        => 20,
                'max_discount' => 500000,
                'label'        => 'Giảm 20% gói ưu đãi mùa hè (tối đa 500k)',
                'active'       => true
            ),
            'VIP500K' => array(
                'type'      => 'fixed',
                'value'     => 500000,
                'min_spend' => 2000000,
                'label'     => 'Giảm 500.000 VNĐ cho kỳ nghỉ sang trọng (đơn từ 2tr)',
                'active'    => true
            ),
            'WELCOME100K' => array(
                'type'      => 'fixed',
                'value'     => 100000,
                'min_spend' => 1000000,
                'label'     => 'Giảm 100.000 VNĐ chào mừng khách mới (đơn từ 1tr)',
                'active'    => true
            )
        );

        return isset($fallbacks[$code]) ? $fallbacks[$code] : null;
    }

    /**
     * AJAX Endpoint kiểm tra và tính toán mã ưu đãi từ Frontend
     * Không dùng check_ajax_referer die(-1) để tương thích với Super Page Cache
     */
    public static function ajax_validate_coupon() {
        $code     = isset($_POST['code']) ? sanitize_text_field($_POST['code']) : '';
        $subtotal = isset($_POST['subtotal']) ? floatval($_POST['subtotal']) : 0;
        $room_id  = isset($_POST['room_id']) ? sanitize_text_field($_POST['room_id']) : 0;

        if (empty($code)) {
            wp_send_json_error(array('message' => __('Vui lòng nhập mã ưu đãi.', 'dragonlegend-coupons')));
        }

        $result = self::verify_coupon($code, $subtotal, $room_id);

        if ($result['valid']) {
            wp_send_json_success($result);
        } else {
            wp_send_json_error($result);
        }
    }
}
