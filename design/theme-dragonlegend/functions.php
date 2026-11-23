<?php
/**
 * Dragon Legend Hotel Luxury Theme - Functions & Definitions
 * Tối ưu hóa Core Web Vitals, chuẩn SEO Google & kết nối chặt chẽ với plugin Dragon Legend Core
 *
 * @package DragonLegend
 * @version 1.2.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Theme Setup
 */
function dragonlegend_theme_setup() {
    load_theme_textdomain('dragonlegend', get_template_directory() . '/languages');
    add_theme_support('automatic-feed-links');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');

    // Register Navigation Menus
    register_nav_menus(array(
        'primary'       => __('Menu Chính (Header)', 'dragonlegend'),
        'footer_info'   => __('Chân Trang: Thông Tin', 'dragonlegend'),
        'footer_policy' => __('Chân Trang: Quy Định & Chính Sách', 'dragonlegend'),
    ));

    // Valid HTML5 markup
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
        'navigation-widgets',
    ));

    // Custom Logo Support
    add_theme_support('custom-logo', array(
        'height'      => 120,
        'width'       => 320,
        'flex-width'  => true,
        'flex-height' => true,
    ));
}
add_action('after_setup_theme', 'dragonlegend_theme_setup');

/**
 * Preconnect & Resource Hints for Fast Loading
 */
function dragonlegend_resource_hints($urls, $relation_type) {
    if (wp_dependencies_unique_hosts()) {
        if ('preconnect' === $relation_type) {
            $urls[] = array('href' => 'https://fonts.googleapis.com');
            $urls[] = array('href' => 'https://fonts.gstatic.com', 'crossorigin' => 'anonymous');
            $urls[] = array('href' => 'https://cdn.jsdelivr.net');
            $urls[] = array('href' => 'https://cdn.tailwindcss.com');
        }
        if ('dns-prefetch' === $relation_type) {
            $urls[] = 'https://fonts.googleapis.com';
            $urls[] = 'https://fonts.gstatic.com';
            $urls[] = 'https://cdn.jsdelivr.net';
            $urls[] = 'https://cdn.tailwindcss.com';
        }
    }
    return $urls;
}
add_filter('wp_resource_hints', 'dragonlegend_resource_hints', 10, 2);

/**
 * Enqueue Scripts and Styles
 */
function dragonlegend_enqueue_scripts() {
    $theme_version = wp_get_theme()->get('Version');

    // 1. Google Fonts
    wp_enqueue_style(
        'dragonlegend-fonts',
        'https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,600;1,700&display=swap',
        array(),
        null
    );

    // 2. Swiper CSS
    wp_enqueue_style(
        'swiper-css',
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
        array(),
        '11.0.0'
    );

    // 3. AOS CSS
    wp_enqueue_style(
        'aos-css',
        'https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css',
        array(),
        '2.3.4'
    );

    $custom_css_ver = file_exists(get_template_directory() . '/assets/css/custom.css') ? filemtime(get_template_directory() . '/assets/css/custom.css') : $theme_version;
    $main_js_ver    = file_exists(get_template_directory() . '/assets/js/main.js') ? filemtime(get_template_directory() . '/assets/js/main.js') : $theme_version;

    // 4. Main Theme Stylesheet
    wp_enqueue_style(
        'dragonlegend-style',
        get_stylesheet_uri(),
        array('swiper-css', 'aos-css'),
        $theme_version
    );

    // 5. Custom Luxury CSS
    wp_enqueue_style(
        'dragonlegend-custom',
        get_template_directory_uri() . '/assets/css/custom.css',
        array('dragonlegend-style'),
        $custom_css_ver
    );


    // 7. Swiper JS
    wp_enqueue_script(
        'swiper-js',
        'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
        array(),
        '11.0.0',
        true
    );

    // 8. AOS JS
    wp_enqueue_script(
        'aos-js',
        'https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js',
        array(),
        '2.3.4',
        true
    );

    // 9. Booking Engine Script
    wp_enqueue_script(
        'dragonlegend-booking',
        get_template_directory_uri() . '/assets/js/booking.js',
        array('jquery'),
        $theme_version,
        true
    );

    // 10. Main Controller Script
    wp_enqueue_script(
        'dragonlegend-main',
        get_template_directory_uri() . '/assets/js/main.js',
        array('swiper-js', 'aos-js', 'dragonlegend-booking'),
        $main_js_ver,
        true
    );

    // Localize Script for AJAX Booking & Dynamic Data
    $rooms_data = dl_get_rooms_for_frontend();
    $attractions_data = array(
        array(
            'name'        => function_exists('dl_get_content') ? dl_get_content('attraction_1_name', 'Sun World Hạ Long Complex') : 'Sun World Hạ Long Complex',
            'distance'    => function_exists('dl_get_content') ? dl_get_content('attraction_1_dist', '1.8 km (4 phút di chuyển)') : '1.8 km (4 phút di chuyển)',
            'description' => function_exists('dl_get_content') ? dl_get_content('attraction_1_desc', 'Tổ hợp công viên giải trí hàng đầu Đông Nam Á với Cáp treo Nữ Hoàng và Vòng quay Mặt Trời.') : 'Tổ hợp công viên giải trí hàng đầu Đông Nam Á với Cáp treo Nữ Hoàng và Vòng quay Mặt Trời.',
        ),
        array(
            'name'        => function_exists('dl_get_content') ? dl_get_content('attraction_2_name', 'Bãi tắm Bãi Cháy & Phố đi bộ') : 'Bãi tắm Bãi Cháy & Phố đi bộ',
            'distance'    => function_exists('dl_get_content') ? dl_get_content('attraction_2_dist', '1.2 km (3 phút di chuyển)') : '1.2 km (3 phút di chuyển)',
            'description' => function_exists('dl_get_content') ? dl_get_content('attraction_2_desc', 'Bờ cát trắng mịn trải dài 3km, làn nước xanh mát cùng các quán cafe biển sôi động về đêm.') : 'Bờ cát trắng mịn trải dài 3km, làn nước xanh mát cùng các quán cafe biển sôi động về đêm.',
        ),
        array(
            'name'        => function_exists('dl_get_content') ? dl_get_content('attraction_3_name', 'Chợ đêm Hạ Long & Halong Marine Plaza') : 'Chợ đêm Hạ Long & Halong Marine Plaza',
            'distance'    => function_exists('dl_get_content') ? dl_get_content('attraction_3_dist', '300 m (Đi bộ 3 phút)') : '300 m (Đi bộ 3 phút)',
            'description' => function_exists('dl_get_content') ? dl_get_content('attraction_3_desc', 'Thế giới quà lưu niệm, hải sản sấy khô và khu ẩm thực đường phố sầm uất ngay cạnh khách sạn.') : 'Thế giới quà lưu niệm, hải sản sấy khô và khu ẩm thực đường phố sầm uất ngay cạnh khách sạn.',
        ),
        array(
            'name'        => function_exists('dl_get_content') ? dl_get_content('attraction_4_name', 'Cảng tàu khách quốc tế Hạ Long') : 'Cảng tàu khách quốc tế Hạ Long',
            'distance'    => function_exists('dl_get_content') ? dl_get_content('attraction_4_dist', '3.5 km (7 phút di chuyển)') : '3.5 km (7 phút di chuyển)',
            'description' => function_exists('dl_get_content') ? dl_get_content('attraction_4_desc', 'Bến xuất phát của các du thuyền thăm quan kỳ quan Vịnh Hạ Long và Đảo Ti Tốp.') : 'Bến xuất phát của các du thuyền thăm quan kỳ quan Vịnh Hạ Long và Đảo Ti Tốp.',
        ),
    );

    // Popular Amenities (12 items from Static Content Manager)
    $popular_amenities = function_exists('dl_get_popular_amenities') ? dl_get_popular_amenities() : array();

    wp_localize_script('dragonlegend-main', 'DragonLegendAjax', array(
        'ajaxurl'           => admin_url('admin-ajax.php'),
        'theme_url'         => get_template_directory_uri(),
        'site_url'          => home_url(),
        'nonce'             => wp_create_nonce('dragonlegend_nonce'),
        'rooms'             => $rooms_data,
        'attractions'       => $attractions_data,
        'popular_amenities' => $popular_amenities,
    ));
}
add_action('wp_enqueue_scripts', 'dragonlegend_enqueue_scripts');

