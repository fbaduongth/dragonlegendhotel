<?php
/**
 * Dragon Legend Hotel Core - Extensible Metabox System
 * Kiến trúc OOP hỗ trợ hook filter để lập trình viên mở rộng thêm trường tùy ý
 *
 * @package DragonLegendCore
 */

if (!defined('ABSPATH')) {
    exit;
}

class DragonLegend_Metaboxes {

    const ROOM_AMENITIES_OPTION       = 'dragonlegend_room_amenities_catalog';
    const ROOM_AMENITIES_TITLE_OPTION = 'dragonlegend_room_amenities_title';

    public static function init() {
        add_action('add_meta_boxes', array(__CLASS__, 'register_metaboxes'));
        add_action('save_post', array(__CLASS__, 'save_metaboxes'), 10, 2);
        add_action('admin_menu', array(__CLASS__, 'register_amenities_submenu'));
        add_action('admin_init', array(__CLASS__, 'handle_amenities_submenu_save'));
    }

    /**
     * Đăng ký trang quản lý danh mục "Tiện Nghi Phòng Cao Cấp" dưới menu Hạng Phòng Nghỉ
     */
    public static function register_amenities_submenu() {
        add_submenu_page(
            'edit.php?post_type=dl_room',
            __('Quản Lý Tiện Nghi Phòng Cao Cấp', 'dragonlegend-core'),
            __('Tiện Nghi Phòng', 'dragonlegend-core'),
            'edit_posts',
            'dl-room-amenities',
            array(__CLASS__, 'render_amenities_submenu_page')
        );
    }

    /**
     * Xử lý lưu trang Quản lý Tiện Nghi Phòng Cao Cấp độc lập
     */
    public static function handle_amenities_submenu_save() {
        if (!isset($_POST['dl_room_amenities_standalone_nonce']) || !wp_verify_nonce($_POST['dl_room_amenities_standalone_nonce'], 'dl_save_room_amenities_standalone')) {
            return;
        }
        if (!current_user_can('edit_posts')) {
            return;
        }

        // Lưu hoặc khôi phục mặc định
        if (!empty($_POST['dl_reset_default_room_amenities'])) {
            $defaults = self::get_default_room_amenities_catalog();
            update_option(self::ROOM_AMENITIES_OPTION, $defaults, true);
            update_option(self::ROOM_AMENITIES_TITLE_OPTION, 'Tiện Nghi Phòng Cao Cấp', true);
            delete_transient('dl_rooms_frontend_cache_v2');
            wp_safe_redirect(add_query_arg(array('page' => 'dl-room-amenities', 'post_type' => 'dl_room', 'dl_saved' => 'reset'), admin_url('edit.php')));
            exit;
        }

        self::process_and_save_amenities_catalog_from_post(0);
        delete_transient('dl_rooms_frontend_cache_v2');

        wp_safe_redirect(add_query_arg(array('page' => 'dl-room-amenities', 'post_type' => 'dl_room', 'dl_saved' => '1'), admin_url('edit.php')));
        exit;
    }

    /**
     * Render trang Quản lý Tiện Nghi Phòng Cao Cấp dưới menu Hạng Phòng Nghỉ
     */
    public static function render_amenities_submenu_page() {
        wp_enqueue_media();
        ?>
        <div class="wrap" style="max-width:1050px; margin-top:20px;">
            <div style="background:linear-gradient(135deg, #07111e 0%, #0f2537 100%); color:#fff; padding:22px 28px; border-radius:12px; margin-bottom:20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; box-shadow:0 4px 15px rgba(0,0,0,0.12);">
                <div>
                    <h1 style="color:#fff; margin:0 0 6px 0; font-size:22px; font-weight:700; display:flex; align-items:center; gap:10px;">
                        Quản Lý Tiện Nghi Phòng Cao Cấp (Database)
                    </h1>
                    <p style="margin:0; color:#cbd5e1; font-size:13.5px;">
                        Thêm, sửa, xoá, sắp xếp thứ tự và thay đổi Icon đại diện cho danh sách Tiện Nghi Phòng Cao Cấp trên toàn website.
                    </p>
                </div>
                <a href="<?php echo esc_url(admin_url('edit.php?post_type=dl_room')); ?>" class="button" style="background:#dfa843; color:#07111e; border:none; font-weight:700; padding:6px 14px; border-radius:6px;">
                    &larr; Danh sách Hạng Phòng
                </a>
            </div>

            <?php if (isset($_GET['dl_saved'])) : ?>
                <div class="notice notice-success is-dismissible" style="margin:0 0 18px 0; border-left-color:#10b981;">
                    <p><strong>
                        <?php echo ($_GET['dl_saved'] === 'reset')
                            ? '✓ Đã khôi phục danh mục Tiện Nghi Phòng Cao Cấp về mặc định chuẩn!'
                            : '✓ Đã lưu danh mục Tiện Nghi Phòng Cao Cấp vào cơ sở dữ liệu thành công!'; ?>
                    </strong></p>
                </div>
            <?php endif; ?>

            <form method="post" action="">
                <?php wp_nonce_field('dl_save_room_amenities_standalone', 'dl_room_amenities_standalone_nonce'); ?>
                <div style="background:#fff; padding:24px; border-radius:12px; border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,0.04);">
                    <?php self::render_room_amenities_manager_ui(0, true); ?>

                    <div style="margin-top:22px; padding-top:18px; border-top:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px;">
                        <button type="submit" class="button button-primary button-large" style="background:#0f766e; border-color:#0d9488; font-weight:700; padding:6px 24px; height:auto;">
                            💾 Lưu Toàn Bộ Thay Đổi Vào Database
                        </button>
                        <button type="submit" name="dl_reset_default_room_amenities" value="1" class="button button-secondary" onclick="return confirm('Bạn có chắc muốn khôi phục danh sách 12 tiện nghi phòng tiêu chuẩn mặc định không?');" style="color:#b91c1c; border-color:#fca5a5;">
                            ↺ Khôi phục mặc định ban đầu
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }

    /**
     * Đăng ký tất cả các Meta Box
     */
    public static function register_metaboxes() {
        // 1. Metabox cho Hạng Phòng Nghỉ
        add_meta_box(
            'dl_room_details',
            __('Thông Số Kỹ Thuật & Tiện Nghi Phòng', 'dragonlegend-core'),
            array(__CLASS__, 'render_room_metabox'),
            'dl_room',
            'normal',
            'high'
        );

        // 2. Metabox Thư Viện Ảnh Phòng Nghỉ
        add_meta_box(
            'dl_room_gallery',
            __('Thư Viện Ảnh Phòng (Gallery)', 'dragonlegend-core'),
            array(__CLASS__, 'render_room_gallery_metabox'),
            'dl_room',
            'normal',
            'high'
        );

        // 3. Metabox cho Gói Ưu Đãi
        add_meta_box(
            'dl_offer_details',
            __('Thông Tin Gói Ưu Đãi & Khuyến Mãi', 'dragonlegend-core'),
            array(__CLASS__, 'render_offer_metabox'),
            'dl_offer',
            'normal',
            'high'
        );

        // 4. Metabox cho Dịch Vụ & Tiện Ích (Hỗ trợ cả Custom Post Type dl_amenity và Bài viết 'post' theo Category)
        add_meta_box(
            'dl_amenity_details',
            __('Thông Tin Dịch Vụ & Tiện Ích Khách Sạn', 'dragonlegend-core'),
            array(__CLASS__, 'render_amenity_metabox'),
            array('dl_amenity', 'post'),
            'normal',
            'high'
        );

        // 5. Metabox cho Đánh Giá Khách Hàng
        add_meta_box(
            'dl_testimonial_details',
            __('Thông Tin Khách Đánh Giá', 'dragonlegend-core'),
            array(__CLASS__, 'render_testimonial_metabox'),
            'dl_testimonial',
            'normal',
            'high'
        );

        // 6. Metabox cho Đơn Đặt Phòng
        add_meta_box(
            'dl_booking_details',
            __('Chi Tiết Đơn Đặt Phòng', 'dragonlegend-core'),
            array(__CLASS__, 'render_booking_metabox'),
            'dl_booking',
            'normal',
            'high'
        );

        // Cho phép các plugin/theme khác thêm metabox tùy biến
        do_action('dragonlegend_add_meta_boxes');
    }

