<?php
/**
 * Dragon Legend Hotel - Coupon Post Type & Admin Table
 *
 * @package DragonLegendCoupons
 */

if (!defined('ABSPATH')) {
    exit;
}

class DragonLegend_Coupon_CPT {

    public static function init() {
        add_action('init', array(__CLASS__, 'register_post_type'));
        add_filter('manage_dl_coupon_posts_columns', array(__CLASS__, 'register_columns'));
        add_action('manage_dl_coupon_posts_custom_column', array(__CLASS__, 'render_column_content'), 10, 2);
    }

    /**
     * Đăng ký Custom Post Type dl_coupon
     */
    public static function register_post_type() {
        $labels = array(
            'name'               => __('Mã Ưu Đãi', 'dragonlegend-coupons'),
            'singular_name'      => __('Mã Ưu Đãi', 'dragonlegend-coupons'),
            'menu_name'          => __('Mã Ưu Đãi', 'dragonlegend-coupons'),
            'all_items'          => __('Quản Lý Mã Ưu Đãi', 'dragonlegend-coupons'),
            'add_new'            => __('Thêm Mã Mới', 'dragonlegend-coupons'),
            'add_new_item'       => __('Tạo Mã Ưu Đãi Mới', 'dragonlegend-coupons'),
            'edit_item'          => __('Chỉnh Sửa Mã Ưu Đãi', 'dragonlegend-coupons'),
            'new_item'           => __('Mã Ưu Đãi Mới', 'dragonlegend-coupons'),
            'view_item'          => __('Xem Mã Ưu Đãi', 'dragonlegend-coupons'),
            'search_items'       => __('Tìm Kiếm Mã Ưu Đãi', 'dragonlegend-coupons'),
            'not_found'          => __('Không tìm thấy mã ưu đãi nào.', 'dragonlegend-coupons'),
            'not_found_in_trash' => __('Không có mã ưu đãi nào trong thùng rác.', 'dragonlegend-coupons'),
        );

        register_post_type('dl_coupon', array(
            'labels'              => $labels,
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => 'edit.php?post_type=dl_booking',
            'capability_type'     => 'post',
            'hierarchical'        => false,
            'supports'            => array('title'),
            'has_archive'         => false,
            'rewrite'             => false,
            'show_in_rest'        => false,
        ));
    }

    /**
     * Cột hiển thị trong danh sách mã ưu đãi
     */
    public static function register_columns($columns) {
        $new_columns = array(
            'cb'          => '<input type="checkbox" />',
            'title'       => __('Tên Chương Trình', 'dragonlegend-coupons'),
            'coupon_code' => __('Mã Ưu Đãi (Code)', 'dragonlegend-coupons'),
            'discount'    => __('Loại & Mức Giảm', 'dragonlegend-coupons'),
            'conditions'  => __('Điều Kiện Áp Dụng', 'dragonlegend-coupons'),
            'usage'       => __('Lượt Sử Dụng', 'dragonlegend-coupons'),
            'validity'    => __('Thời Gian Hiệu Lực', 'dragonlegend-coupons'),
            'status'      => __('Trạng Thái', 'dragonlegend-coupons'),
        );

        return $new_columns;
    }