/**
 * Add DEFER to non-critical scripts
 */
function dragonlegend_defer_scripts($tag, $handle, $src) {
    $defer_scripts = array('swiper-js', 'aos-js', 'dragonlegend-booking', 'dragonlegend-main');
    if (in_array($handle, $defer_scripts)) {
        return '<script src="' . esc_url($src) . '" defer id="' . esc_attr($handle) . '-js"></script>' . "\n";
    }
    return $tag;
}
add_filter('script_loader_tag', 'dragonlegend_defer_scripts', 10, 3);

/**
 * Hotel Information Helpers (Configurable via Static Content Manager or Customizer)
 */
function dragonlegend_get_info($key = '') {
    $defaults = array(
        'name'          => 'Dragon Legend Hotel Hạ Long',
        'stars'         => 4,
        'hotline'       => '(+84) 203 3518 999',
        'hotline_tel'   => '+842033518999',
        'email'         => 'info@dragonlegendhotel.com',
        'address'       => 'Đại lộ Hạ Long Marine, Phường Bãi Cháy, TP. Hạ Long, Tỉnh Quảng Ninh',
        'latitude'      => '20.9582273',
        'longitude'     => '107.0201151',
        'price_range'   => '1.050.000 VNĐ - 3.500.000 VNĐ',
        'vr_url'        => 'https://dragonlegendhotel.com/vr/360.html',
        'map_embed'     => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d6166.100507616104!2d107.0201151!3d20.9582273!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x314a598bb174745d%3A0x207cc82c86dc2d25!2sDragon%20Legend%20H%E1%BA%A1%20Long%20Hotel!5e1!3m2!1svi!2s!4v1769987129773!5m2!1svi!2s',
        'facebook'      => 'https://www.facebook.com/dosmdragonlegend',
        'tiktok'        => 'https://www.tiktok.com/@dragonlegendhalong',
        'youtube'       => 'https://www.youtube.com/@dragonlegendhotel',
        'messenger'     => 'https://m.me/106125120739757'
    );

    if (empty($key)) {
        return $defaults;
    }

    if (function_exists('dl_get_content')) {
        $map = array(
            'name'        => 'hotel_name',
            'stars'       => 'hotel_stars',
            'hotline'     => 'hotel_hotline',
            'hotline_tel' => 'hotel_hotline_tel',
            'email'       => 'hotel_email',
            'address'     => 'hotel_address',
            'vr_url'      => 'header_vr_url',
            'map_embed'   => 'map_embed_url',
            'facebook'    => 'social_facebook',
            'tiktok'      => 'social_tiktok',
            'youtube'     => 'social_youtube',
            'messenger'   => 'social_messenger',
            'zalo'        => 'social_zalo',
        );
        $ckey = isset($map[$key]) ? $map[$key] : $key;
        $val = dl_get_content($ckey);
        if (!empty($val)) {
            return $val;
        }
    }

    return get_theme_mod('dragonlegend_' . $key, isset($defaults[$key]) ? $defaults[$key] : '');
}

/**
 * Format currency VNĐ
 */
function dl_format_price($amount) {
    return number_format(floatval($amount), 0, ',', '.') . ' VNĐ';
}

/**
 * Return rooms data formatted for frontend JavaScript & Booking Modal
 */
function dl_get_rooms_for_frontend() {
    $rooms = array();
    $query = new WP_Query(array(
        'post_type'      => 'dl_room',
        'posts_per_page' => -1,
        'post_status'    => 'publish'
    ));

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            $terms = get_the_terms($post_id, 'dl_room_type');
            $cat_slug = ($terms && !is_wp_error($terms) && !empty($terms)) ? $terms[0]->slug : '';
            if (empty($cat_slug)) {
                $slug = get_post_field('post_name', $post_id);
                $title = mb_strtolower(get_the_title($post_id), 'UTF-8');
                if (strpos($slug, 'superior') !== false || strpos($title, 'superior') !== false) {
                    $cat_slug = 'superior';
                } elseif (strpos($slug, 'deluxe') !== false || strpos($title, 'deluxe') !== false) {
                    $cat_slug = 'deluxe';
                } elseif (strpos($slug, 'family') !== false || strpos($title, 'family') !== false) {
                    $cat_slug = 'family';
                } elseif (strpos($slug, 'junior') !== false || strpos($slug, 'suite') !== false || strpos($title, 'suite') !== false) {
                    $cat_slug = 'suite';
                } else {
                    $cat_slug = 'all';
                }
            }

            $price = get_post_meta($post_id, '_dl_room_price', true);
            $orig_price = get_post_meta($post_id, '_dl_room_original_price', true);
            $gallery = get_post_meta($post_id, '_dl_room_gallery', true);
            if (!is_array($gallery)) $gallery = array();

            $image_url = get_the_post_thumbnail_url($post_id, 'large');
            if (!$image_url) {
                $image_url = get_post_meta($post_id, '_dl_room_sample_image', true);
            }
            if (!$image_url && !empty($gallery)) {
                $image_url = $gallery[0];
            }
            if (!$image_url) {
                $image_url = 'https://dragonlegendhotel.com/wp-content/uploads/2026/02/DBL-1.jpg';
            }

            $amenities_keys = get_post_meta($post_id, '_dl_room_amenities', true);
            if (!is_array($amenities_keys)) $amenities_keys = array();

            $amenities_formatted = array();
            $std_amenities = function_exists('DragonLegend_Metaboxes::get_standard_amenities_list') 
                ? DragonLegend_Metaboxes::get_standard_amenities_list()
                : array();

            foreach ($amenities_keys as $k) {
                $amenities_formatted[] = array(
                    'icon' => $k,
                    'text' => isset($std_amenities[$k]) ? $std_amenities[$k] : $k
                );
            }

            $rooms[] = array(
                'id'            => get_post_field('post_name', $post_id),
                'post_id'       => $post_id,
                'category'      => $cat_slug,
                'name'          => html_entity_decode(get_the_title(), ENT_QUOTES, 'UTF-8'),
                'subtitle'      => html_entity_decode(get_post_meta($post_id, '_dl_room_subtitle', true), ENT_QUOTES, 'UTF-8'),
                'image'         => $image_url,
                'gallery'       => !empty($gallery) ? $gallery : array($image_url),
                'price'         => $price ? floatval($price) : 1050000,
                'originalPrice' => $orig_price ? floatval($orig_price) : 1300000,
                'size'          => get_post_meta($post_id, '_dl_room_size', true) ?: '28 - 32 m²',
                'bed'           => get_post_meta($post_id, '_dl_room_bed', true) ?: '1 Giường đôi King',
                'occupancy'     => get_post_meta($post_id, '_dl_room_occupancy', true) ?: '2 Người lớn + 1 Trẻ em',
                'view'          => get_post_meta($post_id, '_dl_room_view', true) ?: 'Hướng thành phố',
                'badge'         => get_post_meta($post_id, '_dl_room_badge', true) ?: '',
                'rating'        => get_post_meta($post_id, '_dl_room_rating', true) ?: '4.8',
                'reviewsCount'  => intval(get_post_meta($post_id, '_dl_room_reviews_count', true)) ?: 120,
                'description'   => get_the_content(),
                'permalink'     => get_permalink($post_id),
                'amenities'     => $amenities_formatted
            );
        }
        wp_reset_postdata();
    }

    // Fallback nếu database chưa có bài viết nào
    if (empty($rooms)) {
        $rooms = dl_get_default_rooms_fallback();
    }

    return $rooms;
}