    /**
     * Định nghĩa các trường dữ liệu của Hạng Phòng (Có filter mở rộng)
     */
    public static function get_room_fields($post_id = 0) {
        $fields = array(
            '_dl_room_subtitle' => array(
                'label'       => __('Mô tả ngắn / Slogan phòng', 'dragonlegend-core'),
                'type'        => 'text',
                'description' => __('Hiển thị bên dưới tên phòng (ví dụ: Thanh lịch, ấm cúng và đầy đủ tiện nghi chuẩn 4 sao).', 'dragonlegend-core'),
            ),
            '_dl_room_price' => array(
                'label'       => __('Giá phòng / Đêm (VNĐ)', 'dragonlegend-core'),
                'type'        => 'number',
                'description' => __('Ví dụ: 950000', 'dragonlegend-core'),
            ),
            '_dl_room_original_price' => array(
                'label'       => __('Giá gốc niêm yết (VNĐ)', 'dragonlegend-core'),
                'type'        => 'number',
                'description' => __('Giá gạch nếu có khuyến mãi, ví dụ: 1350000', 'dragonlegend-core'),
            ),
            '_dl_room_size' => array(
                'label'       => __('Diện tích phòng', 'dragonlegend-core'),
                'type'        => 'text',
                'description' => __('Ví dụ: 28 - 32 m²', 'dragonlegend-core'),
            ),
            '_dl_room_bed' => array(
                'label'       => __('Loại giường', 'dragonlegend-core'),
                'type'        => 'text',
                'description' => __('Ví dụ: 1 Giường đôi King hoặc 2 Giường đơn', 'dragonlegend-core'),
            ),
            '_dl_room_occupancy' => array(
                'label'       => __('Sức chứa tối đa', 'dragonlegend-core'),
                'type'        => 'text',
                'description' => __('Ví dụ: 2 Người lớn + 1 Trẻ em', 'dragonlegend-core'),
            ),
            '_dl_room_view' => array(
                'label'       => __('Hướng nhìn / Tầm nhìn', 'dragonlegend-core'),
                'type'        => 'text',
                'description' => __('Ví dụ: Hướng thành phố & Vườn cây Bãi Cháy, Hướng Vịnh biển...', 'dragonlegend-core'),
            ),
            '_dl_room_badge' => array(
                'label'       => __('Huy hiệu nổi bật', 'dragonlegend-core'),
                'type'        => 'text',
                'description' => __('Ví dụ: Phổ biến nhất, Bán chạy, Thượng lưu, Gia đình...', 'dragonlegend-core'),
            ),
            '_dl_room_rating' => array(
                'label'       => __('Điểm đánh giá (Sao)', 'dragonlegend-core'),
                'type'        => 'text',
                'description' => __('Ví dụ: 4.8 hoặc 5.0', 'dragonlegend-core'),
            ),
            '_dl_room_reviews_count' => array(
                'label'       => __('Số lượt đánh giá', 'dragonlegend-core'),
                'type'        => 'number',
                'description' => __('Ví dụ: 142', 'dragonlegend-core'),
            ),
            '_dl_room_amenities' => array(
                'label'       => __('Tiện Nghi Phòng Cao Cấp', 'dragonlegend-core'),
                'type'        => 'room_amenities_manager',
                'options'     => self::get_standard_amenities_list(),
                'description' => __('Tích chọn tiện nghi áp dụng cho phòng này. Bạn có thể Thêm mới, Sửa tên, Xoá hoặc đổi Icon đại diện (chọn SVG có sẵn hoặc tải ảnh/SVG lên) ngay tại bảng trên.', 'dragonlegend-core'),
            ),
        );

        /**
         * Filter: dragonlegend_room_metabox_fields
         * Cho phép mở rộng thêm trường hoặc sửa đổi các trường hiện có của phòng
         */
        return apply_filters('dragonlegend_room_metabox_fields', $fields, $post_id);
    }

    /**
     * Danh mục tiện nghi phòng mặc định ban đầu (Seed Database)
     */
    public static function get_default_room_amenities_catalog() {
        return array(
            'wifi' => array(
                'key'   => 'wifi',
                'label' => __('Wifi cáp quang tốc độ cao miễn phí', 'dragonlegend-core'),
                'icon'  => 'wifi',
            ),
            'coffee' => array(
                'key'   => 'coffee',
                'label' => __('Buffet sáng Quốc tế miễn phí', 'dragonlegend-core'),
                'icon'  => 'coffee',
            ),
            'tv' => array(
                'key'   => 'tv',
                'label' => __('Smart TV 50-55 inch 4K', 'dragonlegend-core'),
                'icon'  => 'tv',
            ),
            'bath' => array(
                'key'   => 'bath',
                'label' => __('Bồn tắm nằm thư giãn & vòi sen đứng', 'dragonlegend-core'),
                'icon'  => 'bath',
            ),
            'snowflake' => array(
                'key'   => 'snowflake',
                'label' => __('Điều hòa 2 chiều êm dịu', 'dragonlegend-core'),
                'icon'  => 'snowflake',
            ),
            'wine' => array(
                'key'   => 'wine',
                'label' => __('Mini Bar & Rượu vang / Trà cà phê', 'dragonlegend-core'),
                'icon'  => 'wine',
            ),
            'shield-check' => array(
                'key'   => 'shield-check',
                'label' => __('Két sắt an toàn điện tử', 'dragonlegend-core'),
                'icon'  => 'shield-check',
            ),
            'sparkles' => array(
                'key'   => 'sparkles',
                'label' => __('Dịch vụ dọn phòng hàng ngày', 'dragonlegend-core'),
                'icon'  => 'sparkles',
            ),
            'sun' => array(
                'key'   => 'sun',
                'label' => __('Ban công riêng ngắm bình minh / hoàng hôn', 'dragonlegend-core'),
                'icon'  => 'sun',
            ),
            'waves' => array(
                'key'   => 'waves',
                'label' => __('Miễn phí vé hồ bơi vô cực Sky Pool', 'dragonlegend-core'),
                'icon'  => 'waves',
            ),
            'gem' => array(
                'key'   => 'gem',
                'label' => __('Đặc quyền check-in VIP Lounge', 'dragonlegend-core'),
                'icon'  => 'gem',
            ),
            'car' => array(
                'key'   => 'car',
                'label' => __('Ưu đãi đưa đón sân bay', 'dragonlegend-core'),
                'icon'  => 'car',
            ),
        );
    }

    /**
     * Lấy danh mục Tiện Nghi Phòng Cao Cấp từ Database (wp_options)
     * Tự động khởi tạo vào database nếu chưa tồn tại.
     */
    public static function get_room_amenities_catalog() {
        $saved = get_option(self::ROOM_AMENITIES_OPTION, false);
        if ($saved === false || !is_array($saved) || empty($saved)) {
            $saved = self::get_default_room_amenities_catalog();
            update_option(self::ROOM_AMENITIES_OPTION, $saved, true);
        }

        $normalized = array();
        foreach ($saved as $k => $item) {
            if (is_array($item)) {
                $item_key   = !empty($item['key']) ? sanitize_key($item['key']) : sanitize_key($k);
                $item_label = isset($item['label']) ? $item['label'] : $item_key;
                $item_icon  = !empty($item['icon']) ? $item['icon'] : $item_key;
                $normalized[$item_key] = array(
                    'key'   => $item_key,
                    'label' => $item_label,
                    'icon'  => $item_icon,
                );
            } elseif (is_string($item)) {
                $item_key = sanitize_key($k);
                $normalized[$item_key] = array(
                    'key'   => $item_key,
                    'label' => $item,
                    'icon'  => $item_key,
                );
            }
        }

        return apply_filters('dragonlegend_room_amenities_catalog', $normalized);
    }

    /**
     * Lấy tiêu đề khối Tiện Nghi Phòng Cao Cấp từ Database
     */
    public static function get_room_amenities_title() {
        $title = get_option(self::ROOM_AMENITIES_TITLE_OPTION, '');
        if (empty($title)) {
            $title = __('Tiện Nghi Phòng Cao Cấp', 'dragonlegend-core');
        }
        return $title;
    }

    /**
     * Danh sách tiện nghi tiêu chuẩn dạng key => label (Tương thích ngược)
     */
    public static function get_standard_amenities_list() {
        $catalog   = self::get_room_amenities_catalog();
        $amenities = array();
        foreach ($catalog as $k => $item) {
            $amenities[$k] = $item['label'];
        }
        return apply_filters('dragonlegend_standard_amenities', $amenities);
    }

    /**
     * Tra cứu thông tin chi tiết 1 tiện nghi phòng (gồm key, label, icon) từ Database
     */
    public static function get_room_amenity_detail($key_or_item) {
        $catalog = self::get_room_amenities_catalog();

        if (is_array($key_or_item)) {
            $k = !empty($key_or_item['key']) ? $key_or_item['key'] : '';
            if ($k && isset($catalog[$k])) {
                return $catalog[$k];
            }
            return array(
                'key'   => $k ?: 'custom',
                'label' => !empty($key_or_item['label']) ? $key_or_item['label'] : (!empty($key_or_item['text']) ? $key_or_item['text'] : ''),
                'icon'  => !empty($key_or_item['icon']) ? $key_or_item['icon'] : 'sparkles',
            );
        }

        $k = (string) $key_or_item;
        if (isset($catalog[$k])) {
            return $catalog[$k];
        }

        // Fallback cho danh mục mặc định nếu bị xoá nhưng bài viết cũ vẫn lưu key
        $defaults = self::get_default_room_amenities_catalog();
        if (isset($defaults[$k])) {
            return $defaults[$k];
        }

        return array(
            'key'   => $k,
            'label' => $k,
            'icon'  => $k,
        );
    }

    /**
     * Danh mục các Icon SVG có sẵn để chọn trong Admin
     */
    public static function get_available_icon_choices() {
        return array(
            'wifi'         => 'WiFi tốc độ cao (wifi)',
            'coffee'       => 'Bữa sáng / Trà & Cà phê (coffee)',
            'tv'           => 'Smart TV 4K (tv)',
            'bath'         => 'Bồn tắm & Vòi sen (bath)',
            'snowflake'    => 'Điều hòa 2 chiều (snowflake)',
            'wine'         => 'Mini Bar & Rượu vang (wine)',
            'shield-check' => 'Két sắt / An ninh 24/7 (shield-check)',
            'sparkles'     => 'Dọn phòng / Sang trọng (sparkles)',
            'sun'          => 'Ban công / View bình minh (sun)',
            'waves'        => 'Hồ bơi Sky Pool / Biển (waves)',
            'gem'          => 'Đặc quyền VIP Lounge (gem)',
            'car'          => 'Đưa đón sân bay / Xe riêng (car)',
            'shuttle'      => 'Xe buýt đưa đón (shuttle)',
            'no-smoking'   => 'Phòng không hút thuốc (no-smoking)',
            'concierge'    => 'Dịch vụ phòng 24/7 (concierge)',
            'family'       => 'Phòng gia đình (family)',
            'restaurant'   => 'Nhà hàng ẩm thực (restaurant)',
            'reception'    => 'Lễ tân 24h (reception)',
            'elevator'     => 'Thang máy hiện đại (elevator)',
            'pool'         => 'Hồ bơi bốn mùa (pool)',
            'parking'      => 'Bãi đỗ xe miễn phí (parking)',
            'gym'          => 'Phòng tập Gym & Yoga (gym)',
            'spa'          => 'Spa & Massage (spa)',
            'conference'   => 'Phòng họp & Hội nghị (conference)',
            'users'        => 'Sức chứa nhóm / Gia đình (users)',
            'tennis'       => 'Thể thao & Giải trí (tennis)',
            'kids'         => 'Khu vui chơi trẻ em (kids)',
            'laundry'      => 'Dịch vụ giặt ủi (laundry)',
            'billiards'    => 'Bàn Bida & Lounge (billiards)',
            'cocktail'     => 'Sky Bar & Cocktail (cocktail)',
            'bicycle'      => 'Xe đạp dạo biển (bicycle)',
            'charging'     => 'Trạm sạc xe điện (charging)',
            'luggage'      => 'Giữ hành lý miễn phí (luggage)',
            'money'        => 'Đổi ngoại tệ / Thanh toán (money)',
        );
    }