    /**
     * Nội dung các cột tùy biến
     */
    public static function render_column_content($column, $post_id) {
        $code         = get_post_meta($post_id, '_dl_coupon_code', true);
        $type         = get_post_meta($post_id, '_dl_coupon_type', true) ?: 'percent';
        $value        = floatval(get_post_meta($post_id, '_dl_coupon_value', true));
        $max_discount = floatval(get_post_meta($post_id, '_dl_coupon_max_discount', true));
        $min_spend    = floatval(get_post_meta($post_id, '_dl_coupon_min_spend', true));
        $usage_limit  = intval(get_post_meta($post_id, '_dl_coupon_usage_limit', true));
        $usage_count  = intval(get_post_meta($post_id, '_dl_coupon_usage_count', true));
        $start_date   = get_post_meta($post_id, '_dl_coupon_start_date', true);
        $expiry_date  = get_post_meta($post_id, '_dl_coupon_expiry_date', true);
        $status       = get_post_meta($post_id, '_dl_coupon_status', true);
        if ($status === '') $status = 'active';

        switch ($column) {
            case 'coupon_code':
                if (empty($code)) {
                    $code = strtoupper(get_the_title($post_id));
                }
                echo '<span style="display:inline-block; background:#07111e; color:#c5a880; padding:4px 10px; border-radius:6px; font-weight:700; font-family:monospace; font-size:13px; border:1px solid #c5a880; letter-spacing:1px;">' . esc_html($code) . '</span>';
                break;

            case 'discount':
                if ($type === 'percent') {
                    echo '<strong style="color:#d97706; font-size:14px;">Giảm ' . esc_html($value) . '%</strong>';
                    if ($max_discount > 0) {
                        echo '<br><span style="font-size:11px; color:#64748b;">(Tối đa: ' . number_format($max_discount, 0, ',', '.') . 'đ)</span>';
                    }
                } else {
                    echo '<strong style="color:#16a34a; font-size:14px;">-' . number_format($value, 0, ',', '.') . ' VNĐ</strong>';
                    echo '<br><span style="font-size:11px; color:#64748b;">(Giảm tiền mặt cố định)</span>';
                }
                break;

            case 'conditions':
                $conds = array();
                if ($min_spend > 0) {
                    $conds[] = 'Đơn tối thiểu: <strong>' . number_format($min_spend, 0, ',', '.') . 'đ</strong>';
                }
                $rooms = get_post_meta($post_id, '_dl_coupon_rooms', true);
                if (is_array($rooms) && !empty($rooms)) {
                    $conds[] = 'Áp dụng ' . count($rooms) . ' hạng phòng';
                } else {
                    $conds[] = 'Áp dụng mọi phòng';
                }
                echo '<div style="font-size:12px; line-height:1.4;">' . implode('<br>', $conds) . '</div>';
                break;

            case 'usage':
                $limit_text = ($usage_limit > 0) ? $usage_limit : '∞';
                $usage_percent = ($usage_limit > 0) ? min(100, round(($usage_count / $usage_limit) * 100)) : 0;
                
                echo '<span style="font-weight:600; font-size:13px;">' . esc_html($usage_count) . ' / ' . esc_html($limit_text) . '</span>';
                if ($usage_limit > 0) {
                    $bar_color = ($usage_percent >= 100) ? '#dc2626' : '#3b82f6';
                    echo '<div style="width:70px; height:5px; background:#e2e8f0; border-radius:3px; margin-top:4px; overflow:hidden;">';
                    echo '<div style="width:' . $usage_percent . '%; height:100%; background:' . $bar_color . ';"></div>';
                    echo '</div>';
                }
                break;

            case 'validity':
                $now = date('Y-m-d');
                $is_expired = ($expiry_date && $expiry_date < $now);
                $is_not_started = ($start_date && $start_date > $now);

                if ($is_expired) {
                    echo '<span style="display:inline-block; padding:2px 8px; border-radius:4px; font-size:11px; font-weight:600; background:#fee2e2; color:#b91c1c;">Hết hạn (' . esc_html($expiry_date) . ')</span>';
                } elseif ($is_not_started) {
                    echo '<span style="display:inline-block; padding:2px 8px; border-radius:4px; font-size:11px; font-weight:600; background:#fef3c7; color:#b45309;">Từ ' . esc_html($start_date) . '</span>';
                } elseif ($expiry_date) {
                    echo '<span style="display:inline-block; padding:2px 8px; border-radius:4px; font-size:11px; font-weight:600; background:#dcfce7; color:#15803d;">Đến ' . esc_html($expiry_date) . '</span>';
                } else {
                    echo '<span style="font-size:12px; color:#64748b;">Vô thời hạn</span>';
                }
                break;

            case 'status':
                if ($status === 'active') {
                    echo '<span style="display:inline-flex; align-items:center; gap:4px; color:#16a34a; font-weight:600;"><span class="dashicons dashicons-yes-alt" style="font-size:16px; vertical-align:middle;"></span> Đang áp dụng</span>';
                } else {
                    echo '<span style="display:inline-flex; align-items:center; gap:4px; color:#94a3b8; font-weight:500;"><span class="dashicons dashicons-marker" style="font-size:16px; vertical-align:middle;"></span> Tạm ngưng</span>';
                }
                break;
        }
    }
}
