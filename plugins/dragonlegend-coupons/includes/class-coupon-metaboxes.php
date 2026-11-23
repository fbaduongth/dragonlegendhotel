<?php
/**
 * Dragon Legend Hotel - Coupon Metaboxes
 *
 * @package DragonLegendCoupons
 */

if (!defined('ABSPATH')) {
    exit;
}

class DragonLegend_Coupon_Metaboxes {

    public static function init() {
        add_action('add_meta_boxes', array(__CLASS__, 'register_metaboxes'));
        add_action('save_post_dl_coupon', array(__CLASS__, 'save_metaboxes'), 10, 2);
    }

    public static function register_metaboxes() {
        add_meta_box(
            'dl_coupon_configuration',
            __('Cấu Hình Chi Tiết Mã Ưu Đãi & Khuyến Mãi', 'dragonlegend-coupons'),
            array(__CLASS__, 'render_metabox'),
            'dl_coupon',
            'normal',
            'high'
        );
    }

    public static function render_metabox($post) {
        wp_nonce_field('dl_save_coupon_meta', 'dl_coupon_nonce');

        $code         = get_post_meta($post->ID, '_dl_coupon_code', true);
        if (empty($code) && !empty($post->post_title)) {
            $code = strtoupper(trim($post->post_title));
        }
        $description  = get_post_meta($post->ID, '_dl_coupon_description', true);
        $type         = get_post_meta($post->ID, '_dl_coupon_type', true) ?: 'percent';
        $value        = get_post_meta($post->ID, '_dl_coupon_value', true);
        if ($value === '') $value = '10';
        $max_discount = get_post_meta($post->ID, '_dl_coupon_max_discount', true);
        $min_spend    = get_post_meta($post->ID, '_dl_coupon_min_spend', true);
        $usage_limit  = get_post_meta($post->ID, '_dl_coupon_usage_limit', true);
        $usage_count  = intval(get_post_meta($post->ID, '_dl_coupon_usage_count', true));
        $start_date   = get_post_meta($post->ID, '_dl_coupon_start_date', true);
        $expiry_date  = get_post_meta($post->ID, '_dl_coupon_expiry_date', true);
        $status       = get_post_meta($post->ID, '_dl_coupon_status', true);
        if ($status === '') $status = 'active';

        $saved_rooms  = get_post_meta($post->ID, '_dl_coupon_rooms', true);
        if (!is_array($saved_rooms)) {
            $saved_rooms = array();
        }

        $all_rooms = get_posts(array(
            'post_type'      => 'dl_room',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'title',
            'order'          => 'ASC'
        ));
        ?>
        <style>
            .dl-coupon-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
                margin-top: 10px;
            }
            @media (max-width: 782px) {
                .dl-coupon-grid { grid-template-columns: 1fr; }
            }
            .dl-coupon-card {
                background: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                padding: 16px;
            }
            .dl-coupon-card h4 {
                margin: 0 0 14px 0;
                font-size: 14px;
                color: #0f172a;
                border-bottom: 2px solid #c5a880;
                padding-bottom: 6px;
                display: flex;
                align-items: center;
                gap: 6px;
            }
            .dl-form-group {
                margin-bottom: 14px;
            }
            .dl-form-group:last-child {
                margin-bottom: 0;
            }
            .dl-form-group label {
                display: block;
                font-weight: 600;
                font-size: 13px;
                margin-bottom: 4px;
                color: #334155;
            }
            .dl-form-group input[type="text"],
            .dl-form-group input[type="number"],
            .dl-form-group input[type="date"],
            .dl-form-group select,
            .dl-form-group textarea {
                width: 100%;
                padding: 8px 10px;
                border: 1px solid #cbd5e1;
                border-radius: 6px;
                font-size: 13px;
            }
            .dl-form-group .help-text {
                font-size: 12px;
                color: #64748b;
                margin-top: 4px;
                line-height: 1.4;
            }
            .badge-gold {
                background: #07111e;
                color: #c5a880;
                font-family: monospace;
                padding: 3px 8px;
                border-radius: 4px;
                border: 1px solid #c5a880;
                font-weight: bold;
            }
        </style>

        <div class="dl-coupon-wrapper">
            <div class="dl-coupon-grid">
                <!-- CỘT 1: THÔNG TIN CỐT LÕI & MỨC GIẢM -->
                <div class="dl-coupon-card">
                    <h4><span class="dashicons dashicons-tag" style="color:#c5a880;"></span> Thông Tin & Mức Giảm Giá</h4>