    /**
     * Render Metabox Thông số phòng
     */
    public static function render_room_metabox($post) {
        wp_nonce_field('dl_save_meta_box', 'dl_meta_box_nonce');
        $fields = self::get_room_fields($post->ID);
        self::render_fields_table($fields, $post->ID);
    }

    /**
     * Render Metabox Gallery ảnh phòng
     */
    public static function render_room_gallery_metabox($post) {
        $gallery = get_post_meta($post->ID, '_dl_room_gallery', true);
        if (!is_array($gallery)) {
            $gallery = array();
        }
        ?>
        <div class="dl-gallery-container">
            <p class="description"><?php _e('Thêm các hình ảnh thực tế của phòng vào bộ sưu tập ảnh (Gallery).', 'dragonlegend-core'); ?></p>
            <div id="dl-gallery-preview" style="display:flex; flex-wrap:wrap; gap:10px; margin:15px 0;">
                <?php foreach ($gallery as $img_url) : ?>
                    <div class="dl-gallery-item" style="position:relative; width:100px; height:100px; border-radius:6px; overflow:hidden; border:1px solid #ccc;">
                        <img src="<?php echo esc_url($img_url); ?>" style="width:100%; height:100%; object-fit:cover;">
                        <input type="hidden" name="_dl_room_gallery[]" value="<?php echo esc_url($img_url); ?>">
                        <button type="button" class="dl-remove-img" style="position:absolute; top:2px; right:2px; background:rgba(200,0,0,0.8); color:#fff; border:none; border-radius:50%; width:20px; height:20px; cursor:pointer; line-height:18px;">&times;</button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="button button-secondary" id="dl-add-gallery-btn">
                <?php _e('+ Thêm ảnh vào thư viện', 'dragonlegend-core'); ?>
            </button>
        </div>
        <?php
    }

    /**
     * Render Metabox Gói Ưu đãi
     */
    public static function render_offer_metabox($post) {
        wp_nonce_field('dl_save_meta_box', 'dl_meta_box_nonce');
        $fields = array(
            '_dl_offer_tag' => array(
                'label'       => __('Tag phân loại ưu đãi', 'dragonlegend-core'),
                'type'        => 'text',
                'description' => __('Ví dụ: Ưu đãi mùa hè, Tiết kiệm thông minh, Khách đoàn...', 'dragonlegend-core'),
            ),
            '_dl_offer_badge' => array(
                'label'       => __('Huy hiệu / Giảm giá', 'dragonlegend-core'),
                'type'        => 'text',
                'description' => __('Ví dụ: Best Seller, Giảm 20%, Dành cho MICE...', 'dragonlegend-core'),
            ),
            '_dl_offer_price_text' => array(
                'label'       => __('Giá hiển thị', 'dragonlegend-core'),
                'type'        => 'text',
                'description' => __('Ví dụ: Chỉ từ 690.000 VNĐ / Khách', 'dragonlegend-core'),
            ),
            '_dl_offer_expiry' => array(
                'label'       => __('Thời hạn áp dụng', 'dragonlegend-core'),
                'type'        => 'text',
                'description' => __('Ví dụ: Áp dụng đến hết 31/10/2026 hoặc Áp dụng quanh năm', 'dragonlegend-core'),
            ),
            '_dl_offer_benefits' => array(
                'label'       => __('Quyền lợi bao gồm (Mỗi dòng 1 quyền lợi)', 'dragonlegend-core'),
                'type'        => 'textarea',
                'description' => __('Nhập mỗi quyền lợi trên một dòng mới.', 'dragonlegend-core'),
            ),
        );

        $fields = apply_filters('dragonlegend_offer_metabox_fields', $fields, $post->ID);
        self::render_fields_table($fields, $post->ID);
    }

    /**
     * Render Metabox Dịch vụ & Tiện ích
     */
    public static function render_amenity_metabox($post) {
        wp_nonce_field('dl_save_meta_box', 'dl_meta_box_nonce');
        $fields = array(
            '_dl_amenity_location' => array(
                'label'       => __('Vị trí / Tầng', 'dragonlegend-core'),
                'type'        => 'text',
                'description' => __('Ví dụ: Tầng 12 - Tầng cao nhất, Tầng 2 & Tầng M...', 'dragonlegend-core'),
            ),
            '_dl_amenity_highlights' => array(
                'label'       => __('Điểm nổi bật (Mỗi dòng 1 điểm)', 'dragonlegend-core'),
                'type'        => 'textarea',
                'description' => __('Ví dụ: Tầm nhìn Panorama 360°, Nước ấm 4 mùa...', 'dragonlegend-core'),
            ),
            '_dl_amenity_icon' => array(
                'label'       => __('Mã biểu tượng (Icon key)', 'dragonlegend-core'),
                'type'        => 'text',
                'description' => __('Ví dụ: pool, restaurant, ballroom, lotus-spa', 'dragonlegend-core'),
            ),
        );

        $fields = apply_filters('dragonlegend_amenity_metabox_fields', $fields, $post->ID);
        self::render_fields_table($fields, $post->ID);
    }

    /**
     * Render Metabox Đánh giá khách hàng
     */
    public static function render_testimonial_metabox($post) {
        wp_nonce_field('dl_save_meta_box', 'dl_meta_box_nonce');
        $fields = array(
            '_dl_testimonial_location' => array(
                'label'       => __('Quốc gia / Tỉnh thành của khách', 'dragonlegend-core'),
                'type'        => 'text',
                'description' => __('Ví dụ: Canada, Nam Định, Hà Nội...', 'dragonlegend-core'),
            ),
            '_dl_testimonial_rating' => array(
                'label'       => __('Số sao đánh giá (1 - 5)', 'dragonlegend-core'),
                'type'        => 'number',
                'description' => __('Mặc định: 5 sao', 'dragonlegend-core'),
            ),
            '_dl_testimonial_date' => array(
                'label'       => __('Thời gian lưu trú', 'dragonlegend-core'),
                'type'        => 'text',
                'description' => __('Ví dụ: Tháng 2, 2026', 'dragonlegend-core'),
            ),
            '_dl_testimonial_title' => array(
                'label'       => __('Tiêu đề nhận xét', 'dragonlegend-core'),
                'type'        => 'text',
                'description' => __('Ví dụ: Vượt qua sự mong đợi của tôi!', 'dragonlegend-core'),
            ),
        );

        $fields = apply_filters('dragonlegend_testimonial_metabox_fields', $fields, $post->ID);
        self::render_fields_table($fields, $post->ID);
    }

