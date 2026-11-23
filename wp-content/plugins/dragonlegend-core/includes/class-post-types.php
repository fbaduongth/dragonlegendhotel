<?php
/**
 * Dragon Legend Hotel Core - Custom Post Types & Taxonomies
 *
 * @package DragonLegendCore
 */

if (!defined('ABSPATH')) {
    exit;
}

class DragonLegend_Post_Types {

    public static function init() {
        add_action('init', array(__CLASS__, 'register_post_types'));
        add_action('init', array(__CLASS__, 'register_taxonomies'));

        // Custom columns for Bookings in WP Admin
        add_filter('manage_dl_booking_posts_columns', array(__CLASS__, 'booking_columns'));
        add_action('manage_dl_booking_posts_custom_column', array(__CLASS__, 'booking_column_content'), 10, 2);

        // Custom columns for Rooms in WP Admin
        add_filter('manage_dl_room_posts_columns', array(__CLASS__, 'room_columns'));
        add_action('manage_dl_room_posts_custom_column', array(__CLASS__, 'room_column_content'), 10, 2);

        // Custom columns for Subscribers in WP Admin
        add_filter('manage_dl_subscriber_posts_columns', array(__CLASS__, 'subscriber_columns'));
        add_action('manage_dl_subscriber_posts_custom_column', array(__CLASS__, 'subscriber_column_content'), 10, 2);
        add_action('manage_posts_extra_tablenav', array(__CLASS__, 'subscriber_table_nav_actions'), 10, 1);

        // Custom Admin Menu Order
        add_filter('custom_menu_order', '__return_true');
        add_filter('menu_order', array(__CLASS__, 'reorder_admin_menu'), 999);
    }

    /**
     * Register Custom Post Types
     */
    public static function register_post_types() {
        // 1. Post Type: Hạng Phòng Nghỉ (dl_room)
        $room_labels = array(
            'name'               => __('Hạng Phòng', 'dragonlegend-core'),
            'singular_name'      => __('Hạng Phòng', 'dragonlegend-core'),
            'menu_name'          => __('Phòng Nghỉ', 'dragonlegend-core'),
            'name_admin_bar'     => __('Hạng Phòng', 'dragonlegend-core'),
            'add_new'            => __('Thêm phòng mới', 'dragonlegend-core'),
            'add_new_item'       => __('Thêm Hạng Phòng Mới', 'dragonlegend-core'),
            'new_item'           => __('Hạng phòng mới', 'dragonlegend-core'),
            'edit_item'          => __('Chỉnh sửa hạng phòng', 'dragonlegend-core'),
            'view_item'          => __('Xem hạng phòng', 'dragonlegend-core'),
            'all_items'          => __('Tất cả hạng phòng', 'dragonlegend-core'),
            'search_items'       => __('Tìm kiếm phòng', 'dragonlegend-core'),
            'not_found'          => __('Không tìm thấy phòng nào.', 'dragonlegend-core'),
            'not_found_in_trash' => __('Không có phòng trong thùng rác.', 'dragonlegend-core'),
        );

        register_post_type('dl_room', array(
            'labels'             => $room_labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'phong-nghi'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-building',
            'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
            'show_in_rest'       => true,
        ));

        // 2. Post Type: Gói Ưu Đãi / Khuyến Mãi (dl_offer)
        $offer_labels = array(
            'name'               => __('Ưu Đãi & Khuyến Mãi', 'dragonlegend-core'),
            'singular_name'      => __('Ưu Đãi', 'dragonlegend-core'),
            'menu_name'          => __('Ưu Đãi & Combo', 'dragonlegend-core'),
            'all_items'          => __('Tất cả ưu đãi', 'dragonlegend-core'),
            'add_new'            => __('Thêm ưu đãi mới', 'dragonlegend-core'),
            'add_new_item'       => __('Thêm Ưu Đãi Mới', 'dragonlegend-core'),
            'edit_item'          => __('Chỉnh sửa ưu đãi', 'dragonlegend-core'),
            'view_item'          => __('Xem ưu đãi', 'dragonlegend-core'),
            'search_items'       => __('Tìm kiếm ưu đãi', 'dragonlegend-core'),
        );

        register_post_type('dl_offer', array(
            'labels'             => $offer_labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'uu-dai'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 6,
            'menu_icon'          => 'dashicons-tickets-alt',
            'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
            'show_in_rest'       => true,
        ));

        // 3. Post Type: Dịch Vụ & Tiện Ích (dl_amenity)
        $amenity_labels = array(
            'name'               => __('Dịch Vụ & Tiện Ích', 'dragonlegend-core'),
            'singular_name'      => __('Dịch Vụ', 'dragonlegend-core'),
            'menu_name'          => __('Dịch Vụ Khách Sạn', 'dragonlegend-core'),
            'all_items'          => __('Tất cả dịch vụ', 'dragonlegend-core'),
            'add_new'            => __('Thêm dịch vụ mới', 'dragonlegend-core'),
            'add_new_item'       => __('Thêm Dịch Vụ Mới', 'dragonlegend-core'),
            'edit_item'          => __('Chỉnh sửa dịch vụ', 'dragonlegend-core'),
        );

        register_post_type('dl_amenity', array(
            'labels'             => $amenity_labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'dich-vu'),
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => 7,
            'menu_icon'          => 'dashicons-star-filled',
            'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
            'show_in_rest'       => true,
        ));