                    <div class="dl-form-group">
                        <label for="_dl_coupon_code"><?php _e('Mã Ưu Đãi (Mã Code nhập khi đặt phòng):', 'dragonlegend-coupons'); ?> <span style="color:#ef4444;">*</span></label>
                        <input type="text" id="_dl_coupon_code" name="_dl_coupon_code" value="<?php echo esc_attr($code); ?>" placeholder="VD: DRAGON2026, SUMMER500K..." style="font-family:monospace; font-size:16px; font-weight:700; text-transform:uppercase; color:#07111e; letter-spacing:1px;" required>
                        <p class="help-text"><?php _e('Mã hiển thị cho khách hàng, tự động chuẩn hóa chữ HOA, viết liền không dấu.', 'dragonlegend-coupons'); ?></p>
                    </div>

                    <div class="dl-form-group">
                        <label for="_dl_coupon_type"><?php _e('Hình Thức Giảm Giá:', 'dragonlegend-coupons'); ?> <span style="color:#ef4444;">*</span></label>
                        <select id="_dl_coupon_type" name="_dl_coupon_type" onchange="toggleDiscountType(this.value);" style="font-weight:600;">
                            <option value="percent" <?php selected($type, 'percent'); ?>>🏷️ Giảm theo tỷ lệ phần trăm (%)</option>
                            <option value="fixed" <?php selected($type, 'fixed'); ?>>💵 Giảm theo số tiền cố định (VNĐ)</option>
                        </select>
                        <p class="help-text"><?php _e('Chọn giảm theo % tổng tiền phòng hoặc giảm trừ tiền mặt trực tiếp.', 'dragonlegend-coupons'); ?></p>
                    </div>

                    <div class="dl-form-group">
                        <label for="_dl_coupon_value" id="label-discount-value"><?php echo ($type === 'percent') ? __('Giá Trị Giảm (%):', 'dragonlegend-coupons') : __('Số Tiền Giảm (VNĐ):', 'dragonlegend-coupons'); ?> <span style="color:#ef4444;">*</span></label>
                        <input type="number" id="_dl_coupon_value" name="_dl_coupon_value" value="<?php echo esc_attr($value); ?>" step="any" min="1" placeholder="VD: 10 (cho 10%) hoặc 200000 (cho 200k VNĐ)" style="font-weight:700; font-size:15px; color:#0284c7;" required>
                        <p class="help-text" id="help-discount-value">
                            <?php echo ($type === 'percent') ? __('Nhập số % (ví dụ: 10 nghĩa là giảm 10% tiền phòng).', 'dragonlegend-coupons') : __('Nhập số tiền VNĐ (ví dụ: 500000 nghĩa là giảm 500.000 VNĐ).', 'dragonlegend-coupons'); ?>
                        </p>
                    </div>

                    <div class="dl-form-group" id="group-max-discount" style="<?php echo ($type === 'percent') ? '' : 'display:none;'; ?>">
                        <label for="_dl_coupon_max_discount"><?php _e('Số Tiền Giảm Tối Đa (VNĐ - Tùy chọn):', 'dragonlegend-coupons'); ?></label>
                        <input type="number" id="_dl_coupon_max_discount" name="_dl_coupon_max_discount" value="<?php echo esc_attr($max_discount); ?>" step="10000" min="0" placeholder="VD: 500000 (để trống nếu không giới hạn)">
                        <p class="help-text"><?php _e('Giới hạn mức tiền giảm tối đa để tránh trường hợp khách đặt số ngày lớn làm chiết khấu quá cao.', 'dragonlegend-coupons'); ?></p>
                    </div>

                    <div class="dl-form-group">
                        <label for="_dl_coupon_description"><?php _e('Mô Tả / Tên Chương Trình Ưu Đãi:', 'dragonlegend-coupons'); ?></label>
                        <input type="text" id="_dl_coupon_description" name="_dl_coupon_description" value="<?php echo esc_attr($description); ?>" placeholder="VD: Tri ân khách hàng trực tuyến, Giảm 500k phòng Suite...">
                        <p class="help-text"><?php _e('Hiển thị trên thông báo xác nhận khi khách áp dụng thành công mã này.', 'dragonlegend-coupons'); ?></p>
                    </div>

                    <div class="dl-form-group">
                        <label for="_dl_coupon_status"><?php _e('Trạng Thái Áp Dụng:', 'dragonlegend-coupons'); ?></label>
                        <select id="_dl_coupon_status" name="_dl_coupon_status">
                            <option value="active" <?php selected($status, 'active'); ?>>✅ Đang kích hoạt (Cho phép áp dụng)</option>
                            <option value="inactive" <?php selected($status, 'inactive'); ?>>⏸️ Tạm ngưng (Không cho phép áp dụng)</option>
                        </select>
                    </div>
                </div>