    /**
     * Render Metabox Đơn Đặt Phòng
     */
    public static function render_booking_metabox($post) {
        wp_nonce_field('dl_save_meta_box', 'dl_meta_box_nonce');

        $code        = get_the_title($post->ID);
        if (empty($code) || $code === 'Auto Draft' || $code === __('Auto Draft')) {
            $code = __('Tự động tạo khi lưu (DL-XXXXXX)', 'dragonlegend-core');
        }
        $name        = get_post_meta($post->ID, '_dl_booking_guest_name', true);
        $phone       = get_post_meta($post->ID, '_dl_booking_guest_phone', true);
        $email       = get_post_meta($post->ID, '_dl_booking_guest_email', true);
        $note        = get_post_meta($post->ID, '_dl_booking_guest_note', true);
        $room_name   = get_post_meta($post->ID, '_dl_booking_room_name', true);
        $checkin     = get_post_meta($post->ID, '_dl_booking_checkin', true);
        $checkout    = get_post_meta($post->ID, '_dl_booking_checkout', true);
        $nights      = intval(get_post_meta($post->ID, '_dl_booking_nights', true));
        $rooms       = intval(get_post_meta($post->ID, '_dl_booking_rooms', true)) ?: 1;
        $adults      = intval(get_post_meta($post->ID, '_dl_booking_adults', true)) ?: 2;
        $children    = intval(get_post_meta($post->ID, '_dl_booking_children', true));
        $promo_code  = get_post_meta($post->ID, '_dl_booking_promo_code', true);
        $discount_type = get_post_meta($post->ID, '_dl_booking_discount_type', true) ?: 'percent';
        $discount_percent = intval(get_post_meta($post->ID, '_dl_booking_discount_percent', true));
        $discount    = floatval(get_post_meta($post->ID, '_dl_booking_discount_amount', true));
        $grand_total = floatval(get_post_meta($post->ID, '_dl_booking_grand_total', true));
        $status      = get_post_meta($post->ID, '_dl_booking_status', true);
        if (!$status) $status = 'pending';

        // Lấy thông tin & trạng thái gửi thông báo (Zalo & Email)
        $zalo_guest_status  = get_post_meta($post->ID, '_dl_booking_zalo_guest_status', true);
        $zalo_admin_status  = get_post_meta($post->ID, '_dl_booking_zalo_admin_status', true);
        $email_admin_status = get_post_meta($post->ID, '_dl_booking_email_admin_status', true);
        $email_guest_status = get_post_meta($post->ID, '_dl_booking_email_guest_status', true);
        $notification_logs  = get_post_meta($post->ID, '_dl_booking_notification_logs', true);
        if (!is_array($notification_logs)) {
            $notification_logs = array();
        }

        $settings          = class_exists('DragonLegend_Zalo_Service') ? DragonLegend_Zalo_Service::get_settings() : array();
        $admin_zalo_phones = !empty($settings['admin_zalo_phones']) ? $settings['admin_zalo_phones'] : 'Chưa cấu hình';
        $admin_emails      = !empty($settings['admin_notification_emails']) ? $settings['admin_notification_emails'] : get_option('admin_email');
        $zalo_mode         = !empty($settings['zalo_mode']) ? $settings['zalo_mode'] : 'simulation';

        $available_rooms = get_posts(array(
            'post_type'      => 'dl_room',
            'posts_per_page' => -1,
            'post_status'    => 'publish',
        ));
        ?>
        <div class="dl-booking-detail-wrap" style="background:#fff; padding:15px; border-radius:8px;">
            <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:2px solid #c5a880; padding-bottom:12px; margin-bottom:15px; flex-wrap:wrap; gap:10px;">
                <h3 style="margin:0; font-size:18px; color:#0b1a2d;">
                    Mã đơn: <span style="color:#c5a880;"><?php echo esc_html($code); ?></span>
                </h3>
                <div>
                    <label style="font-weight:bold; margin-right:8px;"><?php _e('Trạng thái đơn:', 'dragonlegend-core'); ?></label>
                    <select name="_dl_booking_status" style="padding:6px 12px; font-weight:bold; border-radius:4px;">
                        <option value="pending" <?php selected($status, 'pending'); ?>>⏳ Chờ xác nhận</option>
                        <option value="confirmed" <?php selected($status, 'confirmed'); ?>>✅ Đã xác nhận</option>
                        <option value="completed" <?php selected($status, 'completed'); ?>>🎉 Đã hoàn thành</option>
                        <option value="cancelled" <?php selected($status, 'cancelled'); ?>>❌ Đã hủy</option>
                    </select>
                </div>
            </div>

            <table class="form-table" style="margin-top:0;">
                <tr>
                    <th style="width:200px;"><label for="_dl_booking_guest_name"><?php _e('Họ tên khách hàng:', 'dragonlegend-core'); ?></label></th>
                    <td>
                        <input type="text" id="_dl_booking_guest_name" name="_dl_booking_guest_name" value="<?php echo esc_attr($name); ?>" class="regular-text" placeholder="Nguyễn Văn A" style="font-weight:600;">
                    </td>
                </tr>
                <tr>
                    <th><label for="_dl_booking_guest_phone"><?php _e('Số điện thoại:', 'dragonlegend-core'); ?></label></th>
                    <td>
                        <input type="text" id="_dl_booking_guest_phone" name="_dl_booking_guest_phone" value="<?php echo esc_attr($phone); ?>" class="regular-text" placeholder="0912 345 678">
                        <?php if ($phone) : ?>
                            <a href="tel:<?php echo esc_attr($phone); ?>" class="button button-small" style="margin-left:8px;">📞 Gọi điện</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th><label for="_dl_booking_guest_email"><?php _e('Email liên hệ:', 'dragonlegend-core'); ?></label></th>
                    <td>
                        <input type="email" id="_dl_booking_guest_email" name="_dl_booking_guest_email" value="<?php echo esc_attr($email); ?>" class="regular-text" placeholder="email@example.com">
                        <?php if ($email) : ?>
                            <a href="mailto:<?php echo esc_attr($email); ?>" class="button button-small" style="margin-left:8px;">✉️ Gửi email</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th><label for="_dl_booking_room_name"><?php _e('Hạng phòng:', 'dragonlegend-core'); ?></label></th>
                    <td>
                        <select id="_dl_booking_room_name" name="_dl_booking_room_name" style="min-width:240px;">
                            <option value=""><?php _e('-- Chọn hạng phòng --', 'dragonlegend-core'); ?></option>
                            <?php foreach ($available_rooms as $r) : ?>
                                <option value="<?php echo esc_attr($r->post_title); ?>" <?php selected($room_name, $r->post_title); ?>>
                                    <?php echo esc_html($r->post_title); ?>
                                </option>
                            <?php endforeach; ?>
                            <?php if ($room_name && !in_array($room_name, wp_list_pluck($available_rooms, 'post_title'))) : ?>
                                <option value="<?php echo esc_attr($room_name); ?>" selected><?php echo esc_html($room_name); ?></option>
                            <?php endif; ?>
                        </select>
                        <span style="margin-left:12px;">Số lượng: </span>
                        <input type="number" name="_dl_booking_rooms" value="<?php echo esc_attr($rooms); ?>" min="1" style="width:65px;"> phòng
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Thời gian lưu trú:', 'dragonlegend-core'); ?></th>
                    <td>
                        Nhận: <input type="date" name="_dl_booking_checkin" value="<?php echo esc_attr($checkin); ?>" style="padding:4px 8px;"> &rarr; 
                        Trả: <input type="date" name="_dl_booking_checkout" value="<?php echo esc_attr($checkout); ?>" style="padding:4px 8px;">
                        <span style="margin-left:8px;">(<em><?php echo intval($nights); ?> đêm</em>)</span>
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Số lượng khách:', 'dragonlegend-core'); ?></th>
                    <td>
                        <input type="number" name="_dl_booking_adults" value="<?php echo esc_attr($adults); ?>" min="1" style="width:65px;"> Người lớn, &nbsp;
                        <input type="number" name="_dl_booking_children" value="<?php echo esc_attr($children); ?>" min="0" style="width:65px;"> Trẻ em
                    </td>
                </tr>
                <tr>
                    <th><?php _e('Mã ưu đãi / Giảm giá:', 'dragonlegend-core'); ?></th>
                    <td>
                        Mã: <input type="text" name="_dl_booking_promo_code" value="<?php echo esc_attr($promo_code); ?>" style="width:130px; font-weight:bold; text-transform:uppercase;"> &nbsp;
                        Giảm: <input type="number" name="_dl_booking_discount_amount" value="<?php echo esc_attr($discount); ?>" style="width:140px;"> VNĐ
                        <?php if (!empty($promo_code)) : ?>
                            <span style="margin-left:8px; display:inline-flex; align-items:center; gap:4px; font-size:12px; background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; padding:2px 8px; border-radius:4px; font-weight:600;">
                                <?php echo ($discount_type === 'fixed') ? '💵 Giảm tiền mặt' : ('🏷️ Giảm ' . intval($discount_percent) . '%'); ?>
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th><label for="_dl_booking_grand_total"><?php _e('Tổng thanh toán dự kiến:', 'dragonlegend-core'); ?></label></th>
                    <td>
                        <input type="number" id="_dl_booking_grand_total" name="_dl_booking_grand_total" value="<?php echo esc_attr($grand_total); ?>" style="font-size:16px; font-weight:bold; color:#d63031; width:200px;"> VNĐ
                        <?php if ($grand_total > 0) : ?>
                            <span style="margin-left:10px; color:#475569;">(Định dạng: <strong><?php echo number_format(floatval($grand_total), 0, ',', '.'); ?> VNĐ</strong> - Đã gồm 8% VAT)</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th><label for="_dl_booking_guest_note"><?php _e('Ghi chú của khách:', 'dragonlegend-core'); ?></label></th>
                    <td>
                        <textarea id="_dl_booking_guest_note" name="_dl_booking_guest_note" rows="3" class="large-text" placeholder="Yêu cầu đặt biệt hoặc ghi chú từ khách hàng..."><?php echo esc_textarea($note); ?></textarea>
                    </td>
                </tr>
            </table>

            <!-- KHỐI QUẢN LÝ THÔNG BÁO TỰ ĐỘNG (ZALO & EMAIL) -->
            <div class="dl-booking-notifications-box" style="margin-top:25px; padding:20px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px;">
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px; flex-wrap:wrap; gap:10px;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span style="font-size:20px;">🔔</span>
                        <h4 style="margin:0; font-size:15px; font-weight:700; color:#0f172a;">
                            <?php _e('Trạng Thái Thông Báo Tự Động (Zalo & Email)', 'dragonlegend-core'); ?>
                        </h4>
                        <span style="font-size:11px; background:#e0e7ff; color:#3730a3; padding:2px 8px; border-radius:10px; font-weight:600;">
                            Chế độ Zalo: <?php echo esc_html(strtoupper($zalo_mode)); ?>
                        </span>
                    </div>
                    <div>
                        <a href="<?php echo esc_url(admin_url('edit.php?post_type=dl_booking&page=dl-booking-settings')); ?>" class="button button-secondary button-small" target="_blank" style="font-weight:600;">
                            ⚙️ <?php _e('Cấu hình Zalo & Email', 'dragonlegend-core'); ?>
                        </a>
                    </div>
                </div>

                <!-- Lưới 4 thẻ trạng thái kênh thông báo -->
                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(210px, 1fr)); gap:12px; margin-bottom:15px;">
                    <div style="background:#fff; padding:12px 14px; border-radius:8px; border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,0.02);">
                        <div style="font-size:12px; font-weight:700; color:#0284c7; margin-bottom:4px; display:flex; align-items:center; gap:6px;">
                            <span>💬</span> Zalo Khách Hàng
                        </div>
                        <div style="font-size:12px; color:#475569; margin-bottom:6px; word-break:break-all;">
                            SĐT: <strong><?php echo esc_html($phone ?: 'Chưa có SĐT'); ?></strong>
                        </div>
                        <div><?php echo self::render_notification_badge($zalo_guest_status); ?></div>
                    </div>

                    <div style="background:#fff; padding:12px 14px; border-radius:8px; border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,0.02);">
                        <div style="font-size:12px; font-weight:700; color:#0284c7; margin-bottom:4px; display:flex; align-items:center; gap:6px;">
                            <span>📱</span> Zalo Quản Trị / Sale
                        </div>
                        <div style="font-size:12px; color:#475569; margin-bottom:6px; word-break:break-all;">
                            SĐT: <strong><?php echo esc_html($admin_zalo_phones); ?></strong>
                        </div>
                        <div><?php echo self::render_notification_badge($zalo_admin_status); ?></div>
                    </div>