        // 4. Post Type: Đánh Giá Khách Hàng (dl_testimonial)
        $testimonial_labels = array(
            'name'               => __('Đánh Giá Của Khách', 'dragonlegend-core'),
            'singular_name'      => __('Đánh Giá', 'dragonlegend-core'),
            'menu_name'          => __('Đánh Giá', 'dragonlegend-core'),
            'all_items'          => __('Tất cả đánh giá', 'dragonlegend-core'),
            'add_new'            => __('Thêm đánh giá mới', 'dragonlegend-core'),
            'add_new_item'       => __('Thêm Đánh Giá Mới', 'dragonlegend-core'),
            'edit_item'          => __('Chỉnh sửa đánh giá', 'dragonlegend-core'),
        );

        register_post_type('dl_testimonial', array(
            'labels'             => $testimonial_labels,
            'public'             => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'capability_type'    => 'post',
            'menu_position'      => 8,
            'menu_icon'          => 'dashicons-testimonial',
            'supports'           => array('title', 'editor', 'thumbnail'),
            'show_in_rest'       => true,
        ));

        // 5. Post Type: Đơn Đặt Phòng (dl_booking)
        $booking_labels = array(
            'name'               => __('Đơn Đặt Phòng', 'dragonlegend-core'),
            'singular_name'      => __('Đơn Đặt Phòng', 'dragonlegend-core'),
            'menu_name'          => __('Đơn Đặt Phòng', 'dragonlegend-core'),
            'all_items'          => __('Tất cả đơn đặt', 'dragonlegend-core'),
            'add_new'            => __('Tạo đơn mới', 'dragonlegend-core'),
            'add_new_item'       => __('Tạo Đơn Đặt Phòng Mới', 'dragonlegend-core'),
            'edit_item'          => __('Chi tiết đơn đặt phòng', 'dragonlegend-core'),
            'view_item'          => __('Xem đơn đặt phòng', 'dragonlegend-core'),
            'search_items'       => __('Tìm kiếm mã đơn / khách hàng', 'dragonlegend-core'),
        );

        register_post_type('dl_booking', array(
            'labels'             => $booking_labels,
            'public'             => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'capability_type'    => 'post',
            'menu_position'      => 4,
            'menu_icon'          => 'dashicons-calendar-alt',
            'supports'           => array('title'),
            'show_in_rest'       => false,
        ));

        // 6. Post Type: Khách Đăng Ký Nhận Ưu Đãi (dl_subscriber)
        $subscriber_labels = array(
            'name'               => __('Đăng Ký Ưu Đãi', 'dragonlegend-core'),
            'singular_name'      => __('Khách Nhận Ưu Đãi', 'dragonlegend-core'),
            'menu_name'          => __('Đăng Ký Ưu Đãi', 'dragonlegend-core'),
            'all_items'          => __('Danh Sách Đăng Ký', 'dragonlegend-core'),
            'add_new'            => __('Thêm khách mới', 'dragonlegend-core'),
            'add_new_item'       => __('Thêm Email Khách Nhận Ưu Đãi', 'dragonlegend-core'),
            'edit_item'          => __('Chi tiết khách nhận ưu đãi', 'dragonlegend-core'),
            'view_item'          => __('Xem thông tin', 'dragonlegend-core'),
            'search_items'       => __('Tìm kiếm email khách hàng', 'dragonlegend-core'),
            'not_found'          => __('Chưa có khách hàng nào đăng ký.', 'dragonlegend-core'),
            'not_found_in_trash' => __('Không có email nào trong thùng rác.', 'dragonlegend-core'),
        );