                <!-- CỘT 2: ĐIỀU KIỆN & GIỚI HẠN SỬ DỤNG -->
                <div class="dl-coupon-card">
                    <h4><span class="dashicons dashicons-admin-settings" style="color:#c5a880;"></span> Điều Kiện & Giới Hạn Áp Dụng</h4>

                    <div class="dl-form-group">
                        <label for="_dl_coupon_min_spend"><?php _e('Giá Trị Đơn Hàng Tối Thiểu (VNĐ):', 'dragonlegend-coupons'); ?></label>
                        <input type="number" id="_dl_coupon_min_spend" name="_dl_coupon_min_spend" value="<?php echo esc_attr($min_spend); ?>" step="50000" min="0" placeholder="VD: 1500000 (để trống nếu không yêu cầu)">
                        <p class="help-text"><?php _e('Tổng tiền phòng tạm tính tối thiểu cần đạt trước khi được áp dụng mã.', 'dragonlegend-coupons'); ?></p>
                    </div>

                    <div class="dl-form-group" style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                        <div>
                            <label for="_dl_coupon_usage_limit"><?php _e('Tổng Lượt Dùng Tối Đa:', 'dragonlegend-coupons'); ?></label>
                            <input type="number" id="_dl_coupon_usage_limit" name="_dl_coupon_usage_limit" value="<?php echo esc_attr($usage_limit); ?>" min="1" placeholder="Để trống = vô hạn">
                            <p class="help-text"><?php _e('VD: 50 lượt', 'dragonlegend-coupons'); ?></p>
                        </div>
                        <div>
                            <label for="_dl_coupon_usage_count"><?php _e('Đã Dùng (Lượt):', 'dragonlegend-coupons'); ?></label>
                            <input type="number" id="_dl_coupon_usage_count" name="_dl_coupon_usage_count" value="<?php echo esc_attr($usage_count); ?>" min="0" style="background:#f1f5f9;">
                            <p class="help-text"><?php _e('Tự động tăng khi có đơn', 'dragonlegend-coupons'); ?></p>
                        </div>
                    </div>

                    <div class="dl-form-group" style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                        <div>
                            <label for="_dl_coupon_start_date"><?php _e('Ngày Bắt Đầu Áp Dụng:', 'dragonlegend-coupons'); ?></label>
                            <input type="date" id="_dl_coupon_start_date" name="_dl_coupon_start_date" value="<?php echo esc_attr($start_date); ?>">
                            <p class="help-text"><?php _e('Để trống = Áp dụng ngay', 'dragonlegend-coupons'); ?></p>
                        </div>
                        <div>
                            <label for="_dl_coupon_expiry_date"><?php _e('Ngày Hết Hạn:', 'dragonlegend-coupons'); ?></label>
                            <input type="date" id="_dl_coupon_expiry_date" name="_dl_coupon_expiry_date" value="<?php echo esc_attr($expiry_date); ?>">
                            <p class="help-text"><?php _e('Để trống = Vô thời hạn', 'dragonlegend-coupons'); ?></p>
                        </div>
                    </div>