                    <div style="background:#fff; padding:12px 14px; border-radius:8px; border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,0.02);">
                        <div style="font-size:12px; font-weight:700; color:#4f46e5; margin-bottom:4px; display:flex; align-items:center; gap:6px;">
                            <span>✉️</span> Email Quản Trị
                        </div>
                        <div style="font-size:12px; color:#475569; margin-bottom:6px; word-break:break-all;">
                            Tới: <strong><?php echo esc_html($admin_emails); ?></strong>
                        </div>
                        <div><?php echo self::render_notification_badge($email_admin_status); ?></div>
                    </div>

                    <div style="background:#fff; padding:12px 14px; border-radius:8px; border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,0.02);">
                        <div style="font-size:12px; font-weight:700; color:#4f46e5; margin-bottom:4px; display:flex; align-items:center; gap:6px;">
                            <span>📧</span> Email Khách Hàng
                        </div>
                        <div style="font-size:12px; color:#475569; margin-bottom:6px; word-break:break-all;">
                            Khách: <strong><?php echo esc_html($email ?: 'Không cung cấp'); ?></strong>
                        </div>
                        <div><?php echo self::render_notification_badge($email_guest_status); ?></div>
                    </div>
                </div>

                <!-- Thao tác gửi lại thông báo -->
                <div style="display:flex; align-items:center; gap:10px; margin-bottom:15px; flex-wrap:wrap;">
                    <span style="font-weight:600; font-size:12px; color:#334155;">Thao tác nhanh:</span>
                    <button type="button" class="button button-primary dl-resend-notification-btn" data-channel="all" data-id="<?php echo esc_attr($post->ID); ?>">
                        🔄 Gửi lại toàn bộ (Zalo & Email)
                    </button>
                    <button type="button" class="button button-secondary dl-resend-notification-btn" data-channel="zalo" data-id="<?php echo esc_attr($post->ID); ?>">
                        💬 Chỉ gửi lại Zalo
                    </button>
                    <button type="button" class="button button-secondary dl-resend-notification-btn" data-channel="email" data-id="<?php echo esc_attr($post->ID); ?>">
                        ✉️ Chỉ gửi lại Email
                    </button>
                    <span id="dl-resend-spinner" class="spinner" style="float:none; margin:0;"></span>
                </div>

                <div id="dl-resend-feedback" style="display:none; margin-bottom:15px; padding:10px 14px; border-radius:6px; font-size:13px; font-weight:600;"></div>

                <!-- Nhật ký lịch sử gửi thông báo -->
                <details style="background:#fff; border:1px solid #e2e8f0; border-radius:8px; overflow:hidden;" <?php echo !empty($notification_logs) ? 'open' : ''; ?>>
                    <summary style="padding:10px 14px; background:#f1f5f9; cursor:pointer; font-weight:700; font-size:13px; color:#1e293b; user-select:none;">
                        📋 Nhật ký thông báo (<?php echo count($notification_logs); ?> lượt ghi nhận)
                    </summary>
                    <div style="padding:12px 14px;">
                        <?php if (empty($notification_logs)) : ?>
                            <div style="color:#64748b; font-size:13px; font-style:italic;">
                                Chưa có nhật ký gửi thông báo nào cho đơn đặt phòng này.
                            </div>
                        <?php else : ?>
                            <table class="wp-list-table widefat striped" style="font-size:12px;">
                                <thead>
                                    <tr>
                                        <th style="width:145px;">Thời gian</th>
                                        <th style="width:140px;">Kênh</th>
                                        <th>Nội dung / Phản hồi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $logs_reversed = array_reverse($notification_logs);
                                    foreach ($logs_reversed as $log) : 
                                        $l_time_val = !empty($log['time']) ? $log['time'] : (!empty($log['timestamp']) ? $log['timestamp'] : '');
                                        $l_time = $l_time_val ? date_i18n('H:i:s d/m/Y', strtotime($l_time_val)) : '—';
                                        $l_channel = isset($log['channel']) ? $log['channel'] : 'Hệ thống';
                                        $l_msg = !empty($log['detail']) ? $log['detail'] : (!empty($log['message']) ? $log['message'] : '');
                                    ?>
                                        <tr>
                                            <td style="color:#64748b;"><?php echo esc_html($l_time); ?></td>
                                            <td><strong><?php echo esc_html($l_channel); ?></strong></td>
                                            <td style="word-break:break-word;"><?php echo esc_html($l_msg); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </details>