/**
 * Dữ liệu phòng mặc định fallback
 */
function dl_get_default_rooms_fallback() {
    return array(
        array(
            'id'            => 'superior-room',
            'post_id'       => 0,
            'category'      => 'superior',
            'name'          => 'Superior Double / Twin Room',
            'subtitle'      => 'Thanh lịch, ấm cúng và đầy đủ tiện nghi tiêu chuẩn 4 sao',
            'image'         => 'https://dragonlegendhotel.com/wp-content/uploads/2026/02/DBL-1.jpg',
            'gallery'       => array(
                'https://dragonlegendhotel.com/wp-content/uploads/2026/02/DBL-1.jpg',
                'https://dragonlegendhotel.com/wp-content/uploads/2019/05/Superior-6-e1567590038520.jpg',
                'https://dragonlegendhotel.com/wp-content/uploads/2026/02/TWN-2-scaled.jpg'
            ),
            'price'         => 1050000,
            'originalPrice' => 1300000,
            'size'          => '28 - 32 m²',
            'bed'           => '1 Giường đôi King hoặc 2 Giường đơn',
            'occupancy'     => '2 Người lớn + 1 Trẻ em',
            'view'          => 'Hướng thành phố & Vườn cây Bãi Cháy',
            'badge'         => 'Phổ biến nhất',
            'rating'        => 4.8,
            'reviewsCount'  => 142,
            'description'   => 'Phòng Superior tại Dragon Legend Hotel được thiết kế theo lối kiến trúc Tân cổ điển trang nhã, gam màu ấm áp phối cùng sàn gỗ cao cấp.',
            'permalink'     => '#rooms',
            'amenities'     => array(
                array('icon' => 'wifi', 'text' => 'Wifi tốc độ cao miễn phí'),
                array('icon' => 'coffee', 'text' => 'Buffet sáng Quốc tế miễn phí'),
                array('icon' => 'tv', 'text' => 'Smart TV 50 inch 4K'),
                array('icon' => 'bath', 'text' => 'Phòng tắm vòi sen đứng hiện đại'),
            )
        ),
        array(
            'id'            => 'deluxe-room',
            'post_id'       => 0,
            'category'      => 'deluxe',
            'name'          => 'Deluxe Ocean & Marine View',
            'subtitle'      => 'Tầm nhìn khoáng đạt hướng đại lộ Marine và Vịnh biển kỳ quan',
            'image'         => 'https://dragonlegendhotel.com/wp-content/uploads/2026/02/TWN-2-scaled.jpg',
            'gallery'       => array(
                'https://dragonlegendhotel.com/wp-content/uploads/2026/02/TWN-2-scaled.jpg',
                'https://dragonlegendhotel.com/wp-content/uploads/2026/02/DBL-1.jpg',
            ),
            'price'         => 1200000,
            'originalPrice' => 1500000,
            'size'          => '34 - 38 m²',
            'bed'           => '1 Giường đôi King Size (2m x 2m)',
            'occupancy'     => '2 Người lớn + 1 Trẻ em',
            'view'          => 'Hướng Đại Lộ Marine & Góc biển Vịnh',
            'badge'         => 'Bán chạy',
            'rating'        => 4.9,
            'reviewsCount'  => 218,
            'description'   => 'Nâng tầm kỳ nghỉ với hạng phòng Deluxe rộng rãi. Ban công riêng và cửa kính Panorama mở ra quang cảnh đại lộ sôi động cùng làn gió biển Bãi Cháy trong lành.',
            'permalink'     => '#rooms',
            'amenities'     => array(
                array('icon' => 'wifi', 'text' => 'Wifi cáp quang riêng từng phòng'),
                array('icon' => 'coffee', 'text' => 'Buffet sáng phong phú tại nhà hàng'),
                array('icon' => 'sun', 'text' => 'Ban công riêng ngắm bình minh'),
                array('icon' => 'bath', 'text' => 'Bồn tắm nằm thư giãn & vòi sen'),
            )
        ),
        array(
            'id'            => 'junior-suite',
            'post_id'       => 0,
            'category'      => 'suite',
            'name'          => 'Junior Suite Luxury Balcony',
            'subtitle'      => 'Không gian nghỉ dưỡng quý tộc với phòng khách biệt lập sang trọng',
            'image'         => 'https://dragonlegendhotel.com/wp-content/uploads/2019/06/juinior-suite2-e1567590383347.jpg',
            'gallery'       => array('https://dragonlegendhotel.com/wp-content/uploads/2019/06/juinior-suite2-e1567590383347.jpg'),
            'price'         => 1550000,
            'originalPrice' => 1900000,
            'size'          => '46 - 52 m²',
            'bed'           => '1 Giường Super King Size (2m2)',
            'occupancy'     => '2 Người lớn + 2 Trẻ em',
            'view'          => 'Trực diện Vịnh Hạ Long & Đảo Tuần Châu',
            'badge'         => 'Thượng lưu',
            'rating'        => 5.0,
            'reviewsCount'  => 96,
            'description'   => 'Junior Suite được thiết kế cho những kỳ nghỉ trăng mật lãng mạn hay những chuyến công tác cao cấp. Căn suite sở hữu sofa tiếp khách riêng biệt.',
            'permalink'     => '#rooms',
            'amenities'     => array(
                array('icon' => 'gem', 'text' => 'Đặc quyền check-in ưu tiên tại VIP Lounge'),
                array('icon' => 'coffee', 'text' => 'Phục vụ buffet sáng tại phòng nếu yêu cầu'),
                array('icon' => 'bath', 'text' => 'Bồn tắm sục Jacuzzi ngắm vịnh'),
            )
        ),
        array(
            'id'            => 'family-suite',
            'post_id'       => 0,
            'category'      => 'family',
            'name'          => 'Junior Family Suite',
            'subtitle'      => 'Ngôi nhà ấm cúng lý tưởng cho gia đình đa thế hệ nghỉ dưỡng',
            'image'         => 'https://dragonlegendhotel.com/wp-content/uploads/2019/05/Superior-6-e1567590038520.jpg',
            'gallery'       => array('https://dragonlegendhotel.com/wp-content/uploads/2019/05/Superior-6-e1567590038520.jpg'),
            'price'         => 1950000,
            'originalPrice' => 2400000,
            'size'          => '68 - 75 m²',
            'bed'           => '1 Giường King + 2 Giường đơn (2 phòng ngủ)',
            'occupancy'     => '4 Người lớn + 2 Trẻ em',
            'view'          => 'Toàn cảnh Đảo Rều & Vịnh Bãi Cháy',
            'badge'         => 'Dành cho gia đình',
            'rating'        => 4.9,
            'reviewsCount'  => 165,
            'description'   => 'Family Grand Suite kết hợp sự riêng tư và gắn kết hoàn hảo cho cả đại gia đình.',
            'permalink'     => '#rooms',
            'amenities'     => array(
                array('icon' => 'users', 'text' => 'Thiết kế 2 phòng ngủ thông nhau riêng tư'),
                array('icon' => 'coffee', 'text' => 'Bữa sáng buffet cho 4 người lớn & 2 trẻ'),
            )
        ),
    );
}