                    <div class="dl-form-group">
                        <label><?php _e('Áp Dụng Cho Hạng Phòng:', 'dragonlegend-coupons'); ?></label>
                        <p class="help-text" style="margin-top:0; margin-bottom:8px;"><?php _e('Chọn các phòng được phép sử dụng mã này (Bỏ trống hoặc không tích chọn để áp dụng cho TẤT CẢ các phòng):', 'dragonlegend-coupons'); ?></p>
                        <div style="max-height:160px; overflow-y:auto; background:#fff; border:1px solid #cbd5e1; border-radius:6px; padding:10px;">
                            <?php if (!empty($all_rooms)) : ?>
                                <?php foreach ($all_rooms as $r) : ?>
                                    <label style="display:block; margin-bottom:6px; font-weight:normal; font-size:13px; cursor:pointer;">
                                        <input type="checkbox" name="_dl_coupon_rooms[]" value="<?php echo esc_attr($r->ID); ?>" <?php checked(in_array($r->ID, $saved_rooms)); ?>>
                                        <?php echo esc_html($r->post_title); ?>
                                    </label>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <span style="color:#64748b; font-size:12px;">Chưa có dữ liệu phòng nghỉ.</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function toggleDiscountType(type) {
                var maxGroup = document.getElementById('group-max-discount');
                var valLabel = document.getElementById('label-discount-value');
                var valHelp = document.getElementById('help-discount-value');

                if (type === 'percent') {
                    if (maxGroup) maxGroup.style.display = 'block';
                    if (valLabel) valLabel.innerHTML = 'Giá Trị Giảm (%): <span style="color:#ef4444;">*</span>';
                    if (valHelp) valHelp.innerText = 'Nhập số % (ví dụ: 10 nghĩa là giảm 10% tiền phòng).';
                } else {
                    if (maxGroup) maxGroup.style.display = 'none';
                    if (valLabel) valLabel.innerHTML = 'Số Tiền Giảm (VNĐ): <span style="color:#ef4444;">*</span>';
                    if (valHelp) valHelp.innerText = 'Nhập số tiền VNĐ (ví dụ: 500000 nghĩa là giảm 500.000 VNĐ).';
                }
            }
        </script>
        <?php
    }

    public static function save_metaboxes($post_id, $post) {
        if (!isset($_POST['dl_coupon_nonce']) || !wp_verify_nonce($_POST['dl_coupon_nonce'], 'dl_save_coupon_meta')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // 1. Mã Code
        $code = isset($_POST['_dl_coupon_code']) ? sanitize_text_field($_POST['_dl_coupon_code']) : '';
        $code = strtoupper(preg_replace('/[^A-Za-z0-9_-]/', '', $code));
        if (empty($code)) {
            $code = 'DL-' . rand(1000, 9999);
        }
        update_post_meta($post_id, '_dl_coupon_code', $code);

        // Đồng bộ post_title theo mã nếu tiêu đề bài viết trống hoặc tự động
        if (empty($post->post_title) || $post->post_title === 'Auto Draft') {
            remove_action('save_post_dl_coupon', array(__CLASS__, 'save_metaboxes'), 10);
            wp_update_post(array(
                'ID'         => $post_id,
                'post_title' => $code
            ));
            add_action('save_post_dl_coupon', array(__CLASS__, 'save_metaboxes'), 10, 2);
        }

        // 2. Các thông số chiết khấu
        $type = (isset($_POST['_dl_coupon_type']) && $_POST['_dl_coupon_type'] === 'fixed') ? 'fixed' : 'percent';
        update_post_meta($post_id, '_dl_coupon_type', $type);

        $value = isset($_POST['_dl_coupon_value']) ? floatval($_POST['_dl_coupon_value']) : 0;
        update_post_meta($post_id, '_dl_coupon_value', $value);

        $max_discount = !empty($_POST['_dl_coupon_max_discount']) ? floatval($_POST['_dl_coupon_max_discount']) : '';
        update_post_meta($post_id, '_dl_coupon_max_discount', $max_discount);

        $min_spend = !empty($_POST['_dl_coupon_min_spend']) ? floatval($_POST['_dl_coupon_min_spend']) : '';
        update_post_meta($post_id, '_dl_coupon_min_spend', $min_spend);

        $usage_limit = !empty($_POST['_dl_coupon_usage_limit']) ? intval($_POST['_dl_coupon_usage_limit']) : '';
        update_post_meta($post_id, '_dl_coupon_usage_limit', $usage_limit);

        $usage_count = isset($_POST['_dl_coupon_usage_count']) ? intval($_POST['_dl_coupon_usage_count']) : 0;
        update_post_meta($post_id, '_dl_coupon_usage_count', $usage_count);

        $start_date = isset($_POST['_dl_coupon_start_date']) ? sanitize_text_field($_POST['_dl_coupon_start_date']) : '';
        update_post_meta($post_id, '_dl_coupon_start_date', $start_date);

        $expiry_date = isset($_POST['_dl_coupon_expiry_date']) ? sanitize_text_field($_POST['_dl_coupon_expiry_date']) : '';
        update_post_meta($post_id, '_dl_coupon_expiry_date', $expiry_date);

        $status = (isset($_POST['_dl_coupon_status']) && $_POST['_dl_coupon_status'] === 'inactive') ? 'inactive' : 'active';
        update_post_meta($post_id, '_dl_coupon_status', $status);

        $description = isset($_POST['_dl_coupon_description']) ? sanitize_text_field($_POST['_dl_coupon_description']) : '';
        update_post_meta($post_id, '_dl_coupon_description', $description);

        $rooms = isset($_POST['_dl_coupon_rooms']) && is_array($_POST['_dl_coupon_rooms']) 
            ? array_map('intval', $_POST['_dl_coupon_rooms']) 
            : array();
        update_post_meta($post_id, '_dl_coupon_rooms', $rooms);
    }
}