                <script type="text/javascript">
                jQuery(document).ready(function($) {
                    $('.dl-resend-notification-btn').on('click', function(e) {
                        e.preventDefault();
                        var $btn = $(this);
                        var channel = $btn.data('channel');
                        var postId = $btn.data('id');
                        var $spinner = $('#dl-resend-spinner');
                        var $fb = $('#dl-resend-feedback');

                        $btn.prop('disabled', true);
                        $spinner.addClass('is-active');
                        $fb.hide();

                        $.post(ajaxurl, {
                            action: 'dl_resend_booking_notifications',
                            nonce: '<?php echo wp_create_nonce('dl_booking_metabox_nonce'); ?>',
                            post_id: postId,
                            channel: channel
                        }, function(res) {
                            $spinner.removeClass('is-active');
                            $btn.prop('disabled', false);

                            if (res && res.success) {
                                $fb.css({
                                    'background': '#dcfce7',
                                    'color': '#15803d',
                                    'border': '1px solid #86efac'
                                }).text('✓ ' + (res.data.message || 'Thành công')).slideDown();
                                setTimeout(function() {
                                    window.location.reload();
                                }, 1200);
                            } else {
                                var msg = (res && res.data && res.data.message) ? res.data.message : 'Có lỗi xảy ra khi gửi lại.';
                                $fb.css({
                                    'background': '#fee2e2',
                                    'color': '#b91c1c',
                                    'border': '1px solid #fca5a5'
                                }).text('✕ ' + msg).slideDown();
                            }
                        }).fail(function() {
                            $spinner.removeClass('is-active');
                            $btn.prop('disabled', false);
                            $fb.css({
                                'background': '#fee2e2',
                                'color': '#b91c1c',
                                'border': '1px solid #fca5a5'
                            }).text('✕ Lỗi kết nối tới máy chủ. Vui lòng thử lại.').slideDown();
                        });
                    });
                });
                </script>
            </div>
        </div>
        <?php
    }

    /**
     * Render huy hiệu trạng thái thông báo
     */
    public static function render_notification_badge($status) {
        switch ($status) {
            case 'success':
                return '<span style="background:#10b981; color:#fff; padding:2px 8px; border-radius:12px; font-size:11px; font-weight:600; display:inline-flex; align-items:center; gap:3px;">✓ Thành công</span>';
            case 'simulated':
                return '<span style="background:#6366f1; color:#fff; padding:2px 8px; border-radius:12px; font-size:11px; font-weight:600; display:inline-flex; align-items:center; gap:3px;">⚡ Mô phỏng</span>';
            case 'failed':
                return '<span style="background:#ef4444; color:#fff; padding:2px 8px; border-radius:12px; font-size:11px; font-weight:600; display:inline-flex; align-items:center; gap:3px;">✕ Thất bại</span>';
            case 'disabled':
                return '<span style="background:#94a3b8; color:#fff; padding:2px 8px; border-radius:12px; font-size:11px; font-weight:600; display:inline-flex; align-items:center; gap:3px;">○ Đang tắt</span>';
            default:
                return '<span style="background:#e2e8f0; color:#64748b; padding:2px 8px; border-radius:12px; font-size:11px; font-weight:600; display:inline-flex; align-items:center; gap:3px;">— Chưa gửi</span>';
        }
    }

    /**
     * Helper render bảng các trường input
     */
    private static function render_fields_table($fields, $post_id) {
        echo '<table class="form-table dl-metabox-table">';
        foreach ($fields as $key => $field) {
            $value = get_post_meta($post_id, $key, true);
            $label = isset($field['label']) ? $field['label'] : $key;
            $type  = isset($field['type']) ? $field['type'] : 'text';
            $desc  = isset($field['description']) ? $field['description'] : '';

            echo '<tr>';
            echo '<th scope="row"><label for="' . esc_attr($key) . '">' . esc_html($label) . '</label></th>';
            echo '<td>';

            switch ($type) {
                case 'number':
                    echo '<input type="number" id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '" class="regular-text" step="any">';
                    break;

                case 'textarea':
                    $rows = isset($field['rows']) ? intval($field['rows']) : 4;
                    echo '<textarea id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" rows="' . $rows . '" class="large-text">' . esc_textarea($value) . '</textarea>';
                    break;

                case 'select':
                    echo '<select id="' . esc_attr($key) . '" name="' . esc_attr($key) . '">';
                    if (!empty($field['options']) && is_array($field['options'])) {
                        foreach ($field['options'] as $opt_key => $opt_label) {
                            echo '<option value="' . esc_attr($opt_key) . '" ' . selected($value, $opt_key, false) . '>' . esc_html($opt_label) . '</option>';
                        }
                    }
                    echo '</select>';
                    break;

                case 'room_amenities_manager':
                    self::render_room_amenities_manager_ui($post_id, false);
                    break;

                case 'checkbox_list':
                    $selected = is_array($value) ? $value : array();
                    echo '<div class="dl-checkbox-grid" style="display:grid; grid-template-columns:repeat(auto-fill, minmax(280px, 1fr)); gap:8px;">';
                    if (!empty($field['options']) && is_array($field['options'])) {
                        foreach ($field['options'] as $opt_key => $opt_label) {
                            $checked = in_array($opt_key, $selected) ? 'checked' : '';
                            echo '<label style="display:flex; align-items:center; gap:6px;">';
                            echo '<input type="checkbox" name="' . esc_attr($key) . '[]" value="' . esc_attr($opt_key) . '" ' . $checked . '> ';
                            echo esc_html($opt_label);
                            echo '</label>';
                        }
                    }
                    echo '</div>';
                    break;

                case 'text':
                default:
                    echo '<input type="text" id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '" class="regular-text">';
                    break;
            }

            if ($desc) {
                echo '<p class="description" style="margin-top:4px;">' . esc_html($desc) . '</p>';
            }

            echo '</td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    /**
     * Render giao diện Quản lý Tiện Nghi Phòng Cao Cấp (Thêm / Sửa / Xoá + Chọn/Tải lên Icon đại diện)
     */
    public static function render_room_amenities_manager_ui($post_id = 0, $is_standalone = false) {
        $catalog      = self::get_room_amenities_catalog();
        $icon_choices = self::get_available_icon_choices();
        $sec_title    = self::get_room_amenities_title();

        $selected_keys = array();
        if ($post_id > 0) {
            $raw_selected = get_post_meta($post_id, '_dl_room_amenities', true);
            if (is_array($raw_selected)) {
                foreach ($raw_selected as $rk) {
                    if (is_array($rk) && !empty($rk['key'])) {
                        $selected_keys[] = $rk['key'];
                        if (!isset($catalog[$rk['key']])) {
                            $catalog[$rk['key']] = array(
                                'key'   => $rk['key'],
                                'label' => !empty($rk['label']) ? $rk['label'] : $rk['key'],
                                'icon'  => !empty($rk['icon']) ? $rk['icon'] : 'sparkles',
                            );
                        }
                    } elseif (is_string($rk) && $rk !== '') {
                        $selected_keys[] = $rk;
                        if (!isset($catalog[$rk])) {
                            $detail = self::get_room_amenity_detail($rk);
                            $catalog[$rk] = $detail;
                        }
                    }
                }
            }
        }

        $items_list = array_values($catalog);
        ?>
        <div class="dl-room-amenities-manager-wrap" style="background:#f8fafc; border:1px solid #cbd5e1; border-radius:10px; padding:16px;">
            <input type="hidden" name="dl_room_amenities_catalog_submitted" value="1">

            <!-- Tiêu đề khối hiển thị ngoài Frontend -->
            <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:14px; padding-bottom:12px; border-bottom:1px solid #e2e8f0;">
                <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap; flex:1;">
                    <label for="dragonlegend_room_amenities_title" style="font-weight:700; color:#0f172a; font-size:13px;">
                        🏷️ <?php _e('Tiêu đề mục trên trang phòng:', 'dragonlegend-core'); ?>
                    </label>
                    <input type="text" id="dragonlegend_room_amenities_title" name="dragonlegend_room_amenities_title" value="<?php echo esc_attr($sec_title); ?>" style="min-width:260px; font-weight:700; color:#0b1a2d; border-color:#cbd5e1; border-radius:6px; padding:4px 10px;" placeholder="Tiện Nghi Phòng Cao Cấp">
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <?php if ($post_id > 0) : ?>
                        <button type="button" class="button button-small" id="dl-room-am-check-all" style="font-size:12px;">☑️ Chọn tất cả</button>
                        <button type="button" class="button button-small" id="dl-room-am-uncheck-all" style="font-size:12px;">☐ Bỏ chọn</button>
                    <?php endif; ?>
                    <button type="button" class="button button-primary" id="dl-btn-add-room-amenity" style="background:#0f766e; border-color:#0d9488; font-weight:600; display:inline-flex; align-items:center; gap:5px;">
                        <span>+</span> <?php _e('Thêm tiện nghi mới', 'dragonlegend-core'); ?>
                    </button>
                </div>
            </div>

            <div style="overflow-x:auto; background:#fff; border:1px solid #e2e8f0; border-radius:8px;">
                <table class="wp-list-table widefat striped" id="dl-room-amenities-table" style="border:none; margin:0; min-width:680px;">
                    <thead>
                        <tr style="background:#07111e;">
                            <?php if ($post_id > 0) : ?>
                                <th style="width:70px; text-align:center; color:#dfa843; font-weight:700; padding:10px 6px;">Có sẵn</th>
                            <?php endif; ?>
                            <th style="width:70px; text-align:center; color:#dfa843; font-weight:700; padding:10px 6px;">Thứ tự</th>
                            <th style="width:270px; color:#dfa843; font-weight:700; padding:10px;">Icon đại diện (SVG / Tải ảnh)</th>
                            <th style="color:#dfa843; font-weight:700; padding:10px;">Tên tiện nghi phòng hiển thị</th>
                            <th style="width:65px; text-align:center; color:#dfa843; font-weight:700; padding:10px 6px;">Xoá</th>
                        </tr>
                    </thead>
                    <tbody id="dl-room-amenities-tbody">
                        <?php foreach ($items_list as $idx => $item) :
                            $item_key   = isset($item['key']) ? $item['key'] : 'item-' . $idx;
                            $item_label = isset($item['label']) ? $item['label'] : '';
                            $item_icon  = !empty($item['icon']) ? $item['icon'] : $item_key;
                            $is_checked = ($post_id > 0 && in_array($item_key, $selected_keys, true));
                        ?>
                        <tr class="dl-room-amenity-row" data-index="<?php echo esc_attr($idx); ?>">
                            <?php if ($post_id > 0) : ?>
                                <td style="text-align:center; vertical-align:middle; padding:8px 6px;">
                                    <input type="checkbox" class="dl-room-am-checkbox" name="dl_amenity_items[<?php echo esc_attr($idx); ?>][enabled]" value="1" <?php checked($is_checked); ?> style="width:18px; height:18px; margin:0;">
                                </td>
                            <?php endif; ?>
                            <td style="text-align:center; vertical-align:middle; padding:8px 6px;">
                                <div style="display:flex; align-items:center; justify-content:center; gap:4px;">
                                    <span class="dl-room-am-order-num" style="display:inline-flex; align-items:center; justify-content:center; width:22px; height:22px; border-radius:50%; background:#07111e; color:#dfa843; font-size:11px; font-weight:700;">
                                        <?php echo intval($idx + 1); ?>
                                    </span>
                                    <div style="display:flex; flex-direction:column; gap:2px;">
                                        <button type="button" class="dl-room-am-up" title="Lên trên" style="border:1px solid #cbd5e1; background:#f8fafc; cursor:pointer; border-radius:3px; font-size:8px; line-height:1; padding:2px 4px;">▲</button>
                                        <button type="button" class="dl-room-am-down" title="Xuống dưới" style="border:1px solid #cbd5e1; background:#f8fafc; cursor:pointer; border-radius:3px; font-size:8px; line-height:1; padding:2px 4px;">▼</button>
                                    </div>
                                </div>
                            </td>
                            <td style="vertical-align:middle; padding:8px 10px;">
                                <div style="display:flex; align-items:center; gap:6px;">
                                    <div class="dl-room-am-icon-preview" style="width:36px; height:36px; border-radius:8px; background:#fffbeb; border:1px solid #fde68a; color:#d97706; display:flex; align-items:center; justify-content:center; flex-shrink:0; overflow:hidden;">
                                        <?php echo function_exists('dl_get_amenity_svg') ? dl_get_amenity_svg($item_icon, 'w-5 h-5 text-gold') : '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3z"/></svg>'; ?>
                                    </div>
                                    <select class="dl-room-am-icon-select" name="dl_amenity_items[<?php echo esc_attr($idx); ?>][icon]" style="flex:1; max-width:165px; font-size:12px; height:32px;">
                                        <?php if (!empty($item_icon) && !isset($icon_choices[$item_icon])) : ?>
                                            <option value="<?php echo esc_attr($item_icon); ?>" selected>
                                                Icon tải lên (<?php echo esc_html(basename(parse_url($item_icon, PHP_URL_PATH))); ?>)
                                            </option>
                                        <?php endif; ?>
                                        <?php foreach ($icon_choices as $ic_key => $ic_label) : ?>
                                            <option value="<?php echo esc_attr($ic_key); ?>" <?php selected($item_icon, $ic_key); ?>>
                                                <?php echo esc_html($ic_label); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="button" class="button button-small dl-room-am-upload-icon" title="Tải ảnh hoặc SVG từ Thư viện Media" style="height:32px; padding:0 8px; display:inline-flex; align-items:center; justify-content:center; color:#0f766e; border-color:#cbd5e1; background:#fff;">
                                        <span class="dashicons dashicons-upload" style="font-size:15px; width:15px; height:15px;"></span>
                                    </button>
                                </div>
                            </td>
                            <td style="vertical-align:middle; padding:8px 10px;">
                                <input type="hidden" class="dl-room-am-key-input" name="dl_amenity_items[<?php echo esc_attr($idx); ?>][key]" value="<?php echo esc_attr($item_key); ?>">
                                <input type="text" class="dl-room-am-label-input" name="dl_amenity_items[<?php echo esc_attr($idx); ?>][label]" value="<?php echo esc_attr($item_label); ?>" placeholder="VD: Wifi cáp quang tốc độ cao miễn phí" style="width:100%; font-weight:600; color:#0f172a; height:34px; border-radius:6px; border:1px solid #cbd5e1; padding:0 10px;">
                            </td>
                            <td style="text-align:center; vertical-align:middle; padding:8px 6px;">
                                <button type="button" class="dl-room-am-delete" title="Xoá tiện nghi này" style="background:#fef2f2; border:1px solid #fecaca; color:#dc2626; border-radius:6px; padding:5px 8px; cursor:pointer; font-weight:700;">
                                    ✕
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div style="margin-top:12px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px;">
                <button type="button" class="button button-secondary" id="dl-btn-add-room-amenity-bottom" style="color:#0f766e; border-color:#0f766e; font-weight:600;">
                    + <?php _e('Thêm tiện nghi phòng mới', 'dragonlegend-core'); ?>
                </button>
                <span style="font-size:12px; color:#475569;">
                    💡 <em>Bạn có thể chọn Icon SVG có sẵn, hoặc bấm nút <strong>📁</strong> để tải lên file ảnh/SVG riêng. Mọi thay đổi được lưu trực tiếp vào Database khi bấm <strong>Cập nhật / Lưu</strong>.</em>
                </span>
            </div>
        </div>

        <style>
            .dl-room-am-icon-preview svg { width: 20px; height: 20px; stroke: #d97706; }
            .dl-room-am-icon-preview img { width: 22px; height: 22px; object-fit: contain; }
            .dl-room-amenity-row:hover td { background-color: #fffbeb !important; }
        </style>

        <script type="text/javascript">
        jQuery(document).ready(function($) {
            var hasCheckboxCol = <?php echo ($post_id > 0) ? 'true' : 'false'; ?>;
            var iconChoicesHtml = <?php
                $opts_html = '';
                foreach ($icon_choices as $ic_key => $ic_label) {
                    $opts_html .= '<option value="' . esc_attr($ic_key) . '">' . esc_html($ic_label) . '</option>';
                }
                echo wp_json_encode($opts_html);
            ?>;
            var svgMap = <?php
                $svg_map = array();
                if (function_exists('dl_get_amenity_svg')) {
                    foreach (array_keys($icon_choices) as $ic_k) {
                        $svg_map[$ic_k] = dl_get_amenity_svg($ic_k, 'w-5 h-5 text-gold');
                    }
                }
                echo wp_json_encode($svg_map);
            ?>;

            function reindexRoomAmenityRows() {
                $('#dl-room-amenities-tbody tr.dl-room-amenity-row').each(function(idx) {
                    var $tr = $(this);
                    $tr.attr('data-index', idx);
                    $tr.find('.dl-room-am-order-num').text(idx + 1);
                    $tr.find('.dl-room-am-checkbox').attr('name', 'dl_amenity_items[' + idx + '][enabled]');
                    $tr.find('.dl-room-am-icon-select').attr('name', 'dl_amenity_items[' + idx + '][icon]');
                    $tr.find('.dl-room-am-key-input').attr('name', 'dl_amenity_items[' + idx + '][key]');
                    $tr.find('.dl-room-am-label-input').attr('name', 'dl_amenity_items[' + idx + '][label]');
                });
            }

            function renderIconPreview(iconVal) {
                var defaultSvg = '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3z"/></svg>';
                if (!iconVal) return defaultSvg;
                if (iconVal.indexOf('http://') === 0 || iconVal.indexOf('https://') === 0 || iconVal.indexOf('/') === 0) {
                    return '<img src="' + iconVal + '" alt="Icon">';
                }
                if (iconVal.indexOf('<svg') !== -1) {
                    return iconVal;
                }
                return svgMap[iconVal] || defaultSvg;
            }

            function addNewRoomAmenityRow() {
                var idx = $('#dl-room-amenities-tbody tr.dl-room-amenity-row').length;
                var uniqueKey = 'amenity-' + Date.now() + '-' + Math.floor(Math.random() * 100);
                var checkboxTd = hasCheckboxCol
                    ? '<td style="text-align:center; vertical-align:middle; padding:8px 6px;"><input type="checkbox" class="dl-room-am-checkbox" name="dl_amenity_items[' + idx + '][enabled]" value="1" checked style="width:18px; height:18px; margin:0;"></td>'
                    : '';

                var rowHtml = '<tr class="dl-room-amenity-row" data-index="' + idx + '">' +
                    checkboxTd +
                    '<td style="text-align:center; vertical-align:middle; padding:8px 6px;">' +
                        '<div style="display:flex; align-items:center; justify-content:center; gap:4px;">' +
                            '<span class="dl-room-am-order-num" style="display:inline-flex; align-items:center; justify-content:center; width:22px; height:22px; border-radius:50%; background:#07111e; color:#dfa843; font-size:11px; font-weight:700;">' + (idx + 1) + '</span>' +
                            '<div style="display:flex; flex-direction:column; gap:2px;">' +
                                '<button type="button" class="dl-room-am-up" title="Lên trên" style="border:1px solid #cbd5e1; background:#f8fafc; cursor:pointer; border-radius:3px; font-size:8px; line-height:1; padding:2px 4px;">▲</button>' +
                                '<button type="button" class="dl-room-am-down" title="Xuống dưới" style="border:1px solid #cbd5e1; background:#f8fafc; cursor:pointer; border-radius:3px; font-size:8px; line-height:1; padding:2px 4px;">▼</button>' +
                            '</div>' +
                        '</div>' +
                    '</td>' +
                    '<td style="vertical-align:middle; padding:8px 10px;">' +
                        '<div style="display:flex; align-items:center; gap:6px;">' +
                            '<div class="dl-room-am-icon-preview" style="width:36px; height:36px; border-radius:8px; background:#fffbeb; border:1px solid #fde68a; color:#d97706; display:flex; align-items:center; justify-content:center; flex-shrink:0; overflow:hidden;">' +
                                renderIconPreview('sparkles') +
                            '</div>' +
                            '<select class="dl-room-am-icon-select" name="dl_amenity_items[' + idx + '][icon]" style="flex:1; max-width:165px; font-size:12px; height:32px;">' +
                                iconChoicesHtml +
                            '</select>' +
                            '<button type="button" class="button button-small dl-room-am-upload-icon" title="Tải ảnh hoặc SVG từ Thư viện Media" style="height:32px; padding:0 8px; display:inline-flex; align-items:center; justify-content:center; color:#0f766e; border-color:#cbd5e1; background:#fff;"><span class="dashicons dashicons-upload" style="font-size:15px; width:15px; height:15px;"></span></button>' +
                        '</div>' +
                    '</td>' +
                    '<td style="vertical-align:middle; padding:8px 10px;">' +
                        '<input type="hidden" class="dl-room-am-key-input" name="dl_amenity_items[' + idx + '][key]" value="' + uniqueKey + '">' +
                        '<input type="text" class="dl-room-am-label-input" name="dl_amenity_items[' + idx + '][label]" value="" placeholder="Nhập tên tiện nghi phòng mới..." style="width:100%; font-weight:600; color:#0f172a; height:34px; border-radius:6px; border:1px solid #0f766e; padding:0 10px;">' +
                    '</td>' +
                    '<td style="text-align:center; vertical-align:middle; padding:8px 6px;">' +
                        '<button type="button" class="dl-room-am-delete" title="Xoá tiện nghi này" style="background:#fef2f2; border:1px solid #fecaca; color:#dc2626; border-radius:6px; padding:5px 8px; cursor:pointer; font-weight:700;">✕</button>' +
                    '</td>' +
                '</tr>';

                var $newRow = $(rowHtml);
                $newRow.find('.dl-room-am-icon-select').val('sparkles');
                $('#dl-room-amenities-tbody').append($newRow);
                $newRow.find('.dl-room-am-label-input').focus();
            }

            $('#dl-btn-add-room-amenity, #dl-btn-add-room-amenity-bottom').off('click.dlRoomAm').on('click.dlRoomAm', function(e) {
                e.preventDefault();
                addNewRoomAmenityRow();
            });

            $('#dl-room-am-check-all').off('click.dlRoomAm').on('click.dlRoomAm', function(e) {
                e.preventDefault();
                $('#dl-room-amenities-tbody .dl-room-am-checkbox').prop('checked', true);
            });

            $('#dl-room-am-uncheck-all').off('click.dlRoomAm').on('click.dlRoomAm', function(e) {
                e.preventDefault();
                $('#dl-room-amenities-tbody .dl-room-am-checkbox').prop('checked', false);
            });

            $(document).off('change.dlRoomAm', '.dl-room-am-icon-select').on('change.dlRoomAm', '.dl-room-am-icon-select', function() {
                var val = $(this).val();
                $(this).closest('td').find('.dl-room-am-icon-preview').html(renderIconPreview(val));
            });

            $(document).off('click.dlRoomAm', '.dl-room-am-delete').on('click.dlRoomAm', '.dl-room-am-delete', function(e) {
                e.preventDefault();
                if ($('#dl-room-amenities-tbody tr.dl-room-amenity-row').length <= 1) {
                    alert('Cần giữ lại ít nhất 1 tiện nghi trong danh mục.');
                    return;
                }
                if (confirm('Bạn có chắc muốn xoá tiện nghi này khỏi danh sách?')) {
                    $(this).closest('tr.dl-room-amenity-row').remove();
                    reindexRoomAmenityRows();
                }
            });

            $(document).off('click.dlRoomAm', '.dl-room-am-up').on('click.dlRoomAm', '.dl-room-am-up', function(e) {
                e.preventDefault();
                var $row = $(this).closest('tr.dl-room-amenity-row');
                var $prev = $row.prev('tr.dl-room-amenity-row');
                if ($prev.length) {
                    $row.insertBefore($prev);
                    reindexRoomAmenityRows();
                }
            });

            $(document).off('click.dlRoomAm', '.dl-room-am-down').on('click.dlRoomAm', '.dl-room-am-down', function(e) {
                e.preventDefault();
                var $row = $(this).closest('tr.dl-room-amenity-row');
                var $next = $row.next('tr.dl-room-amenity-row');
                if ($next.length) {
                    $row.insertAfter($next);
                    reindexRoomAmenityRows();
                }
            });

            $(document).off('click.dlRoomAm', '.dl-room-am-upload-icon').on('click.dlRoomAm', '.dl-room-am-upload-icon', function(e) {
                e.preventDefault();
                var $btn = $(this);
                var $select = $btn.siblings('.dl-room-am-icon-select');
                var $preview = $btn.siblings('.dl-room-am-icon-preview');

                if (typeof wp === 'undefined' || !wp.media) {
                    alert('Thư viện Media chưa sẵn sàng.');
                    return;
                }

                var frame = wp.media({
                    title: 'Chọn hoặc Tải lên Icon đại diện (Ảnh / SVG)',
                    button: { text: 'Sử dụng Icon này' },
                    multiple: false
                });

                frame.on('select', function() {
                    var attachment = frame.state().get('selection').first().toJSON();
                    if (attachment && attachment.url) {
                        var url = attachment.url;
                        var filename = url.split('/').pop();
                        if ($select.find('option[value="' + url + '"]').length === 0) {
                            $select.prepend('<option value="' + url + '">📁 Icon tải lên (' + filename + ')</option>');
                        }
                        $select.val(url);
                        $preview.html(renderIconPreview(url));
                    }
                });

                frame.open();
            });
        });
        </script>
        <?php
    }

    /**
     * Xử lý lưu danh mục Tiện Nghi Phòng Cao Cấp vào wp_options và lưu lựa chọn của phòng vào wp_postmeta
     */
    public static function process_and_save_amenities_catalog_from_post($post_id = 0) {
        if (isset($_POST['dragonlegend_room_amenities_title'])) {
            $sec_title = sanitize_text_field(wp_unslash($_POST['dragonlegend_room_amenities_title']));
            if (!empty($sec_title)) {
                update_option(self::ROOM_AMENITIES_TITLE_OPTION, $sec_title, true);
            }
        }

        if (isset($_POST['dl_amenity_items']) && is_array($_POST['dl_amenity_items'])) {
            $raw_items        = wp_unslash($_POST['dl_amenity_items']);
            $new_catalog      = array();
            $selected_for_room = array();

            foreach ($raw_items as $idx => $item) {
                $label = isset($item['label']) ? sanitize_text_field($item['label']) : '';
                if ($label === '') {
                    continue;
                }

                $raw_key = !empty($item['key']) ? sanitize_key($item['key']) : '';
                if (empty($raw_key) || strpos($raw_key, 'amenity-') === 0) {
                    $slug_base = sanitize_title($label);
                    if (empty($slug_base)) {
                        $slug_base = 'tien-nghi-' . ($idx + 1);
                    }
                    $raw_key = $slug_base;
                    // Đảm bảo không trùng key nếu thêm mới
                    if (isset($new_catalog[$raw_key])) {
                        $raw_key = $slug_base . '-' . ($idx + 1);
                    }
                }

                $icon = isset($item['icon']) ? trim($item['icon']) : 'sparkles';
                if (strpos($icon, '<svg') === false) {
                    $icon = sanitize_text_field($icon);
                }

                $new_catalog[$raw_key] = array(
                    'key'   => $raw_key,
                    'label' => $label,
                    'icon'  => $icon,
                );

                if ($post_id > 0 && !empty($item['enabled'])) {
                    $selected_for_room[] = $raw_key;
                }
            }

            if (!empty($new_catalog)) {
                update_option(self::ROOM_AMENITIES_OPTION, $new_catalog, true);
            }

            if ($post_id > 0) {
                update_post_meta($post_id, '_dl_room_amenities', $selected_for_room);
            }
        }
    }

    /**
     * Lưu dữ liệu Meta Box an toàn
     */
    public static function save_metaboxes($post_id, $post) {
        // Kiểm tra Nonce
        if (!isset($_POST['dl_meta_box_nonce']) || !wp_verify_nonce($_POST['dl_meta_box_nonce'], 'dl_save_meta_box')) {
            return;
        }

        // Kiểm tra Auto Save
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Kiểm tra quyền hạn
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // 1. Lưu trường của Hạng Phòng (dl_room)
        if ($post->post_type === 'dl_room') {
            $fields = self::get_room_fields($post_id);
            foreach ($fields as $key => $field) {
                if ($field['type'] === 'room_amenities_manager') {
                    if (isset($_POST['dl_room_amenities_catalog_submitted'])) {
                        self::process_and_save_amenities_catalog_from_post($post_id);
                    }
                    continue;
                }

                if (isset($_POST[$key])) {
                    if ($field['type'] === 'checkbox_list' && is_array($_POST[$key])) {
                        $sanitized = array_map('sanitize_text_field', $_POST[$key]);
                        update_post_meta($post_id, $key, $sanitized);
                    } elseif ($field['type'] === 'textarea') {
                        update_post_meta($post_id, $key, sanitize_textarea_field($_POST[$key]));
                    } else {
                        update_post_meta($post_id, $key, sanitize_text_field($_POST[$key]));
                    }
                } else {
                    if ($field['type'] === 'checkbox_list') {
                        delete_post_meta($post_id, $key);
                    }
                }
            }

            // Lưu gallery ảnh phòng
            if (isset($_POST['_dl_room_gallery']) && is_array($_POST['_dl_room_gallery'])) {
                $gallery = array_map('esc_url_raw', $_POST['_dl_room_gallery']);
                update_post_meta($post_id, '_dl_room_gallery', $gallery);
            } else {
                delete_post_meta($post_id, '_dl_room_gallery');
            }
        }

        // 2. Lưu trường của Gói Ưu Đãi (dl_offer)
        if ($post->post_type === 'dl_offer') {
            $keys = array('_dl_offer_tag', '_dl_offer_badge', '_dl_offer_price_text', '_dl_offer_expiry', '_dl_offer_benefits');
            foreach ($keys as $key) {
                if (isset($_POST[$key])) {
                    if ($key === '_dl_offer_benefits') {
                        update_post_meta($post_id, $key, sanitize_textarea_field($_POST[$key]));
                    } else {
                        update_post_meta($post_id, $key, sanitize_text_field($_POST[$key]));
                    }
                }
            }
        }

        // 3. Lưu trường của Dịch Vụ (dl_amenity & post)
        if ($post->post_type === 'dl_amenity' || $post->post_type === 'post') {
            $keys = array('_dl_amenity_location', '_dl_amenity_highlights', '_dl_amenity_icon');
            foreach ($keys as $key) {
                if (isset($_POST[$key])) {
                    if ($key === '_dl_amenity_highlights') {
                        update_post_meta($post_id, $key, sanitize_textarea_field($_POST[$key]));
                    } else {
                        update_post_meta($post_id, $key, sanitize_text_field($_POST[$key]));
                    }
                }
            }
        }

        // 4. Lưu trường của Đánh Giá (dl_testimonial)
        if ($post->post_type === 'dl_testimonial') {
            $keys = array('_dl_testimonial_location', '_dl_testimonial_rating', '_dl_testimonial_date', '_dl_testimonial_title');
            foreach ($keys as $key) {
                if (isset($_POST[$key])) {
                    update_post_meta($post_id, $key, sanitize_text_field($_POST[$key]));
                }
            }
        }

        // 5. Lưu trạng thái & chi tiết Đơn Đặt Phòng (dl_booking)
        if ($post->post_type === 'dl_booking') {
            $booking_fields = array(
                '_dl_booking_status'          => 'text',
                '_dl_booking_guest_name'      => 'text',
                '_dl_booking_guest_phone'     => 'text',
                '_dl_booking_guest_email'     => 'email',
                '_dl_booking_room_name'       => 'text',
                '_dl_booking_checkin'         => 'text',
                '_dl_booking_checkout'        => 'text',
                '_dl_booking_rooms'           => 'int',
                '_dl_booking_adults'          => 'int',
                '_dl_booking_children'        => 'int',
                '_dl_booking_promo_code'      => 'text',
                '_dl_booking_discount_type'   => 'text',
                '_dl_booking_discount_amount' => 'float',
                '_dl_booking_grand_total'     => 'float',
                '_dl_booking_guest_note'      => 'textarea',
            );

            foreach ($booking_fields as $b_key => $b_type) {
                if (isset($_POST[$b_key])) {
                    if ($b_type === 'int') {
                        update_post_meta($post_id, $b_key, intval($_POST[$b_key]));
                    } elseif ($b_type === 'float') {
                        update_post_meta($post_id, $b_key, floatval($_POST[$b_key]));
                    } elseif ($b_type === 'email') {
                        update_post_meta($post_id, $b_key, sanitize_email($_POST[$b_key]));
                    } elseif ($b_type === 'textarea') {
                        update_post_meta($post_id, $b_key, sanitize_textarea_field($_POST[$b_key]));
                    } else {
                        update_post_meta($post_id, $b_key, sanitize_text_field($_POST[$b_key]));
                    }
                }
            }

            // Tính toán lại số đêm nếu có checkin và checkout
            if (!empty($_POST['_dl_booking_checkin']) && !empty($_POST['_dl_booking_checkout'])) {
                $d1 = strtotime($_POST['_dl_booking_checkin']);
                $d2 = strtotime($_POST['_dl_booking_checkout']);
                if ($d1 && $d2 && $d2 > $d1) {
                    $nights = round(($d2 - $d1) / 86400);
                    update_post_meta($post_id, '_dl_booking_nights', $nights);
                }
            }

            // Tự động tạo mã đơn nếu chưa có
            $code = get_post_meta($post_id, '_dl_booking_code', true);
            if (empty($code) || empty($post->post_title) || $post->post_title === 'Auto Draft' || $post->post_title === __('Auto Draft')) {
                $new_code = 'DL-' . strtoupper(wp_generate_password(6, false, false));
                update_post_meta($post_id, '_dl_booking_code', $new_code);
                
                // Cập nhật title của bài viết thành mã đơn
                remove_action('save_post', array(__CLASS__, 'save_metaboxes'));
                wp_update_post(array(
                    'ID'         => $post_id,
                    'post_title' => $new_code,
                    'post_name'  => sanitize_title($new_code),
                ));
                add_action('save_post', array(__CLASS__, 'save_metaboxes'), 10, 2);
            }
        }

        // Hook cho phép bên ngoài lưu thêm trường tùy chỉnh
        do_action('dragonlegend_save_meta_box', $post_id, $post);
    }
}