        register_post_type('dl_subscriber', array(
            'labels'             => $subscriber_labels,
            'public'             => false,
            'show_ui'            => true,
            'show_in_menu'       => 'edit.php?post_type=dl_booking',
            'capability_type'    => 'post',
            'supports'           => array('title'),
            'show_in_rest'       => false,
        ));
    }

    /**
     * Register Taxonomies
     */
    public static function register_taxonomies() {
        // Taxonomy Loại Phòng (dl_room_type) cho dl_room
        $labels = array(
            'name'              => __('Loại Phòng', 'dragonlegend-core'),
            'singular_name'     => __('Loại Phòng', 'dragonlegend-core'),
            'search_items'      => __('Tìm loại phòng', 'dragonlegend-core'),
            'all_items'         => __('Tất cả loại phòng', 'dragonlegend-core'),
            'parent_item'       => __('Loại phòng cha', 'dragonlegend-core'),
            'parent_item_colon' => __('Loại phòng cha:', 'dragonlegend-core'),
            'edit_item'         => __('Chỉnh sửa loại phòng', 'dragonlegend-core'),
            'update_item'       => __('Cập nhật loại phòng', 'dragonlegend-core'),
            'add_new_item'      => __('Thêm loại phòng mới', 'dragonlegend-core'),
            'new_item_name'     => __('Tên loại phòng mới', 'dragonlegend-core'),
            'menu_name'         => __('Phân Loại Phòng', 'dragonlegend-core'),
        );

        register_taxonomy('dl_room_type', array('dl_room'), array(
            'hierarchical'      => true,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'loai-phong'),
            'show_in_rest'      => true,
        ));
    }

    /**
     * Admin columns for dl_room
     */
    public static function room_columns($columns) {
        $new_columns = array();
        $new_columns['cb'] = $columns['cb'];
        $new_columns['thumbnail'] = __('Ảnh', 'dragonlegend-core');
        $new_columns['title'] = $columns['title'];
        $new_columns['taxonomy-dl_room_type'] = __('Loại phòng', 'dragonlegend-core');
        $new_columns['price'] = __('Giá / Đêm', 'dragonlegend-core');
        $new_columns['size'] = __('Diện tích', 'dragonlegend-core');
        $new_columns['badge'] = __('Huy hiệu', 'dragonlegend-core');
        $new_columns['date'] = $columns['date'];
        return $new_columns;
    }

    public static function room_column_content($column, $post_id) {
        switch ($column) {
            case 'thumbnail':
                if (has_post_thumbnail($post_id)) {
                    echo get_the_post_thumbnail($post_id, array(50, 50), array('style' => 'border-radius:4px; object-fit:cover;'));
                } else {
                    echo '<span style="color:#999;">—</span>';
                }
                break;
            case 'price':
                $price = get_post_meta($post_id, '_dl_room_price', true);
                if ($price && is_numeric($price)) {
                    echo '<strong>' . number_format(floatval($price), 0, ',', '.') . ' VNĐ</strong>';
                } else {
                    echo '—';
                }
                break;
            case 'size':
                $size = get_post_meta($post_id, '_dl_room_size', true);
                echo $size ? esc_html($size) : '—';
                break;
            case 'badge':
                $badge = get_post_meta($post_id, '_dl_room_badge', true);
                if ($badge) {
                    echo '<span style="background:#c5a880; color:#fff; padding:2px 6px; border-radius:10px; font-size:11px;">' . esc_html($badge) . '</span>';
                } else {
                    echo '—';
                }
                break;
        }
    }

    /**
     * Admin columns for dl_booking
     */
    public static function booking_columns($columns) {
        return array(
            'cb'           => '<input type="checkbox" />',
            'title'        => __('Mã Đặt Phòng', 'dragonlegend-core'),
            'guest_info'   => __('Khách Hàng', 'dragonlegend-core'),
            'room'         => __('Phòng', 'dragonlegend-core'),
            'dates'        => __('Lưu Trú', 'dragonlegend-core'),
            'total'         => __('Tổng Tiền', 'dragonlegend-core'),
            'status'        => __('Trạng Thái', 'dragonlegend-core'),
            'notifications' => __('Thông Báo', 'dragonlegend-core'),
            'date'          => __('Ngày Gửi', 'dragonlegend-core')
        );
    }

    public static function booking_column_content($column, $post_id) {
        switch ($column) {
            case 'guest_info':
                $name = get_post_meta($post_id, '_dl_booking_guest_name', true);
                $phone = get_post_meta($post_id, '_dl_booking_guest_phone', true);
                $email = get_post_meta($post_id, '_dl_booking_guest_email', true);
                echo '<strong>' . esc_html($name) . '</strong><br>';
                echo '<a href="tel:' . esc_attr($phone) . '">' . esc_html($phone) . '</a>';
                if ($email) {
                    echo '<br><small style="color:#666;">' . esc_html($email) . '</small>';
                }
                break;

            case 'room':
                $room_name = get_post_meta($post_id, '_dl_booking_room_name', true);
                $rooms_count = get_post_meta($post_id, '_dl_booking_rooms', true);
                echo esc_html($room_name);
                if ($rooms_count) {
                    echo ' (' . intval($rooms_count) . ' phòng)';
                }
                break;

            case 'dates':
                $checkin = get_post_meta($post_id, '_dl_booking_checkin', true);
                $checkout = get_post_meta($post_id, '_dl_booking_checkout', true);
                $nights = get_post_meta($post_id, '_dl_booking_nights', true);
                if ($checkin && $checkout) {
                    echo esc_html($checkin) . ' &rarr; ' . esc_html($checkout);
                    echo '<br><small style="color:#666;">(' . intval($nights) . ' đêm)</small>';
                } else {
                    echo '—';
                }
                break;

            case 'total':
                $total = get_post_meta($post_id, '_dl_booking_grand_total', true);
                if ($total && is_numeric($total)) {
                    echo '<strong style="color:#0b1a2d;">' . number_format(floatval($total), 0, ',', '.') . ' VNĐ</strong>';
                } else {
                    echo '—';
                }
                break;

            case 'status':
                $status = get_post_meta($post_id, '_dl_booking_status', true);
                $status = $status ? $status : 'pending';
                $status_map = array(
                    'pending'   => array('label' => 'Chờ xác nhận', 'color' => '#f39c12'),
                    'confirmed' => array('label' => 'Đã xác nhận', 'color' => '#27ae60'),
                    'cancelled' => array('label' => 'Đã hủy', 'color' => '#e74c3c'),
                    'completed' => array('label' => 'Đã hoàn thành', 'color' => '#2980b9'),
                );
                $current = isset($status_map[$status]) ? $status_map[$status] : $status_map['pending'];
                echo '<span style="background:' . esc_attr($current['color']) . '; color:#fff; padding:3px 8px; border-radius:12px; font-size:11px; font-weight:600;">' . esc_html($current['label']) . '</span>';
                break;

            case 'notifications':
                $zalo_guest  = get_post_meta($post_id, '_dl_booking_zalo_guest_status', true);
                $zalo_admin  = get_post_meta($post_id, '_dl_booking_zalo_admin_status', true);
                $email_admin = get_post_meta($post_id, '_dl_booking_email_admin_status', true);

                $get_mini_badge = function($status, $label) {
                    $color = '#94a3b8';
                    $icon = '○';
                    if ($status === 'success') {
                        $color = '#10b981';
                        $icon = '✓';
                    } elseif ($status === 'simulated') {
                        $color = '#6366f1';
                        $icon = '⚡';
                    } elseif ($status === 'failed') {
                        $color = '#ef4444';
                        $icon = '✕';
                    } elseif ($status === 'disabled') {
                        $color = '#cbd5e1';
                        $icon = '—';
                    }
                    return '<span title="' . esc_attr($label . ': ' . ($status ?: 'chưa gửi')) . '" style="display:inline-block; font-size:10px; font-weight:600; background:' . $color . '; color:#fff; padding:1px 6px; border-radius:4px; margin-bottom:2px; white-space:nowrap;">' . $icon . ' ' . esc_html($label) . '</span>';
                };

                echo '<div style="display:flex; flex-direction:column; gap:2px; align-items:flex-start;">';
                echo $get_mini_badge($zalo_guest, 'Zalo Khách');
                echo $get_mini_badge($zalo_admin, 'Zalo Admin');
                echo $get_mini_badge($email_admin, 'Email Admin');
                echo '</div>';
                break;
        }
    }

    /**
     * Admin columns for dl_subscriber
     */
    public static function subscriber_columns($columns) {
        return array(
            'cb'          => '<input type="checkbox" />',
            'title'       => __('Email Khách Hàng', 'dragonlegend-core'),
            'coupon'      => __('Mã Ưu Đãi Cấp', 'dragonlegend-core'),
            'mail_status' => __('Trạng Thái Thư', 'dragonlegend-core'),
            'ip_address'  => __('Địa Chỉ IP', 'dragonlegend-core'),
            'date'        => __('Ngày Đăng Ký', 'dragonlegend-core')
        );
    }

    public static function subscriber_column_content($column, $post_id) {
        switch ($column) {
            case 'coupon':
                $code = get_post_meta($post_id, '_coupon_code', true);
                echo '<span style="background:#07111e; color:#c5a880; padding:3px 8px; border-radius:4px; font-weight:bold; font-family:monospace; font-size:12px; border:1px solid #c5a880;">' . esc_html($code ? $code : 'DRAGON2026') . '</span>';
                break;

            case 'mail_status':
                $sent = get_post_meta($post_id, '_welcome_email_sent', true);
                if ($sent === '1' || $sent === 1) {
                    echo '<span style="color:#16a34a; font-weight:600;"><span class="dashicons dashicons-yes-alt" style="font-size:16px; vertical-align:middle;"></span> Đã gửi mã voucher</span>';
                } else {
                    echo '<span style="color:#64748b; font-weight:500;">Chưa gửi</span>';
                }
                break;

            case 'ip_address':
                $ip = get_post_meta($post_id, '_subscriber_ip', true);
                echo '<span style="font-family:monospace; color:#64748b;">' . esc_html($ip ? $ip : '—') . '</span>';
                break;
        }
    }

    /**
     * Thêm các nút thao tác nhanh trên danh sách khách đăng ký ưu đãi (Xuất CSV & Gửi Email)
     */
    public static function subscriber_table_nav_actions($which) {
        global $typenow;
        if ($typenow !== 'dl_subscriber' || $which !== 'top') {
            return;
        }

        $export_url = wp_nonce_url(admin_url('admin.php?action=dl_export_subscribers_csv'), 'dl_export_subscribers_nonce');
        $broadcast_url = admin_url('edit.php?post_type=dl_booking&page=dl-broadcast-email');

        echo '<div class="alignleft actions" style="display:inline-flex; gap:8px; align-items:center; margin-left:8px;">';
        echo '<a href="' . esc_url($export_url) . '" class="button button-primary" style="background:#16a34a; border-color:#15803d; color:#fff; display:inline-flex; align-items:center; gap:5px;"><span class="dashicons dashicons-download" style="font-size:16px; line-height:1.6;"></span> ' . esc_html__('Xuất File Excel (CSV)', 'dragonlegend-core') . '</a>';
        echo '<a href="' . esc_url($broadcast_url) . '" class="button button-secondary" style="background:#07111e; color:#c5a880; border-color:#c5a880; display:inline-flex; align-items:center; gap:5px;"><span class="dashicons dashicons-email-alt" style="font-size:16px; line-height:1.6; color:#c5a880;"></span> ' . esc_html__('Gửi Email Hàng Loạt', 'dragonlegend-core') . '</a>';
        echo '</div>';
    }

    /**
     * Sắp xếp lại thứ tự menu trong bảng quản trị WP-Admin theo đúng yêu cầu:
     * 1. Trang quản trị WordPress (index.php - Mặc định mở Site Kit Dashboard)
     * 2. Đơn đặt phòng (edit.php?post_type=dl_booking)
     * 3. Nội dung tĩnh (dragonlegend-content-manager)
     * 4. Phòng nghỉ (edit.php?post_type=dl_room)
     * 5. Ưu đãi & Combo (edit.php?post_type=dl_offer)
     * 6. Dịch vụ khách sạn (edit.php?post_type=dl_amenity)
     * 7. Đánh giá (Review) (edit.php?post_type=dl_testimonial)
     * 8. Đường phân cách
     * ...
     * Cuối cùng: Site Kit (googlesitekit-dashboard)
     */
    public static function reorder_admin_menu($menu_order) {
        if (!$menu_order) {
            return true;
        }

        $desired = array(
            'index.php',                          // 1. Trang quản trị WordPress (Mặc định mở Site Kit Dashboard)
            'edit.php?post_type=dl_booking',     // 2. Đơn đặt phòng
            'dragonlegend-content-manager',       // 3. Nội dung tĩnh
            'edit.php?post_type=dl_room',        // 4. Phòng nghỉ
            'edit.php?post_type=dl_offer',       // 5. Ưu đãi & Combo
            'edit.php?post_type=dl_amenity',     // 6. Dịch vụ khách sạn
            'edit.php?post_type=dl_testimonial', // 7. Đánh Giá
            'edit.php?post_type=dl_job',         // 8. Tuyển Dụng
            'separator1',                        // Đường phân cách
        );

        $new_order = array();
        foreach ($desired as $slug) {
            if (in_array($slug, $menu_order, true)) {
                $new_order[] = $slug;
            }
        }

        foreach ($menu_order as $slug) {
            if (!in_array($slug, $new_order, true) && $slug !== 'googlesitekit-dashboard') {
                $new_order[] = $slug;
            }
        }

        // Đưa Site Kit xuống cuối cùng của menu
        if (in_array('googlesitekit-dashboard', $menu_order, true)) {
            $new_order[] = 'googlesitekit-dashboard';
        }

        return $new_order;
    }
}