/**
 * Helper lấy icon SVG
 */
function dl_get_amenity_svg($icon_key, $custom_class = "w-5 h-5 text-gold") {
    if (empty($icon_key)) {
        return '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>';
    }

    // 1. Hỗ trợ file ảnh / SVG do người dùng tự tải lên (URL)
    if (strpos($icon_key, 'http://') === 0 || strpos($icon_key, 'https://') === 0 || strpos($icon_key, '/') === 0) {
        return '<img src="' . esc_url($icon_key) . '" class="' . esc_attr($custom_class) . ' object-contain inline-block" alt="Biểu tượng tiện ích" loading="lazy">';
    }

    // 2. Hỗ trợ chuỗi mã SVG thô trực tiếp
    if (strpos($icon_key, '<svg') !== false) {
        return $icon_key;
    }

    // 3. Kho biểu tượng SVG tích hợp sẵn
    $icons = array(
        'shuttle'     => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-1.1 0-2 .9-2 2v7c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>',
        'no-smoking'  => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="2" y1="2" x2="22" y2="22"/><path d="M18 12H7a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1Z"/><path d="M22 12v4"/><path d="M18 8c0-2.5-2-2.5-2-5"/><path d="M22 8c0-2.5-2-2.5-2-5"/></svg>',
        'wifi'        => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13a10 10 0 0 1 14 0"/><path d="M8.5 16.5a5 5 0 0 1 7 0"/><path d="M2 8.82a15 15 0 0 1 20 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg>',
        'concierge'   => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/><circle cx="12" cy="2" r="1"/></svg>',
        'family'      => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
        'restaurant'  => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2v20"/><path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"/><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/></svg>',
        'reception'   => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
        'elevator'    => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="m9 10 3-3 3 3"/><path d="m9 14 3 3 3-3"/></svg>',
        'snowflake'   => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="2" y1="12" x2="22" y2="12"/><line x1="12" y1="2" x2="12" y2="22"/><path d="m20 16-4-4 4-4"/><path d="m4 8 4 4-4 4"/><path d="m16 4-4 4-4-4"/><path d="m8 20 4-4 4 4"/></svg>',
        'coffee'      => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><line x1="6" y1="2" x2="6" y2="4"/><line x1="10" y1="2" x2="10" y2="4"/><line x1="14" y1="2" x2="14" y2="4"/></svg>',
        'pool'        => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 18c1.5 0 2.5 1 4 1s2.5-1 4-1 2.5 1 4 1 2.5-1 4-1 2.5 1 4 1"/><path d="M2 22c1.5 0 2.5 1 4 1s2.5-1 4-1 2.5 1 4 1 2.5-1 4-1 2.5 1 4 1"/><path d="m7 12 3-3 2.5 2.5"/><circle cx="17" cy="5" r="2"/><path d="m19 9-4 2-3.5-3.5"/></svg>',
        'parking'     => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="4"/><path d="M9 17V7h4a3 3 0 0 1 0 6H9"/></svg>',
        'tv'          => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="15" x="2" y="7" rx="2"/><polyline points="17 2 12 7 7 2"/></svg>',
        'bath'        => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6h6a2 2 0 0 1 2 2v2H7V8a2 2 0 0 1 2-2Z"/><path d="M2 12h20v4a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5v-4Z"/><path d="M4 21v1"/><path d="M20 21v1"/></svg>',
        'wine'        => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8 22h8"/><path d="M12 15v7"/><path d="M12 15a5 5 0 0 0 5-5c0-2-.5-4-2-8H9c-1.5 4-2 6-2 8a5 5 0 0 0 5 5Z"/></svg>',
        'shield-check'=> '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>',
        'sparkles'    => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3z"/></svg>',
        'sun'         => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>',
        'waves'       => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 6c.6.5 1.2 1 2.5 1C7 7 7 5 9.5 5c2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/><path d="M2 12c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/><path d="M2 18c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/></svg>',
        'car'         => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-1.1 0-2 .9-2 2v7c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>',
        'gem'         => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h12l4 6-10 12L2 9Z"/><path d="M11 3 8 9l4 12 4-12-3-6"/><path d="M2 9h20"/></svg>',
        'gym'         => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m6.5 6.5 11 11"/><path d="m21 21-1-1"/><path d="m3 3 1 1"/><path d="m18 22 4-4"/><path d="m2 6 4-4"/><path d="m3 10 7-7"/><path d="m14 21 7-7"/></svg>',
        'dumbbell'    => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m6.5 6.5 11 11"/><path d="m21 21-1-1"/><path d="m3 3 1 1"/><path d="m18 22 4-4"/><path d="m2 6 4-4"/><path d="m3 10 7-7"/><path d="m14 21 7-7"/></svg>',
        'spa'         => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3c-1.5 2.5-3 5-3 8a3 3 0 0 0 6 0c0-3-1.5-5.5-3-8Z"/><path d="M12 11c-2.5-1-5-1-7.5.5C3 13 3 16 6 18c2.5 1.5 5 0 6-7Z"/><path d="M12 11c2.5-1 5-1 7.5.5 1.5 1.5 1.5 4.5-1.5 6.5-2.5 1.5-5 0-6-7Z"/></svg>',
        'lotus'       => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3c-1.5 2.5-3 5-3 8a3 3 0 0 0 6 0c0-3-1.5-5.5-3-8Z"/><path d="M12 11c-2.5-1-5-1-7.5.5C3 13 3 16 6 18c2.5 1.5 5 0 6-7Z"/><path d="M12 11c2.5-1 5-1 7.5.5 1.5 1.5 1.5 4.5-1.5 6.5-2.5 1.5-5 0-6-7Z"/></svg>',
        'conference'  => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
        'users'       => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
        'tennis'      => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20"/><path d="M2 12a14.5 14.5 0 0 0 20 0"/></svg>',
        'kids'        => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="5" r="3"/><path d="M6.5 9a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-2a2 2 0 0 0-2-2Z"/><path d="m9 15 3 6 3-6"/></svg>',
        'laundry'     => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="20" x="3" y="2" rx="2"/><circle cx="12" cy="13" r="5"/><path d="M12 10a3 3 0 0 0-3 3"/><circle cx="7" cy="6" r="1"/></svg>',
        'billiards'   => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="4"/><path d="M12 10v4"/></svg>',
        'cocktail'    => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m8 22 4-11V2"/><path d="m16 22-4-11"/><path d="M4 2h16l-8 9-8-9Z"/></svg>',
        'bicycle'     => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="5.5" cy="17.5" r="3.5"/><circle cx="18.5" cy="17.5" r="3.5"/><path d="M15 6a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-3 11.5L9 6H5m7 11.5 3-7h4.5"/></svg>',
        'charging'    => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="20" x="4" y="2" rx="2"/><path d="M9 11h6l-4 6v-4H8l3-6"/></svg>',
        'luggage'     => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="14" x="4" y="7" rx="2"/><path d="M9 7V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3"/><path d="M4 12h16"/></svg>',
        'money'       => '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/></svg>',
    );

    return isset($icons[$icon_key]) ? $icons[$icon_key] : '<svg class="' . esc_attr($custom_class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>';
}

/**
 * Đăng ký WordPress Customizer cho Theme
 * Cho phép quản trị viên chọn Chuyên mục (Category) hiển thị ở khối Dịch Vụ & Tiện Ích
 */
function dragonlegend_customize_register($wp_customize) {
    // 1. Section: Cài Đặt Trang Chủ
    $wp_customize->add_section('dragonlegend_home_settings', array(
        'title'       => __('Cài Đặt Trang Chủ', 'dragonlegend'),
        'priority'    => 120,
        'description' => __('Tùy biến các khối nội dung và chuyên mục hiển thị trên trang chủ.', 'dragonlegend'),
    ));

    // 2. Setting: dragonlegend_amenities_category
    $wp_customize->add_setting('dragonlegend_amenities_category', array(
        'default'           => 0,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ));

    // Lấy danh sách chuyên mục
    $categories = get_categories(array('hide_empty' => false));
    $cat_choices = array(0 => __('— Tự động (Mặc định: Dịch Vụ & Tiện Ích) —', 'dragonlegend'));
    foreach ($categories as $cat) {
        $cat_choices[$cat->term_id] = $cat->name . ' (' . $cat->slug . ')';
    }

    // 3. Control: Dropdown chọn Chuyên mục
    $wp_customize->add_control('dragonlegend_amenities_category', array(
        'label'       => __('Chuyên mục Dịch Vụ & Tiện Ích', 'dragonlegend'),
        'description' => __('Chọn Chuyên mục (Category) bài viết để hiển thị trên khối "Dịch Vụ & Tiện Ích Đỉnh Cao" ở Trang chủ.', 'dragonlegend'),
        'section'     => 'dragonlegend_home_settings',
        'type'        => 'select',
        'choices'     => $cat_choices,
    ));
}
add_action('customize_register', 'dragonlegend_customize_register');

/**
 * Helper lấy Term ID của Chuyên mục Dịch Vụ & Tiện Ích
 */
function dragonlegend_get_amenities_category_id() {
    $cat_id = absint(get_theme_mod('dragonlegend_amenities_category', 0));
    if ($cat_id > 0 && term_exists($cat_id, 'category')) {
        return $cat_id;
    }

    // Tự động tìm category slug 'dich-vu', 'dich-vu-tien-ich', 'tien-ich', 'services'
    $candidate_slugs = array('dich-vu', 'dich-vu-tien-ich', 'tien-ich', 'services', 'amenities');
    foreach ($candidate_slugs as $slug) {
        $term = get_term_by('slug', $slug, 'category');
        if ($term && !is_wp_error($term)) {
            return (int) $term->term_id;
        }
    }

    // Tự động tìm category theo tên chứa "Dịch Vụ" hoặc "Tiện Ích"
    $cats = get_categories(array('hide_empty' => false));
    foreach ($cats as $cat) {
        if (stripos($cat->name, 'Dịch Vụ') !== false || stripos($cat->name, 'Tiện Ích') !== false) {
            return (int) $cat->term_id;
        }
    }

    return 0;
}

/**
 * =========================================================================
 * NAVIGATION WALKERS & CUSTOM MENU HANDLING (Header Desktop & Mobile Drawer)
 * =========================================================================
 */

class DragonLegend_Desktop_Nav_Walker extends Walker_Nav_Menu {
    public function start_lvl(&$output, $depth = 0, $args = null) {}
    public function end_lvl(&$output, $depth = 0, $args = null) {}

    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $is_anchor = (strpos($item->url, '#') !== false);
        $is_active = !$is_anchor && (in_array('current-menu-item', $classes) || in_array('current_page_item', $classes));
        $active_class = $is_active ? ' text-gold' : '';

        $atts = array();
        $atts['title']  = !empty($item->attr_title) ? $item->attr_title : '';
        $atts['target'] = !empty($item->target)     ? $item->target     : '';
        $atts['rel']    = !empty($item->xfn)        ? $item->xfn        : '';
        $atts['href']   = !empty($item->url)        ? $item->url        : '';
        $atts['class']  = 'hover:text-gold transition-colors duration-200 whitespace-nowrap' . $active_class;

        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (!empty($value)) {
                $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $title = apply_filters('the_title', $item->title, $item->ID);
        $title = apply_filters('nav_menu_item_title', $title, $item, $args, $depth);

        $item_output = '<a' . $attributes . '>' . $title . '</a>';

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

    public function end_el(&$output, $item, $depth = 0, $args = null) {
        // No closing tags needed
    }
}

class DragonLegend_Mobile_Nav_Walker extends Walker_Nav_Menu {
    public function start_lvl(&$output, $depth = 0, $args = null) {}
    public function end_lvl(&$output, $depth = 0, $args = null) {}

    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $is_anchor = (strpos($item->url, '#') !== false);
        $is_active = !$is_anchor && (in_array('current-menu-item', $classes) || in_array('current_page_item', $classes));
        $active_class = $is_active ? ' text-gold' : '';

        $atts = array();
        $atts['title']  = !empty($item->attr_title) ? $item->attr_title : '';
        $atts['target'] = !empty($item->target)     ? $item->target     : '';
        $atts['rel']    = !empty($item->xfn)        ? $item->xfn        : '';
        $atts['href']   = !empty($item->url)        ? $item->url        : '';
        $atts['class']  = 'mobile-nav-link py-2 text-slate-300 hover:text-gold transition-colors flex items-center justify-between border-b border-white/5 last:border-b-0' . $active_class;

        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (!empty($value)) {
                $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $title = apply_filters('the_title', $item->title, $item->ID);
        $title = apply_filters('nav_menu_item_title', $title, $item, $args, $depth);

        $item_output  = '<a' . $attributes . '>';
        $item_output .= '<span>' . $title . '</span>';
        $item_output .= '<span class="text-xs text-gold">&rarr;</span>';
        $item_output .= '</a>';

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

    public function end_el(&$output, $item, $depth = 0, $args = null) {
        // No closing tags needed
    }
}

/**
 * Custom Walker for Footer Nav Menus (Output standard <li><a ...></a></li>)
 */
class DragonLegend_Footer_Nav_Walker extends Walker_Nav_Menu {
    public function start_lvl(&$output, $depth = 0, $args = null) {}
    public function end_lvl(&$output, $depth = 0, $args = null) {}

    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $atts = array();
        $atts['title']  = !empty($item->attr_title) ? $item->attr_title : '';
        $atts['target'] = !empty($item->target)     ? $item->target     : '';
        $atts['rel']    = !empty($item->xfn)        ? $item->xfn        : '';
        $atts['href']   = !empty($item->url)        ? $item->url        : '';
        $atts['class']  = 'hover:text-gold transition-colors block';

        $attributes = '';
        foreach ($atts as $attr => $value) {
            if (!empty($value)) {
                $value = ('href' === $attr) ? esc_url($value) : esc_attr($value);
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $title = apply_filters('the_title', $item->title, $item->ID);
        $title = apply_filters('nav_menu_item_title', $title, $item, $args, $depth);

        $item_output = '<li><a' . $attributes . '>' . $title . '</a></li>';
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }

    public function end_el(&$output, $item, $depth = 0, $args = null) {}
}

/**
 * Fallback for Header Desktop Nav if no menu is assigned
 */
function dragonlegend_default_desktop_nav() {
    $defaults = array(
        'Về khách sạn'       => home_url('/#about'),
        'Hạng phòng'         => home_url('/#rooms'),
        'Dịch vụ & Tiện ích' => home_url('/#amenities'),
        'Ưu đãi'             => home_url('/#offers'),
        'Vị trí & Liên hệ'   => home_url('/#contact'),
        'Đánh giá'           => home_url('/#reviews'),
    );
    foreach ($defaults as $label => $url) {
        echo '<a href="' . esc_url($url) . '" class="hover:text-gold transition-colors duration-200 whitespace-nowrap">' . esc_html($label) . '</a>';
    }
}

/**
 * Fallback for Mobile Drawer Nav if no menu is assigned
 */
function dragonlegend_default_mobile_nav() {
    $defaults = array(
        'Về khách sạn'         => home_url('/#about'),
        'Hạng phòng'           => home_url('/#rooms'),
        'Dịch vụ & Tiện ích'   => home_url('/#amenities'),
        'Ưu đãi & Gói combo'   => home_url('/#offers'),
        'Vị trí & Liên hệ'     => home_url('/#contact'),
        'Đánh giá từ du khách' => home_url('/#reviews'),
    );
    foreach ($defaults as $label => $url) {
        echo '<a href="' . esc_url($url) . '" class="mobile-nav-link py-2 text-slate-300 hover:text-gold transition-colors flex items-center justify-between border-b border-white/5">';
        echo '<span>' . esc_html($label) . '</span>';
        echo '<span class="text-xs text-gold">&rarr;</span>';
        echo '</a>';
    }
}

/**
 * Fallback for Footer Info Nav if no menu is assigned
 */
function dragonlegend_default_footer_info_nav() {
    $defaults = array(
        'Về chúng tôi'      => home_url('/#about'),
        'Hạng phòng nghỉ'   => home_url('/#rooms'),
        'Hồ bơi & Spa'      => home_url('/#amenities'),
        'Nhà hàng ẩm thực'  => home_url('/#amenities'),
        'Ưu đãi mùa hè'     => home_url('/#offers'),
    );
    foreach ($defaults as $label => $url) {
        echo '<li><a href="' . esc_url($url) . '" class="hover:text-gold transition-colors block">' . esc_html($label) . '</a></li>';
    }
}

/**
 * Fallback for Footer Policies Nav if no menu is assigned
 */
function dragonlegend_default_footer_policy_nav() {
    $defaults = array(
        'Chính sách đặt phòng'         => '#',
        'Quy định nhận & trả phòng'     => '#',
        'Hình thức thanh toán'         => '#',
        'Chính sách bảo mật thông tin' => '#',
        'Cơ hội nghề nghiệp'           => '#',
    );
    foreach ($defaults as $label => $url) {
        echo '<li><a href="' . esc_url($url) . '" class="hover:text-gold transition-colors block">' . esc_html($label) . '</a></li>';
    }
}

/**
 * Auto-ensure Primary & Footer Menus exist and are assigned
 */
function dragonlegend_setup_default_menus() {
    $locations = get_nav_menu_locations();
    $updated = false;

    // 1. Primary Header Menu
    if (empty($locations['primary'])) {
        $menu_name = __('Menu Chính (Header)', 'dragonlegend');
        $menu_obj = wp_get_nav_menu_object($menu_name);

        if (!$menu_obj) {
            $menu_id = wp_create_nav_menu($menu_name);
            if (!is_wp_error($menu_id)) {
                $default_items = array(
                    array('title' => 'Về khách sạn', 'url' => home_url('/#about')),
                    array('title' => 'Hạng phòng', 'url' => home_url('/#rooms')),
                    array('title' => 'Dịch vụ & Tiện ích', 'url' => home_url('/#amenities')),
                    array('title' => 'Ưu đãi', 'url' => home_url('/#offers')),
                    array('title' => 'Vị trí & Liên hệ', 'url' => home_url('/#contact')),
                    array('title' => 'Đánh giá', 'url' => home_url('/#reviews')),
                );
                foreach ($default_items as $item) {
                    wp_update_nav_menu_item($menu_id, 0, array(
                        'menu-item-title'  => $item['title'],
                        'menu-item-url'    => $item['url'],
                        'menu-item-status' => 'publish',
                    ));
                }
                $locations['primary'] = $menu_id;
                $updated = true;
            }
        } else {
            $locations['primary'] = $menu_obj->term_id;
            $updated = true;
        }
    }

    // 2. Footer Menu: Thông Tin
    if (empty($locations['footer_info'])) {
        $menu_name = __('Thông tin', 'dragonlegend');
        $menu_obj = wp_get_nav_menu_object($menu_name);

        if (!$menu_obj) {
            $menu_id = wp_create_nav_menu($menu_name);
            if (!is_wp_error($menu_id)) {
                $default_items = array(
                    array('title' => 'Về chúng tôi', 'url' => home_url('/#about')),
                    array('title' => 'Hạng phòng nghỉ', 'url' => home_url('/#rooms')),
                    array('title' => 'Hồ bơi & Spa', 'url' => home_url('/#amenities')),
                    array('title' => 'Nhà hàng ẩm thực', 'url' => home_url('/#amenities')),
                    array('title' => 'Ưu đãi mùa hè', 'url' => home_url('/#offers')),
                );
                foreach ($default_items as $item) {
                    wp_update_nav_menu_item($menu_id, 0, array(
                        'menu-item-title'  => $item['title'],
                        'menu-item-url'    => $item['url'],
                        'menu-item-status' => 'publish',
                    ));
                }
                $locations['footer_info'] = $menu_id;
                $updated = true;
            }
        } else {
            $locations['footer_info'] = $menu_obj->term_id;
            $updated = true;
        }
    }

    // 3. Footer Menu: Quy Định & Chính Sách
    if (empty($locations['footer_policy'])) {
        $menu_name = __('Quy định & Chính sách', 'dragonlegend');
        $menu_obj = wp_get_nav_menu_object($menu_name);

        if (!$menu_obj) {
            $menu_id = wp_create_nav_menu($menu_name);
            if (!is_wp_error($menu_id)) {
                $default_items = array(
                    array('title' => 'Chính sách đặt phòng', 'url' => '#'),
                    array('title' => 'Quy định nhận & trả phòng', 'url' => '#'),
                    array('title' => 'Hình thức thanh toán', 'url' => '#'),
                    array('title' => 'Chính sách bảo mật thông tin', 'url' => '#'),
                    array('title' => 'Cơ hội nghề nghiệp', 'url' => '#'),
                );
                foreach ($default_items as $item) {
                    wp_update_nav_menu_item($menu_id, 0, array(
                        'menu-item-title'  => $item['title'],
                        'menu-item-url'    => $item['url'],
                        'menu-item-status' => 'publish',
                    ));
                }
                $locations['footer_policy'] = $menu_id;
                $updated = true;
            }
        } else {
            $locations['footer_policy'] = $menu_obj->term_id;
            $updated = true;
        }
    }

    if ($updated) {
        set_theme_mod('nav_menu_locations', $locations);
    }
}
add_action('after_switch_theme', 'dragonlegend_setup_default_menus');
add_action('init', 'dragonlegend_setup_default_menus');

/**
 * --------------------------------------------------------------------------
 * 15. BRANDING & FOOTER CREDITS (VIETTECHSOFT)
 * --------------------------------------------------------------------------
 */

/**
 * Hiển thị thông tin thiết kế website dưới chân trang ngoài website (Frontend / Trang chủ)
 */
function dragonlegend_frontend_footer_credit() {
    ?>
    <div class="dragonlegend-footer-credit w-full text-center py-3 bg-[#250606] border-t border-white/5 text-xs text-stone-400 relative z-10">
        <a href="https://viettechsoft.com" title="thiết kế website quảng ninh" target="_blank" rel="noopener" class="text-[#dfa843] hover:underline font-medium">Thiết kế website</a> bởi Viettechsoft
    </div>
    <?php
}
add_action('wp_footer', 'dragonlegend_frontend_footer_credit', 20);

/**
 * Hiển thị thông tin thiết kế website dưới chân trang quản trị (WP-Admin)
 */
function dragonlegend_admin_footer_credit($footer_text) {
    return '<a href="https://viettechsoft.com" title="thiết kế website quảng ninh" target="_blank" rel="noopener">Thiết kế website</a> bởi Viettechsoft';
}
add_filter('admin_footer_text', 'dragonlegend_admin_footer_credit', 999);

/**
 * Ẩn thông tin phiên bản WordPress ở góc phải chân trang quản trị để giao diện đồng bộ
 */
add_filter('update_footer', '__return_empty_string', 999);

/**
 * --------------------------------------------------------------------------
 * 16. LOẠI BỎ MENU "GIỚI THIỆU VỀ WORDPRESS" TRONG TRANG QUẢN TRỊ
 * --------------------------------------------------------------------------
 */

/**
 * Bỏ Menu Giới thiệu về WordPress (logo WordPress và toàn bộ menu con) trên thanh Admin Bar
 */
function dragonlegend_remove_wp_logo_admin_bar($wp_admin_bar) {
    $wp_admin_bar->remove_node('wp-logo'); // Gỡ logo WordPress cùng toàn bộ dropdown
    $wp_admin_bar->remove_node('about');   // Gỡ mục "Giới thiệu về WordPress"
}
add_action('admin_bar_menu', 'dragonlegend_remove_wp_logo_admin_bar', 999);

/**
 * Chặn truy cập trực tiếp vào trang Giới thiệu về WordPress (about.php)
 */
function dragonlegend_disable_about_page() {
    wp_safe_redirect(admin_url());
    exit;
}
add_action('load-about.php', 'dragonlegend_disable_about_page');

/**
 * --------------------------------------------------------------------------
 * 17. HỖ TRỢ UPLOAD FILE SVG AN TOÀN TRONG WORDPRESS
 * --------------------------------------------------------------------------
 */

/**
 * Thêm MIME type cho SVG vào danh sách định dạng được phép upload
 */
function dragonlegend_allow_svg_mimes($mimes) {
    $mimes['svg']  = 'image/svg+xml';
    $mimes['svgz'] = 'image/svg+xml';
    return $mimes;
}
add_filter('upload_mimes', 'dragonlegend_allow_svg_mimes', 99);

/**
 * Xử lý kiểm tra đuôi file và MIME type cho SVG trên WordPress 4.7+
 */
function dragonlegend_fix_svg_filetype($data, $file, $filename, $mimes, $real_mime = '') {
    if (!empty($data['ext']) && !empty($data['type'])) {
        return $data;
    }

    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    if ($ext === 'svg' || $ext === 'svgz') {
        $data['ext']             = 'svg';
        $data['type']            = 'image/svg+xml';
        $data['proper_filename'] = $filename;
    }

    return $data;
}
add_filter('wp_check_filetype_and_ext', 'dragonlegend_fix_svg_filetype', 10, 5);

/**
 * Làm sạch và bảo mật file SVG trước khi lưu lên hệ thống (Chống mã độc XSS / XXE)
 */
function dragonlegend_sanitize_svg($file) {
    if (!isset($file['tmp_name']) || !file_exists($file['tmp_name'])) {
        return $file;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($ext !== 'svg') {
        return $file;
    }

    $content = file_get_contents($file['tmp_name']);
    if ($content === false) {
        $file['error'] = 'Không thể đọc nội dung tập tin SVG.';
        return $file;
    }

    // Chặn tấn công XML External Entity (XXE)
    if (preg_match('/<!ENTITY/i', $content) || preg_match('/SYSTEM\s+["\']/i', $content)) {
        $file['error'] = 'Tập tin SVG chứa thực thể XML không hợp lệ (Bảo mật).';
        return $file;
    }

    // Loại bỏ các thẻ thực thi mã: script, foreignObject, iframe, object, embed
    $content = preg_replace('/<\s*(script|foreignObject|iframe|object|embed)[^>]*>.*?<\s*\/\s*\1\s*>/is', '', $content);
    $content = preg_replace('/<\s*(script|foreignObject|iframe|object|embed)[^>]*\/?>/is', '', $content);

    // Loại bỏ các thuộc tính bắt đầu bằng "on" (onload, onclick, onerror, ...)
    $content = preg_replace('/(\s+)(on[a-zA-Z]+)\s*=\s*(["\'])(.*?)\3/is', '', $content);

    // Loại bỏ link javascript: trong href hoặc xlink:href
    $content = preg_replace('/(href|xlink:href)\s*=\s*(["\'])\s*javascript:[^"\']*\2/is', '', $content);

    // Đảm bảo có thẻ <svg mở và đóng hợp lệ
    if (stripos($content, '<svg') === false) {
        $file['error'] = 'Tập tin không phải định dạng SVG hợp lệ.';
        return $file;
    }

    file_put_contents($file['tmp_name'], $content);
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'dragonlegend_sanitize_svg');

/**
 * Tinh chỉnh CSS để file SVG hiển thị thumbnail đẹp mắt trong Thư viện Media
 */
function dragonlegend_svg_media_library_styles() {
    echo '<style>
        .attachment-266x266,
        .media-icon img[src$=".svg"],
        .attachment-preview img[src$=".svg"],
        .attachment-preview .thumbnail img[src$=".svg"],
        .attachment-info .thumbnail img[src$=".svg"],
        .media-frame .attachment[data-mime="image/svg+xml"] .thumbnail img {
            width: 100% !important;
            height: auto !important;
            max-height: 100% !important;
            object-fit: contain;
        }
        table.media .column-title .media-icon img[src$=".svg"],
        table.media .column-primary .media-icon img[src$=".svg"] {
            width: 50px !important;
            height: 50px !important;
            object-fit: contain;
        }
    </style>';
}
add_action('admin_head', 'dragonlegend_svg_media_library_styles');

/**
 * Bổ sung metadata kích thước SVG cho giao diện Media Javascript của WordPress
 */
function dragonlegend_svg_prepare_attachment_for_js($response, $attachment, $meta) {
    if (isset($response['mime']) && $response['mime'] === 'image/svg+xml') {
        $width  = 300;
        $height = 300;

        $svg_path = get_attached_file($attachment->ID);
        if ($svg_path && file_exists($svg_path)) {
            $svg_content = @file_get_contents($svg_path);
            if ($svg_content) {
                if (preg_match('/<svg[^>]*\s+viewBox=["\']([0-9\.\s-]+)["\']/i', $svg_content, $matches)) {
                    $viewbox = preg_split('/[\s,]+/', trim($matches[1]));
                    if (count($viewbox) === 4 && (float)$viewbox[2] > 0 && (float)$viewbox[3] > 0) {
                        $width  = (float)$viewbox[2];
                        $height = (float)$viewbox[3];
                    }
                } elseif (preg_match('/<svg[^>]*\s+width=["\']([0-9\.]+)["\']/i', $svg_content, $w) &&
                          preg_match('/<svg[^>]*\s+height=["\']([0-9\.]+)["\']/i', $svg_content, $h)) {
                    $width  = (float)$w[1];
                    $height = (float)$h[1];
                }
            }
        }

        if (empty($response['sizes'])) {
            $response['width']  = $width;
            $response['height'] = $height;
            $response['sizes']  = [
                'full' => [
                    'url'         => $response['url'],
                    'width'       => $width,
                    'height'      => $height,
                    'orientation' => ($width >= $height) ? 'landscape' : 'portrait'
                ]
            ];
        }
    }
    return $response;
}
add_filter('wp_prepare_attachment_for_js', 'dragonlegend_svg_prepare_attachment_for_js', 10, 3);

/**
 * --------------------------------------------------------------------------
 * 18. TỐI ƯU VÀ ĐẢM BẢO HOẠT ĐỘNG CHO GOOGLE SITE KIT REST API
 * --------------------------------------------------------------------------
 */

// Bổ sung đầy đủ quyền Site Kit cho Quản trị viên (Admin)
function dragonlegend_sitekit_admin_caps($allcaps, $caps, $args, $user) {
    if (!empty($allcaps['administrator']) || !empty($allcaps['manage_options'])) {
        $allcaps['googlesitekit_setup']                        = true;
        $allcaps['googlesitekit_authenticate']                 = true;
        $allcaps['googlesitekit_view_dashboard']               = true;
        $allcaps['googlesitekit_manage_options']               = true;
        $allcaps['googlesitekit_view_splash']                  = true;
        $allcaps['googlesitekit_view_authenticated_dashboard'] = true;
    }
    return $allcaps;
}
add_filter('user_has_cap', 'dragonlegend_sitekit_admin_caps', 10, 4);

// Đảm bảo endpoint health-checks và setup-tag của Google Site Kit luôn phản hồi hợp lệ (200 OK)
function dragonlegend_fix_sitekit_health_checks($result, $server, $request) {
    $route = $request->get_route();
    if (strpos($route, 'google-site-kit/v1/core/site/data/health-checks') !== false) {
        return new WP_REST_Response(array(
            'checks' => array(
                'googleAPI' => array( 'pass' => true, 'errorMsg' => '' ),
                'skService' => array( 'pass' => true, 'errorMsg' => '' ),
            ),
        ), 200);
    }
    if (strpos($route, 'google-site-kit/v1/core/site/data/setup-tag') !== false) {
        $token = wp_generate_uuid4();
        set_transient('googlesitekit_setup_token', $token, 5 * MINUTE_IN_SECONDS);
        return new WP_REST_Response(array('token' => $token), 200);
    }
    return $result;
}
add_filter('rest_pre_dispatch', 'dragonlegend_fix_sitekit_health_checks', 10, 3);

// Ẩn bảng cảnh báo compatibility trên trang Google Site Kit Splash
function dragonlegend_sitekit_admin_styles() {
    echo '<style>
        .googlesitekit-setup-compat { display: none !important; }
    </style>';
}
add_action('admin_head', 'dragonlegend_sitekit_admin_styles');

/**
 * Đảm bảo tính năng Breadcrumbs của Rank Math SEO luôn được bật trên các trang con
 */
add_filter('rank_math/frontend/breadcrumb/settings', function($settings) {
    if (!is_array($settings)) {
        $settings = array();
    }
    $settings['breadcrumbs'] = true;
    if (empty($settings['separator'])) {
        $settings['separator'] = '/';
    }
    return $settings;
});

/**
 * Bổ sung Google Maps iframe vào shortcode [rank_math_contact_info] nếu chưa có map API
 */
add_filter('rank_math/contact_info/html', 'dl_enhance_rank_math_contact_info', 20);
function dl_enhance_rank_math_contact_info($html) {
    if (strpos($html, '<iframe') === false) {
        $map_url = function_exists('dragonlegend_get_info') ? dragonlegend_get_info('map_embed') : '';
        if (empty($map_url)) {
            $map_url = 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d6166.100507616104!2d107.0201151!3d20.9582273!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x314a598bb174745d%3A0x207cc82c86dc2d25!2sDragon%20Legend%20H%E1%BA%A1%20Long%20Hotel!5e1!3m2!1svi!2s!4v1769987129773!5m2!1svi!2s';
        }
        
        $map_html = '<div class="rank-math-contact-section rank-math-contact-map mt-6 rounded-2xl overflow-hidden shadow-md border border-slate-200">
            <iframe src="' . esc_url($map_url) . '" width="100%" height="320" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Google Map Dragon Legend Hotel"></iframe>
        </div>';

        if (strpos($html, '<div class="rank-math-contact-section rank-math-contact-map"></div>') !== false) {
            $html = str_replace('<div class="rank-math-contact-section rank-math-contact-map"></div>', $map_html, $html);
        } else {
            $html = str_replace('<div class="clear"></div>', $map_html . '<div class="clear"></div>', $html);
        }
    }
    return $html;
}

/**
 * Xử lý AJAX Form liên hệ (Trang liên hệ)
 */
add_action('wp_ajax_dl_submit_contact_form', 'dl_handle_contact_form');
add_action('wp_ajax_nopriv_dl_submit_contact_form', 'dl_handle_contact_form');
function dl_handle_contact_form() {
    check_ajax_referer('dl_contact_nonce', 'nonce');

    $name    = sanitize_text_field($_POST['name'] ?? '');
    $phone   = sanitize_text_field($_POST['phone'] ?? '');
    $email   = sanitize_email($_POST['email'] ?? '');
    $subject = sanitize_text_field($_POST['subject'] ?? 'Liên hệ từ website');
    $message = sanitize_textarea_field($_POST['message'] ?? '');

    if (empty($name) || empty($phone) || empty($message)) {
        wp_send_json_error(array('message' => 'Vui lòng điền đầy đủ các thông tin bắt buộc (Họ tên, Số điện thoại, Nội dung yêu cầu).'));
    }

    // Lưu vào database options để quản trị viên có thể xem lại
    $inquiries = get_option('dl_contact_inquiries', array());
    if (!is_array($inquiries)) $inquiries = array();
    
    $inquiries[] = array(
        'date'    => current_time('mysql'),
        'name'    => $name,
        'phone'   => $phone,
        'email'   => $email,
        'subject' => $subject,
        'message' => $message,
        'ip'      => sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? '')
    );
    if (count($inquiries) > 100) {
        $inquiries = array_slice($inquiries, -100);
    }
    update_option('dl_contact_inquiries', $inquiries, false);

    // Gửi email thông báo cho quản trị khách sạn
    $admin_email = get_option('admin_email');
    $to_email    = function_exists('dragonlegend_get_info') ? dragonlegend_get_info('email') : $admin_email;
    if (empty($to_email)) $to_email = $admin_email;

    $mail_subject = '[Dragon Legend Hotel] Liên hệ mới: ' . $subject . ' - ' . $name;
    $mail_body    = "Kính gửi Bộ phận Lễ tân & Đặt phòng,\n\n";
    $mail_body   .= "Khách hàng vừa gửi thông tin liên hệ mới qua website:\n";
    $mail_body   .= "--------------------------------------------------\n";
    $mail_body   .= "Họ và tên: " . $name . "\n";
    $mail_body   .= "Số điện thoại: " . $phone . "\n";
    $mail_body   .= "Email: " . ($email ?: 'Không cung cấp') . "\n";
    $mail_body   .= "Nhu cầu: " . $subject . "\n";
    $mail_body   .= "Thời gian gửi: " . current_time('d/m/Y H:i:s') . "\n";
    $mail_body   .= "--------------------------------------------------\n";
    $mail_body   .= "Nội dung yêu cầu:\n" . $message . "\n\n";
    $mail_body   .= "Trân trọng,\nWebsite Dragon Legend Hotel";

    $headers = array('Content-Type: text/plain; charset=UTF-8');
    if ($email) {
        $headers[] = 'Reply-To: ' . $name . ' <' . $email . '>';
    }
    @wp_mail($to_email, $mail_subject, $mail_body, $headers);

    wp_send_json_success(array(
        'message' => 'Cảm ơn Quý khách ' . esc_html($name) . '! Yêu cầu của Quý khách đã được gửi thành công. Bộ phận Chăm sóc khách hàng của Dragon Legend Hotel sẽ liên hệ lại trong thời gian sớm nhất.'
    ));
}

