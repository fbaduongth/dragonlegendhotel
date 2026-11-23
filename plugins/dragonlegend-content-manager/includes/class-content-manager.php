<?php
/**
 * Dragon Legend Content Manager - Main Handler Class
 *
 * @package DragonLegendContentManager
 */

if (!defined('ABSPATH')) {
    exit;
}

class DragonLegend_Content_Manager {

    const OPTION_KEY = 'dragonlegend_static_content_data';

    /**
     * Khởi tạo hooks
     */
    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'register_admin_menu'));
        add_action('admin_init', array(__CLASS__, 'register_settings'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_assets'));
        add_action('wp_head', array(__CLASS__, 'output_tracking_scripts'), 1);
        add_action('wp_body_open', array(__CLASS__, 'output_body_scripts'), 1);
    }

    /**
     * Danh sách giá trị mặc định cho toàn bộ nội dung tĩnh
     */
    public static function get_defaults() {
        return array(
            // --- TAB 1: HEADER & TOPBAR ---
            'topbar_badge'              => 'Đặt trực tiếp - Luôn cam kết giá tốt nhất',
            'topbar_promo'              => 'Nhập mã DRAGON2026 giảm ngay 10%',
            'hotel_name'                => 'Dragon Legend Hotel Hạ Long',
            'hotel_stars'               => '4',
            'hotel_hotline'             => '(+84) 203 3518 999',
            'hotel_hotline_tel'         => '+842033518999',
            'hotel_email'               => 'info@dragonlegendhotel.com',
            'hotel_address'             => 'Đại lộ Hạ Long Marine, Phường Bãi Cháy, TP. Hạ Long, Tỉnh Quảng Ninh',
            'header_vr_url'             => 'https://dragonlegendhotel.com/vr/360.html',
            'header_vr_text'            => 'Tour VR 360°',
            'header_logo_url'           => 'https://dragonlegendhotel.com/wp-content/uploads/2015/07/logoweb-1.png',
            'header_brand_title'        => 'DRAGON LEGEND',
            'header_brand_subtitle'     => 'HẠ LONG HOTEL ★★★★',
            'header_book_btn_text'      => 'Đặt phòng ngay',
            'header_book_btn_url'       => '#booking',

            // --- TAB 2: HERO BANNER ---
            'hero_badge_text'           => 'Khách Sạn Tân Cổ Điển 4 Sao Bãi Cháy',
            'hero_h1_line1'             => 'Dragon Legend Hotel',
            'hero_h1_line2'             => 'Khách Sạn 4 Sao Bãi Cháy Hạ Long',
            'hero_description'          => 'Khám phá không gian nghỉ dưỡng 138 phòng tiện nghi tiêu chuẩn quốc tế, hồ bơi vô cực ngắm trọn kỳ quan cùng nét tinh hoa ẩm thực Á - Âu độc đáo trên đại lộ Hạ Long Marine.',
            'hero_bg_image'             => 'https://dragonlegendhotel.com/wp-content/uploads/2015/07/DJI_0081-HDR.jpg',
            'hero_btn1_text'            => 'Khám phá phòng nghỉ',
            'hero_btn1_url'             => '#rooms',
            'hero_btn2_text'            => 'Khám phá Tour VR 360°',
            'hero_btn2_url'             => 'https://dragonlegendhotel.com/vr/360.html',

            // --- TAB 3: STATS COUNTER ---
            'stats_num_1'               => '138',
            'stats_unit_1'              => '+',
            'stats_label_1'             => 'Phòng Nghỉ Hoàng Gia',
            'stats_num_2'               => '4',
            'stats_unit_2'              => '★',
            'stats_label_2'             => 'Tiêu Chuẩn Quốc Tế',
            'stats_num_3'               => '12',
            'stats_unit_3'              => ' Tầng',
            'stats_label_3'             => 'Hồ Bơi Vô Cực Trên Cao',
            'stats_num_4'               => '100',
            'stats_unit_4'              => '%',
            'stats_label_4'             => 'View Đại Lộ & Vịnh Biển',

            // --- TAB 4: ABOUT HOTEL ---
            'about_tagline'             => 'Về Chúng Tôi',
            'about_title'               => 'Viên Ngọc Rực Rỡ Giữa Lòng Kỳ Quan Di Sản',
            'about_p1'                  => 'Tọa lạc tại vị trí đắc địa trên đại lộ Hạ Long Marine sầm uất thuộc trung tâm Bãi Cháy, Dragon Legend Hotel là điểm dừng chân hoàn hảo kết hợp nét trang nhã cổ điển châu Âu cùng sự năng động hiện đại của thành phố biển.',
            'about_p2'                  => 'Sở hữu 138 phòng nghỉ tiêu chuẩn quốc tế, hồ bơi vô cực ngắm trọn kỳ quan thiên nhiên thế giới, trung tâm ẩm thực phong phú cùng đội ngũ nhân viên nhiệt thành, chúng tôi cam kết đem lại cho bạn kỳ nghỉ trọn vẹn và đáng nhớ nhất.',
            'about_badge1_title'        => 'Vị Trí Đắc Địa Bãi Cháy',
            'about_badge2_title'        => 'Ẩm Thực Á - Âu Đặc Sắc',
            'about_badge3_title'        => 'Sky Pool Tầng 12',
            'about_badge4_title'        => 'An Ninh & Lễ Tân 24/7',
            'about_image_main'          => 'https://dragonlegendhotel.com/wp-content/uploads/2015/07/DJI_0081-HDR.jpg',
            'about_image_sub'           => 'https://dragonlegendhotel.com/wp-content/uploads/2026/02/DBL-1.jpg',
            'about_btn_contact_text'    => 'Liên hệ đặt phòng',
            'about_btn_vr_text'         => 'Xem toàn cảnh 360° →',

            // --- TAB AMENITIES: POPULAR AMENITIES (12 ITEMS) ---
            'sec_amenities_popular_tag' => 'Trải Nghiệm Khách Sạn',
            'sec_amenities_popular_ttl' => 'Tiện Nghi Nổi Bật Được Yêu Thích',
            'sec_amenities_popular_dsc' => 'Tận hưởng hệ sinh thái tiện ích đồng bộ, hoàn hảo từ những chi tiết nhỏ nhất nhằm mang đến sự thoải mái tối đa cho quý khách.',

            'pop_amenity_1_name'        => 'Xe đưa đón sân bay',
            'pop_amenity_1_icon'        => 'shuttle',
            'pop_amenity_1_desc'        => 'Hỗ trợ đưa đón sân bay Cát Bi & Vân Đồn tiện lợi',

            'pop_amenity_2_name'        => 'Phòng không hút thuốc',
            'pop_amenity_2_icon'        => 'no-smoking',
            'pop_amenity_2_desc'        => 'Không khí trong lành, khử khuẩn định kỳ',

            'pop_amenity_3_name'        => 'WiFi miễn phí tốc độ cao',
            'pop_amenity_3_icon'        => 'wifi',
            'pop_amenity_3_desc'        => 'Độ phủ sóng 100% trong phòng và toàn khách sạn',

            'pop_amenity_4_name'        => 'Dịch vụ phòng chu đáo',
            'pop_amenity_4_icon'        => 'concierge',
            'pop_amenity_4_desc'        => 'Phục vụ ẩm thực và dọn dẹp theo yêu cầu 24/7',

            'pop_amenity_5_name'        => 'Phòng gia đình tiện nghi',
            'pop_amenity_5_icon'        => 'family',
            'pop_amenity_5_desc'        => 'Không gian rộng rãi, giường phụ cho trẻ em',

            'pop_amenity_6_name'        => 'Nhà hàng ẩm thực Á - Âu',
            'pop_amenity_6_icon'        => 'restaurant',
            'pop_amenity_6_desc'        => 'Hải sản tươi sống và tiệc buffet phong phú',

            'pop_amenity_7_name'        => 'Lễ tân phục vụ 24 giờ',
            'pop_amenity_7_icon'        => 'reception',
            'pop_amenity_7_desc'        => 'Nhận/trả phòng linh hoạt, hỗ trợ tour vịnh',

            'pop_amenity_8_name'        => 'Thang máy hiện đại',
            'pop_amenity_8_icon'        => 'elevator',
            'pop_amenity_8_desc'        => 'Hệ thống thang máy thẻ từ an ninh cao',

            'pop_amenity_9_name'        => 'Điều hòa nhiệt độ 2 chiều',
            'pop_amenity_9_icon'        => 'snowflake',
            'pop_amenity_9_desc'        => 'Vận hành êm dịu, tùy chỉnh theo sở thích',

            'pop_amenity_10_name'       => 'Bữa sáng buffet thơm ngon',
            'pop_amenity_10_icon'       => 'coffee',
            'pop_amenity_10_desc'       => 'Hơn 50 món phong phú từ truyền thống đến quốc tế',

            'pop_amenity_11_name'       => 'Hồ bơi vô cực tầng 12',
            'pop_amenity_11_icon'       => 'pool',
            'pop_amenity_11_desc'       => 'Tầm nhìn bao trọn toàn cảnh Vịnh Hạ Long',

            'pop_amenity_12_name'       => 'Bãi đỗ xe miễn phí',
            'pop_amenity_12_icon'       => 'parking',
            'pop_amenity_12_desc'       => 'Khuôn viên đậu xe ô tô rộng rãi, an ninh 24/7',

            // --- TAB 5: SECTION HEADINGS ---
            'sec_rooms_tag'             => 'Không Gian Nghỉ Dưỡng',
            'sec_rooms_ttl'             => 'Kiệt Tác Phòng Nghỉ Hoàng Gia',
            'sec_rooms_dsc'             => 'Mỗi căn phòng tại Dragon Legend Hotel là sự giao hòa tuyệt mỹ giữa nét quý phái tân cổ điển và trang thiết bị tân tiến, đem lại giấc ngủ êm dịu nhất.',
            'sec_offers_tag'            => 'Đặc Quyền Nghỉ Dưỡng',
            'sec_offers_ttl'            => 'Ưu Đãi & Combo Trải Nghiệm Độc Quyền',
            'sec_offers_dsc'            => 'Tận hưởng trọn vẹn kỳ nghỉ đẳng cấp với các gói ưu đãi giá phòng, ẩm thực và dịch vụ được thiết kế riêng cho bạn.',
            'sec_amenities_tag'         => 'Trải nghiệm trọn vẹn',
            'sec_amenities_ttl'         => 'Dịch Vụ & Tiện Ích Đỉnh Cao',
            'sec_amenities_dsc'         => 'Tận hưởng những giây phút thư giãn bất tận với hệ thống tiện ích đẳng cấp từ tầng 12 đến các sảnh hội nghị sang trọng.',
            'sec_reviews_tag'           => 'Khách hàng nói gì về chúng tôi',
            'sec_reviews_ttl'           => 'Trải Nghiệm Thực Tế Từ Du Khách',
            'sec_contact_tag'           => 'Điểm Đến Lý Tưởng',
            'sec_contact_ttl'           => 'Vị Trí Trung Tâm & Kết Nối Thuận Tiện',
            'sec_contact_dsc'           => 'Nằm trên đại lộ sầm uất nhất khu du lịch Bãi Cháy, Dragon Legend Hotel giúp bạn dễ dàng di chuyển tới các điểm vui chơi, bãi tắm và cảng du thuyền quốc tế trong bán kính chưa đầy 5-10 phút.',

            // --- TAB 6: LOCATION & ATTRACTIONS ---
            'map_embed_url'             => 'https://maps.google.com/maps?q=Dragon%20Legend%20H%E1%BA%A1%20Long%20Hotel,%20%C4%90%E1%BA%A1i%20l%E1%BB%99%20H%E1%BA%A1%20Long%20Marine,%20B%C3%A3i%20Ch%C3%A1y,%20H%E1%BA%A1%20Long&ll=20.9582273,107.0201151&t=m&z=16&hl=vi&ie=UTF8&iwloc=&output=embed',
            'attraction_1_name'         => 'Sun World Hạ Long Complex',
            'attraction_1_dist'         => '1.8 km (4 phút di chuyển)',
            'attraction_1_desc'         => 'Tổ hợp công viên giải trí hàng đầu Đông Nam Á với Cáp treo Nữ Hoàng và Vòng quay Mặt Trời.',
            'attraction_2_name'         => 'Bãi tắm Bãi Cháy & Phố đi bộ',
            'attraction_2_dist'         => '1.2 km (3 phút di chuyển)',
            'attraction_2_desc'         => 'Bờ cát trắng mịn trải dài 3km, làn nước xanh mát cùng các quán cafe biển sôi động về đêm.',
            'attraction_3_name'         => 'Chợ đêm Hạ Long & Halong Marine Plaza',
            'attraction_3_dist'         => '300 m (Đi bộ 3 phút)',
            'attraction_3_desc'         => 'Thế giới quà lưu niệm, hải sản sấy khô và khu ẩm thực đường phố sầm uất ngay cạnh khách sạn.',
            'attraction_4_name'         => 'Cảng tàu khách quốc tế Hạ Long',
            'attraction_4_dist'         => '3.5 km (7 phút di chuyển)',
            'attraction_4_desc'         => 'Bến xuất phát của các du thuyền thăm quan kỳ quan Vịnh Hạ Long và Đảo Ti Tốp.',

            // --- TAB 7: FOOTER & SOCIAL ---
            'footer_logo_url'           => 'https://dragonlegendhotel.com/wp-content/uploads/2015/07/logoweb-1.png',
            'footer_brand_title'        => 'Dragon Legend Hotel Hạ Long',
            'footer_brand_subtitle'     => 'HẠ LONG ★★★★',
            'footer_desc'               => 'Được ví như một "Viên ngọc rực rỡ" giữa lòng kỳ quan thiên nhiên thế giới Vịnh Hạ Long. Tận hưởng chuẩn mực nghỉ dưỡng tân cổ điển đỉnh cao với 138 phòng tiện nghi, hồ bơi vô cực ngắm trọn kỳ quan cùng nét tinh hoa ẩm thực Á - Âu độc đáo.',
            'social_facebook'           => 'https://www.facebook.com/dosmdragonlegend',
            'social_tiktok'             => 'https://www.tiktok.com/@dragonlegendhalong',
            'social_youtube'            => 'https://www.youtube.com/@dragonlegendhotel',
            'social_messenger'          => 'https://m.me/106125120739757',
            'social_zalo'               => 'https://zalo.me/02033518999',
            'footer_newsletter_title'   => 'Đăng ký nhận ưu đãi',
            'footer_newsletter_desc'    => 'Nhận ngay mã giảm 10% cho lần đặt phòng kế tiếp cùng các gói combo độc quyền.',
            'footer_newsletter_btn'     => 'Đăng ký ngay',
            'footer_copyright'          => '© 2026 Dragon Legend Hotel Ha Long. All rights reserved.',
            'footer_subtext_1'          => 'Tiêu chuẩn 4 Sao Quốc Tế',
            'footer_subtext_2'          => 'Bãi Cháy, Quảng Ninh',

            // --- TAB 8: GOOGLE TAG & GA4 ANALYTICS ---
            'ga4_measurement_id'        => 'G-DRAGON2026',
            'google_tag_manager_id'     => '',
            'custom_header_scripts'     => '',
            'custom_body_scripts'       => '',
        );
    }

    /**
     * Lấy giá trị cấu hình theo key
     */
    public static function get($key, $fallback = '') {
        $data = get_option(self::OPTION_KEY, array());
        $defaults = self::get_defaults();

        if (isset($data[$key]) && $data[$key] !== '') {
            if ($key === 'map_embed_url' && (strpos($data[$key], '!1svi!2s!') !== false || strpos($data[$key], '1769987129773') !== false)) {
                return $defaults['map_embed_url'];
            }
            return $data[$key];
        }

        if (isset($defaults[$key])) {
            return $defaults[$key];
        }

        return $fallback;
    }

    /**
     * Danh sách 15 tiện nghi nổi bật mặc định
     */
    public static function get_default_popular_amenities() {
        return array(
            array('name' => 'Xe đưa đón sân bay', 'icon' => 'shuttle', 'desc' => 'Hỗ trợ đưa đón sân bay Cát Bi & Vân Đồn tiện lợi'),
            array('name' => 'Phòng không hút thuốc', 'icon' => 'no-smoking', 'desc' => 'Không khí trong lành, khử khuẩn định kỳ'),
            array('name' => 'WiFi miễn phí tốc độ cao', 'icon' => 'wifi', 'desc' => 'Độ phủ sóng 100% trong phòng và toàn khách sạn'),
            array('name' => 'Dịch vụ phòng chu đáo', 'icon' => 'concierge', 'desc' => 'Phục vụ ẩm thực và dọn dẹp theo yêu cầu 24/7'),
            array('name' => 'Phòng gia đình tiện nghi', 'icon' => 'family', 'desc' => 'Không gian rộng rãi, giường phụ cho trẻ em'),
            array('name' => 'Nhà hàng ẩm thực Á - Âu', 'icon' => 'restaurant', 'desc' => 'Hải sản tươi sống và tiệc buffet phong phú'),
            array('name' => 'Lễ tân phục vụ 24 giờ', 'icon' => 'reception', 'desc' => 'Nhận/trả phòng linh hoạt, hỗ trợ tour vịnh'),
            array('name' => 'Thang máy hiện đại', 'icon' => 'elevator', 'desc' => 'Hệ thống thang máy thẻ từ an ninh cao'),
            array('name' => 'Điều hòa nhiệt độ 2 chiều', 'icon' => 'snowflake', 'desc' => 'Vận hành êm dịu, tùy chỉnh theo sở thích'),
            array('name' => 'Bữa sáng buffet thơm ngon', 'icon' => 'coffee', 'desc' => 'Hơn 50 món phong phú từ truyền thống đến quốc tế'),
            array('name' => 'Bể bơi trong nhà & bar', 'icon' => 'pool', 'desc' => 'Thư giãn'),
            array('name' => 'Bãi đỗ xe miễn phí', 'icon' => 'parking', 'desc' => 'Khuôn viên đậu xe ô tô rộng rãi, an ninh 24/7'),
            array('name' => 'Gym & Yoga', 'icon' => 'gym', 'desc' => 'Trang thiết bị hiện đại & không gian yoga thư thái'),
            array('name' => 'Spa', 'icon' => 'spa', 'desc' => 'Thư giãn tinh thần và phục hồi năng lượng chuyên sâu'),
            array('name' => 'Sảnh tiệc & Hội nghị', 'icon' => 'conference', 'desc' => 'Không gian sang trọng, sức chứa lớn & âm thanh chuẩn quốc tế'),
        );
    }

    /**
     * Lấy danh sách tiện nghi nổi bật đã cấu hình (hỗ trợ Thêm, Sửa, Xoá động)
     */
    public static function get_popular_amenities() {
        $saved = get_option(self::OPTION_KEY, array());

        // 1. Nếu đã lưu cấu hình dynamic popular_amenities
        if (isset($saved['popular_amenities']) && is_array($saved['popular_amenities'])) {
            $items = array();
            foreach ($saved['popular_amenities'] as $idx => $am) {
                if (!is_array($am)) continue;
                $name = isset($am['name']) ? trim($am['name']) : '';
                if ($name !== '') {
                    $items[] = array(
                        'id'   => 'pop-amenity-' . ($idx + 1),
                        'name' => $name,
                        'icon' => !empty($am['icon']) ? sanitize_text_field($am['icon']) : 'sparkles',
                        'desc' => isset($am['desc']) ? sanitize_text_field($am['desc']) : '',
                    );
                }
            }
            if (!empty($items)) {
                return $items;
            }
        }

        // 2. Nếu có cấu hình cũ dạng pop_amenity_1..12
        $items = array();
        $defaults = self::get_defaults();
        for ($i = 1; $i <= 12; $i++) {
            $name_key = "pop_amenity_{$i}_name";
            $icon_key = "pop_amenity_{$i}_icon";
            $desc_key = "pop_amenity_{$i}_desc";

            $name = isset($saved[$name_key]) && $saved[$name_key] !== '' ? $saved[$name_key] : (isset($defaults[$name_key]) ? $defaults[$name_key] : '');
            $icon = isset($saved[$icon_key]) && $saved[$icon_key] !== '' ? $saved[$icon_key] : (isset($defaults[$icon_key]) ? $defaults[$icon_key] : 'shuttle');
            $desc = isset($saved[$desc_key]) && $saved[$desc_key] !== '' ? $saved[$desc_key] : (isset($defaults[$desc_key]) ? $defaults[$desc_key] : '');

            if (!empty($name)) {
                $items[] = array(
                    'id'   => 'pop-amenity-' . $i,
                    'name' => $name,
                    'icon' => $icon,
                    'desc' => $desc,
                );
            }
        }

        // Tự động bổ sung 3 hạng mục mới nếu chưa có: Gym & Yoga, Spa, Sảnh tiệc & Hội nghị
        $has_gym = false;
        $has_spa = false;
        $has_conference = false;
        foreach ($items as $it) {
            $n_lower = mb_strtolower($it['name'], 'UTF-8');
            if (strpos($n_lower, 'gym') !== false) $has_gym = true;
            if (strpos($n_lower, 'spa') !== false) $has_spa = true;
            if (strpos($n_lower, 'sảnh tiệc') !== false || strpos($n_lower, 'hội nghị') !== false) $has_conference = true;
        }

        if (!$has_gym) {
            $items[] = array(
                'id'   => 'pop-amenity-' . (count($items) + 1),
                'name' => 'Gym & Yoga',
                'icon' => 'gym',
                'desc' => 'Trang thiết bị hiện đại & không gian yoga thư thái',
            );
        }
        if (!$has_spa) {
            $items[] = array(
                'id'   => 'pop-amenity-' . (count($items) + 1),
                'name' => 'Spa',
                'icon' => 'spa',
                'desc' => 'Thư giãn tinh thần và phục hồi năng lượng chuyên sâu',
            );
        }
        if (!$has_conference) {
            $items[] = array(
                'id'   => 'pop-amenity-' . (count($items) + 1),
                'name' => 'Sảnh tiệc & Hội nghị',
                'icon' => 'conference',
                'desc' => 'Không gian sang trọng, sức chứa lớn & âm thanh chuẩn quốc tế',
            );
        }

        return $items;
    }

    /**
     * Đăng ký trang cài đặt trong WP-Admin
     */
    public static function register_admin_menu() {
        add_menu_page(
            __('Quản Lý Nội Dung Tĩnh', 'dragonlegend-content-manager'),
            __('Nội Dung Tĩnh', 'dragonlegend-content-manager'),
            'manage_options',
            'dragonlegend-content-manager',
            array(__CLASS__, 'render_admin_page'),
            'dashicons-admin-site-alt3',
            28
        );
    }

    /**
     * Đăng ký settings với WordPress
     */
    public static function register_settings() {
        register_setting(
            'dragonlegend_static_content_group',
            self::OPTION_KEY,
            array(__CLASS__, 'sanitize_options')
        );
    }

    /**
     * Sanitize dữ liệu trước khi lưu
     */
    public static function sanitize_options($input) {
        if (!is_array($input)) {
            return array();
        }

        $existing = get_option(self::OPTION_KEY, array());
        $sanitized = is_array($existing) ? $existing : array();
        $defaults = self::get_defaults();

        foreach ($defaults as $key => $default_val) {
            if (!isset($input[$key])) {
                continue;
            }

            $val = $input[$key];

            // Cho phép thẻ HTML cơ bản cho các đoạn văn bản
            if (in_array($key, array('about_p1', 'about_p2', 'footer_desc', 'hero_description', 'topbar_promo'))) {
                $sanitized[$key] = wp_kses_post($val);
            } elseif (in_array($key, array('custom_header_scripts', 'custom_body_scripts'))) {
                $sanitized[$key] = current_user_can('unfiltered_html') ? $val : wp_kses_post($val);
            } elseif (strpos($key, '_url') !== false || in_array($key, array('social_facebook', 'social_tiktok', 'social_youtube', 'social_messenger', 'social_zalo', 'map_embed_url'))) {
                $sanitized[$key] = esc_url_raw($val);
            } else {
                $sanitized[$key] = sanitize_text_field($val);
            }
        }

        // Xử lý danh sách động Tiện Nghi Nổi Bật (Thêm, Sửa, Xoá, Sắp xếp thứ tự)
        if (isset($input['popular_amenities']) && is_array($input['popular_amenities'])) {
            $sanitized_amenities = array();
            $idx = 1;
            foreach ($input['popular_amenities'] as $am) {
                if (!is_array($am)) continue;
                $name = isset($am['name']) ? sanitize_text_field(trim($am['name'])) : '';
                if ($name === '') continue; // Bỏ qua nếu tên trống
                $icon = isset($am['icon']) ? sanitize_text_field(trim($am['icon'])) : 'sparkles';
                $desc = isset($am['desc']) ? sanitize_text_field(trim($am['desc'])) : '';

                $sanitized_amenities[] = array(
                    'name' => $name,
                    'icon' => $icon,
                    'desc' => $desc,
                );

                // Đồng bộ ngược vào 12 keys truyền thống nếu có để tương thích tối đa
                if ($idx <= 12) {
                    $sanitized["pop_amenity_{$idx}_name"] = $name;
                    $sanitized["pop_amenity_{$idx}_icon"] = $icon;
                    $sanitized["pop_amenity_{$idx}_desc"] = $desc;
                }
                $idx++;
            }
            $sanitized['popular_amenities'] = $sanitized_amenities;
        }

        return $sanitized;
    }

    /**
     * Xuất mã Google Tag (gtag.js) và mã nhúng trong <head>
     */
    public static function output_tracking_scripts() {
        $data = get_option(self::OPTION_KEY, array());
        $defaults = self::get_defaults();

        $ga4_id = !empty($data['ga4_measurement_id']) ? trim($data['ga4_measurement_id']) : (!empty($defaults['ga4_measurement_id']) ? $defaults['ga4_measurement_id'] : '');
        $gtm_id = !empty($data['google_tag_manager_id']) ? trim($data['google_tag_manager_id']) : '';
        $custom_header = !empty($data['custom_header_scripts']) ? $data['custom_header_scripts'] : '';

        // 1. Google Tag (gtag.js) cho GA4
        if (!empty($ga4_id)) {
            $is_local = in_array($_SERVER['REMOTE_ADDR'] ?? '', array('127.0.0.1', '::1', 'localhost')) || (defined('WP_DEBUG') && WP_DEBUG);
            ?>
<!-- Google tag (gtag.js) - Dragon Legend Hotel -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr($ga4_id); ?>"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', '<?php echo esc_js($ga4_id); ?>', {
    'send_page_view': true,
    'debug_mode': <?php echo $is_local ? 'true' : 'false'; ?>

  });
</script>
            <?php
        }

        // 2. Google Tag Manager (<head>)
        if (!empty($gtm_id)) {
            ?>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','<?php echo esc_js($gtm_id); ?>');</script>
<!-- End Google Tag Manager -->
            <?php
        }

        // 3. Custom Header Scripts
        if (!empty($custom_header)) {
            echo $custom_header . "\n";
        }
    }

    /**
     * Xuất mã nhúng ngay sau thẻ <body>
     */
    public static function output_body_scripts() {
        $data = get_option(self::OPTION_KEY, array());
        $gtm_id = !empty($data['google_tag_manager_id']) ? trim($data['google_tag_manager_id']) : '';
        $custom_body = !empty($data['custom_body_scripts']) ? $data['custom_body_scripts'] : '';

        if (!empty($gtm_id)) {
            ?>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?php echo esc_attr($gtm_id); ?>"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
            <?php
        }

        if (!empty($custom_body)) {
            echo $custom_body . "\n";
        }
    }

    /**
     * Nạp scripts và styles cho trang quản trị
     */
    public static function enqueue_admin_assets($hook) {
        if (strpos($hook, 'dragonlegend-content-manager') === false) {
            return;
        }

        $asset_version = time();

        wp_enqueue_media();
        wp_enqueue_style(
            'dl-content-manager-admin-css',
            DL_CONTENT_MANAGER_URL . 'admin/css/admin-style.css',
            array(),
            $asset_version
        );

        wp_enqueue_script(
            'dl-content-manager-admin-js',
            DL_CONTENT_MANAGER_URL . 'admin/js/admin-media.js',
            array('jquery'),
            $asset_version,
            true
        );
    }

    /**
     * Render trang giao diện quản trị
     */
    public static function render_admin_page() {
        require_once DL_CONTENT_MANAGER_DIR . 'admin/settings-page.php';
    }
}
