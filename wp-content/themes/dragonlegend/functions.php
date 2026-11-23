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
            if (is_front_page()) {
                $urls[] = array('href' => 'https://cdn.jsdelivr.net', 'crossorigin' => 'anonymous');
            }
        }
        if ('dns-prefetch' === $relation_type) {
            $urls[] = 'https://fonts.googleapis.com';
            $urls[] = 'https://fonts.gstatic.com';
            if (is_front_page()) {
                $urls[] = 'https://cdn.jsdelivr.net';
            }
        }
    }
    return $urls;
}
add_filter('wp_resource_hints', 'dragonlegend_resource_hints', 10, 2);

/**
 * Remove WordPress Frontend Bloat (Emojis, RSD, WLW, Shortlink, oEmbed)
 */
function dragonlegend_cleanup_head() {
    if (is_admin()) {
        return;
    }
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wp_shortlink_wp_head', 10);
    remove_action('wp_head', 'rest_output_link_wp_head', 10);
    remove_action('wp_head', 'wp_oembed_add_discovery_links');
    remove_action('wp_head', 'wp_oembed_add_host_js');
}
add_action('init', 'dragonlegend_cleanup_head');

/**
 * Inline Compiled Tailwind + Theme CSS Bundle in <head> (Zero Render-Blocking CSS, like onsenquanghanh.com)
 */
function dragonlegend_inline_compiled_css() {
    if (is_admin()) {
        return;
    }
    $bundle_path = get_template_directory() . '/assets/css/bundle.min.css';
    $custom_path = get_template_directory() . '/assets/css/custom.css';

    if (file_exists($bundle_path)) {
        echo '<style id="dragonlegend-bundle-inline-css">' . file_get_contents($bundle_path) . '</style>' . "\n";
        // If custom.css was edited after bundle.min.css was built, append custom.css overrides automatically
        if (file_exists($custom_path) && filemtime($custom_path) > filemtime($bundle_path) + 5) {
            echo '<style id="dragonlegend-custom-live-css">' . file_get_contents($custom_path) . '</style>' . "\n";
        }
    }
}
add_action('wp_head', 'dragonlegend_inline_compiled_css', 9);

/**
 * Enqueue Scripts and Styles
 */
function dragonlegend_enqueue_scripts() {
    $theme_version = wp_get_theme()->get('Version');

    // 1. Google Fonts (Loaded non-blockingly via preload filter)
    wp_enqueue_style(
        'dragonlegend-fonts',
        'https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400;1,600;1,700&display=swap',
        array(),
        null
    );

    $is_home = is_front_page();

    // 2. Swiper & AOS (Only needed on Front Page)
    if ($is_home) {
        wp_enqueue_style(
            'swiper-css',
            'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css',
            array(),
            '11.0.0'
        );

        wp_enqueue_style(
            'aos-css',
            'https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css',
            array(),
            '2.3.4',
            '(min-width: 768px)'
        );

        wp_enqueue_script(
            'swiper-js',
            'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js',
            array(),
            '11.0.0',
            true
        );

        wp_enqueue_script(
            'aos-js',
            'https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js',
            array(),
            '2.3.4',
            true
        );
    }

    $custom_css_ver = file_exists(get_template_directory() . '/assets/css/custom.css') ? filemtime(get_template_directory() . '/assets/css/custom.css') : $theme_version;
    $main_js_ver    = file_exists(get_template_directory() . '/assets/js/main.js') ? filemtime(get_template_directory() . '/assets/js/main.js') : $theme_version;
    $booking_js_ver = file_exists(get_template_directory() . '/assets/js/booking.js') ? filemtime(get_template_directory() . '/assets/js/booking.js') : $theme_version;

    // Fallback external CSS only if bundle.min.css is missing
    if (!file_exists(get_template_directory() . '/assets/css/bundle.min.css')) {
        wp_enqueue_style(
            'dragonlegend-style',
            get_stylesheet_uri(),
            array(),
            $theme_version
        );
        wp_enqueue_style(
            'dragonlegend-custom',
            get_template_directory_uri() . '/assets/css/custom.css',
            array('dragonlegend-style'),
            $custom_css_ver
        );
    }

    // 9. Booking Engine Script (100% Vanilla JS - No jQuery dependency)
    wp_enqueue_script(
        'dragonlegend-booking',
        get_template_directory_uri() . '/assets/js/booking.js',
        array(),
        $booking_js_ver,
        true
    );

    // 10. Main Controller Script
    $main_deps = $is_home ? array('swiper-js', 'aos-js', 'dragonlegend-booking') : array('dragonlegend-booking');
    wp_enqueue_script(
        'dragonlegend-main',
        get_template_directory_uri() . '/assets/js/main.js',
        $main_deps,
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
 * Dequeue Unused WP Core Styles & Unused Frontend jQuery (Save ~150KB Render-Blocking Assets)
 */
function dragonlegend_dequeue_unused_assets() {
    if (is_admin()) {
        return;
    }

    // Remove unused Gutenberg / Classic block styles on frontend
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('wc-blocks-style');
    wp_dequeue_style('classic-theme-styles');
    wp_dequeue_style('global-styles');

    // Translator CSS is already compiled into bundle.min.css
    if (file_exists(get_template_directory() . '/assets/css/bundle.min.css')) {
        wp_dequeue_style('dl-translator-css');
    }

    if (!is_user_logged_in()) {
        wp_dequeue_style('dashicons');
        // Theme & plugins on frontend use 100% pure Vanilla JS
        wp_dequeue_script('jquery');
        wp_deregister_script('jquery');
    }
}
add_action('wp_enqueue_scripts', 'dragonlegend_dequeue_unused_assets', 100);

/**
 * Convert External Stylesheets (Google Fonts, Swiper, AOS) to Non-Blocking Preload Links
 */
function dragonlegend_async_styles($html, $handle, $href, $media) {
    if (is_admin()) {
        return $html;
    }
    if ($handle === 'aos-css') {
        return '<link rel="stylesheet" id="aos-css-css" href="' . esc_url($href) . '" media="(min-width: 768px)" />' . "\n";
    }
    $async_handles = array('dragonlegend-fonts', 'swiper-css');
    if (in_array($handle, $async_handles, true)) {
        return '<link rel="preload" as="style" id="' . esc_attr($handle) . '-css" href="' . esc_url($href) . '" onload="this.onload=null;this.rel=\'stylesheet\'" />' . "\n"
             . '<noscript><link rel="stylesheet" href="' . esc_url($href) . '" /></noscript>' . "\n";
    }
    return $html;
}
add_filter('style_loader_tag', 'dragonlegend_async_styles', 10, 4);

/**
 * Add DEFER to Theme Scripts (Third-party scripts are delayed by the Flying Scripts plugin)
 */
function dragonlegend_defer_scripts($tag, $handle, $src) {
    if (is_admin()) {
        return $tag;
    }

    $defer_scripts = array('swiper-js', 'aos-js', 'dragonlegend-booking', 'dragonlegend-main', 'dl-translator-js');
    if (in_array($handle, $defer_scripts, true)) {
        if (strpos($tag, ' defer') === false) {
            return str_replace(' src=', ' defer src=', $tag);
        }
    }
    return $tag;
}
add_filter('script_loader_tag', 'dragonlegend_defer_scripts', 20, 3);

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
        'map_embed'     => 'https://maps.google.com/maps?q=Dragon%20Legend%20H%E1%BA%A1%20Long%20Hotel,%20%C4%90%E1%BA%A1i%20l%E1%BB%99%20H%E1%BA%A1%20Long%20Marine,%20B%C3%A3i%20Ch%C3%A1y,%20H%E1%BA%A1%20Long&ll=20.9582273,107.0201151&t=m&z=16&hl=vi&ie=UTF8&iwloc=&output=embed',
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
            if ($key === 'map_embed') {
                if (preg_match('/src=["\']([^"\']+)["\']/i', $val, $m)) {
                    $val = $m[1];
                }
                if (strpos($val, '!1svi!2s!') !== false || strpos($val, '1769987129773') !== false) {
                    return $defaults['map_embed'];
                }
            }
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
 * Đồng bộ & chuẩn hóa dữ liệu Phân Loại Phòng (dl_room_type) trong Database (chạy 1 lần duy nhất)
 * Loại bỏ các term tiếng Anh trùng lặp (superior, deluxe, suite, family) và gán đúng vào các term chuẩn trong DB
 */
function dl_maybe_normalize_room_type_terms() {
    if (get_option('dl_room_type_terms_normalized_v1') === 'yes') {
        return;
    }
    if (!taxonomy_exists('dl_room_type')) {
        return;
    }

    // 1. Đảm bảo 4 phân loại phòng chuẩn tồn tại và có thứ tự hiển thị hợp lý
    $standard_cats = array(
        array('old_slug' => 'superior', 'slug' => 'phong-superior', 'name' => 'Phòng Superior', 'order' => 10),
        array('old_slug' => 'deluxe',   'slug' => 'phong-deluxe',   'name' => 'Phòng Deluxe',   'order' => 20),
        array('old_slug' => 'suite',    'slug' => 'junior-suite',   'name' => 'Junior Suite',   'order' => 30),
        array('old_slug' => 'family',   'slug' => 'family-suite',   'name' => 'Family Suite',   'order' => 40),
    );

    $canonical_term_ids = array();

    foreach ($standard_cats as $cat) {
        $target_term = get_term_by('slug', $cat['slug'], 'dl_room_type');
        if (!$target_term || is_wp_error($target_term)) {
            $target_term = get_term_by('name', $cat['name'], 'dl_room_type');
        }
        $old_term = get_term_by('slug', $cat['old_slug'], 'dl_room_type');

        if ($target_term && !is_wp_error($target_term)) {
            $target_id = (int) $target_term->term_id;
            update_term_meta($target_id, '_dl_room_type_order', $cat['order']);

            // Nếu tồn tại term tiếng Anh trùng lặp (vd: deluxe, suite, family), chuyển toàn bộ phòng sang term chuẩn rồi xóa term trùng
            if ($old_term && !is_wp_error($old_term) && (int) $old_term->term_id !== $target_id) {
                $old_posts = get_posts(array(
                    'post_type'      => 'dl_room',
                    'posts_per_page' => -1,
                    'post_status'    => 'any',
                    'fields'         => 'ids',
                    'tax_query'      => array(
                        array(
                            'taxonomy' => 'dl_room_type',
                            'field'    => 'term_id',
                            'terms'    => (int) $old_term->term_id,
                        ),
                    ),
                ));
                foreach ($old_posts as $pid) {
                    wp_set_object_terms($pid, array($target_id), 'dl_room_type', true);
                    wp_remove_object_terms($pid, (int) $old_term->term_id, 'dl_room_type');
                }
                wp_delete_term((int) $old_term->term_id, 'dl_room_type');
            }
        } elseif ($old_term && !is_wp_error($old_term)) {
            // Đổi tên và slug của term cũ (vd: superior -> Phòng Superior / phong-superior)
            $target_id = (int) $old_term->term_id;
            wp_update_term($target_id, 'dl_room_type', array(
                'name' => $cat['name'],
                'slug' => $cat['slug'],
            ));
            update_term_meta($target_id, '_dl_room_type_order', $cat['order']);
        } else {
            $inserted = wp_insert_term($cat['name'], 'dl_room_type', array('slug' => $cat['slug']));
            if (!is_wp_error($inserted) && !empty($inserted['term_id'])) {
                $target_id = (int) $inserted['term_id'];
                update_term_meta($target_id, '_dl_room_type_order', $cat['order']);
            } else {
                $target_id = 0;
            }
        }

        if ($target_id) {
            $canonical_term_ids[$cat['old_slug']] = $target_id;
        }
    }

    // 2. Đảm bảo tất cả các phòng hiện có đều được gán đúng Phân Loại Phòng trong Database
    $all_rooms = get_posts(array(
        'post_type'      => 'dl_room',
        'posts_per_page' => -1,
        'post_status'    => 'any',
    ));
    foreach ($all_rooms as $room_post) {
        $existing_terms = wp_get_object_terms($room_post->ID, 'dl_room_type', array('fields' => 'ids'));
        if (empty($existing_terms) || is_wp_error($existing_terms)) {
            $slug  = mb_strtolower($room_post->post_name, 'UTF-8');
            $title = mb_strtolower($room_post->post_title, 'UTF-8');
            $assign_key = '';
            if (strpos($slug, 'superior') !== false || strpos($title, 'superior') !== false) {
                $assign_key = 'superior';
            } elseif (strpos($slug, 'deluxe') !== false || strpos($title, 'deluxe') !== false) {
                $assign_key = 'deluxe';
            } elseif (strpos($slug, 'family') !== false || strpos($title, 'family') !== false) {
                $assign_key = 'family';
            } elseif (strpos($slug, 'junior') !== false || strpos($slug, 'suite') !== false || strpos($title, 'suite') !== false) {
                $assign_key = 'suite';
            }
            if ($assign_key && !empty($canonical_term_ids[$assign_key])) {
                wp_set_object_terms($room_post->ID, array((int) $canonical_term_ids[$assign_key]), 'dl_room_type', false);
            }
        }
    }

    update_option('dl_room_type_terms_normalized_v1', 'yes');
    delete_transient('dl_rooms_frontend_cache_v2');
}
add_action('init', 'dl_maybe_normalize_room_type_terms', 25);

/**
 * Lấy danh sách Loại Phòng (taxonomy dl_room_type) từ Database để hiển thị bộ lọc
 */
function dl_get_room_categories_for_frontend() {
    if (!taxonomy_exists('dl_room_type')) {
        return array();
    }

    $terms = get_terms(array(
        'taxonomy'   => 'dl_room_type',
        'hide_empty' => false,
    ));

    if (is_wp_error($terms) || empty($terms)) {
        return array();
    }

    // Sắp xếp theo _dl_room_type_order (nếu có), sau đó theo term_id tăng dần
    usort($terms, function($a, $b) {
        $order_a = get_term_meta($a->term_id, '_dl_room_type_order', true);
        $order_b = get_term_meta($b->term_id, '_dl_room_type_order', true);
        $val_a   = ($order_a !== '' && $order_a !== false) ? intval($order_a) : (1000 + intval($a->term_id));
        $val_b   = ($order_b !== '' && $order_b !== false) ? intval($order_b) : (1000 + intval($b->term_id));
        if ($val_a === $val_b) {
            return intval($a->term_id) - intval($b->term_id);
        }
        return $val_a - $val_b;
    });

    $categories = array();
    foreach ($terms as $term) {
        $categories[] = array(
            'id'    => (int) $term->term_id,
            'name'  => $term->name,
            'slug'  => $term->slug,
            'count' => (int) $term->count,
        );
    }

    return $categories;
}

/**
 * Return rooms data formatted for frontend JavaScript & Booking Modal (Cached via Transient for Fast TTFB)
 */
function dl_get_rooms_for_frontend() {
    $cached = get_transient('dl_rooms_frontend_cache_v2');
    if (false !== $cached && is_array($cached) && !empty($cached)) {
        return $cached;
    }

    $rooms = array();
    $query = new WP_Query(array(
        'post_type'              => 'dl_room',
        'posts_per_page'         => -1,
        'post_status'            => 'publish',
        'no_found_rows'          => true,
        'update_post_meta_cache' => true,
        'update_post_term_cache' => true,
    ));

    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            $terms = get_the_terms($post_id, 'dl_room_type');
            $cat_slugs = array();
            $cat_names = array();
            if ($terms && !is_wp_error($terms) && !empty($terms)) {
                foreach ($terms as $t) {
                    $cat_slugs[] = $t->slug;
                    $cat_names[] = $t->name;
                }
            }
            $cat_slug = !empty($cat_slugs) ? $cat_slugs[0] : '';
            if (empty($cat_slug)) {
                $slug = get_post_field('post_name', $post_id);
                $title = mb_strtolower(get_the_title($post_id), 'UTF-8');
                if (strpos($slug, 'superior') !== false || strpos($title, 'superior') !== false) {
                    $cat_slug = 'phong-superior';
                } elseif (strpos($slug, 'deluxe') !== false || strpos($title, 'deluxe') !== false) {
                    $cat_slug = 'phong-deluxe';
                } elseif (strpos($slug, 'family') !== false || strpos($title, 'family') !== false) {
                    $cat_slug = 'family-suite';
                } elseif (strpos($slug, 'junior') !== false || strpos($slug, 'suite') !== false || strpos($title, 'suite') !== false) {
                    $cat_slug = 'junior-suite';
                } else {
                    $cat_slug = 'all';
                }
                $cat_slugs = array($cat_slug);
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
                $image_url = 'https://media.dragonlegendhotel.com/2026/02/DBL-1.jpg';
            }

            $amenities_keys = get_post_meta($post_id, '_dl_room_amenities', true);
            if (!is_array($amenities_keys)) $amenities_keys = array();

            $amenities_formatted = array();
            foreach ($amenities_keys as $k) {
                $am_detail = dl_get_room_amenity_detail($k);
                if (!empty($am_detail['label'])) {
                    $amenities_formatted[] = array(
                        'icon' => !empty($am_detail['icon']) ? $am_detail['icon'] : 'sparkles',
                        'text' => $am_detail['label'],
                    );
                }
            }

            $rooms[] = array(
                'id'             => get_post_field('post_name', $post_id),
                'post_id'        => $post_id,
                'category'       => $cat_slug,
                'categories'     => $cat_slugs,
                'category_names' => $cat_names,
                'name'           => html_entity_decode(get_the_title(), ENT_QUOTES, 'UTF-8'),
                'subtitle'       => html_entity_decode(get_post_meta($post_id, '_dl_room_subtitle', true), ENT_QUOTES, 'UTF-8'),
                'image'          => $image_url,
                'gallery'        => !empty($gallery) ? $gallery : array($image_url),
                'price'          => $price ? floatval($price) : 1050000,
                'originalPrice'  => $orig_price ? floatval($orig_price) : 1300000,
                'size'           => get_post_meta($post_id, '_dl_room_size', true) ?: '28 - 32 m²',
                'bed'            => get_post_meta($post_id, '_dl_room_bed', true) ?: '1 Giường đôi King',
                'occupancy'      => get_post_meta($post_id, '_dl_room_occupancy', true) ?: '2 Người lớn + 1 Trẻ em',
                'view'           => get_post_meta($post_id, '_dl_room_view', true) ?: 'Hướng thành phố',
                'badge'          => get_post_meta($post_id, '_dl_room_badge', true) ?: '',
                'rating'         => get_post_meta($post_id, '_dl_room_rating', true) ?: '4.8',
                'reviewsCount'   => intval(get_post_meta($post_id, '_dl_room_reviews_count', true)) ?: 120,
                'description'    => get_the_content(),
                'permalink'      => get_permalink($post_id),
                'amenities'      => $amenities_formatted
            );
        }
        wp_reset_postdata();
    }

    // Fallback nếu database chưa có bài viết nào
    if (empty($rooms)) {
        $rooms = dl_get_default_rooms_fallback();
    }

    set_transient('dl_rooms_frontend_cache_v2', $rooms, HOUR_IN_SECONDS);

    return $rooms;
}

/**
 * Tra cứu thông tin chi tiết 1 tiện nghi phòng (label + icon đại diện) từ Database
 */
function dl_get_room_amenity_detail($key_or_item) {
    if (method_exists('DragonLegend_Metaboxes', 'get_room_amenity_detail')) {
        return DragonLegend_Metaboxes::get_room_amenity_detail($key_or_item);
    }

    $catalog = get_option('dragonlegend_room_amenities_catalog', array());
    $defaults = array(
        'wifi'         => array('key' => 'wifi',         'label' => 'Wifi cáp quang tốc độ cao miễn phí',       'icon' => 'wifi'),
        'coffee'       => array('key' => 'coffee',       'label' => 'Buffet sáng Quốc tế miễn phí',             'icon' => 'coffee'),
        'tv'           => array('key' => 'tv',           'label' => 'Smart TV 50-55 inch 4K',                   'icon' => 'tv'),
        'bath'         => array('key' => 'bath',         'label' => 'Bồn tắm nằm thư giãn & vòi sen đứng',      'icon' => 'bath'),
        'snowflake'    => array('key' => 'snowflake',    'label' => 'Điều hòa 2 chiều êm dịu',                  'icon' => 'snowflake'),
        'wine'         => array('key' => 'wine',         'label' => 'Mini Bar & Rượu vang / Trà cà phê',        'icon' => 'wine'),
        'shield-check' => array('key' => 'shield-check', 'label' => 'Két sắt an toàn điện tử',                  'icon' => 'shield-check'),
        'sparkles'     => array('key' => 'sparkles',     'label' => 'Dịch vụ dọn phòng hàng ngày',              'icon' => 'sparkles'),
        'sun'          => array('key' => 'sun',          'label' => 'Ban công riêng ngắm bình minh / hoàng hôn','icon' => 'sun'),
        'waves'        => array('key' => 'waves',        'label' => 'Miễn phí vé hồ bơi vô cực Sky Pool',       'icon' => 'waves'),
        'gem'          => array('key' => 'gem',          'label' => 'Đặc quyền check-in VIP Lounge',            'icon' => 'gem'),
        'car'          => array('key' => 'car',          'label' => 'Ưu đãi đưa đón sân bay',                   'icon' => 'car'),
    );

    if (is_array($key_or_item)) {
        $k = !empty($key_or_item['key']) ? $key_or_item['key'] : '';
        if ($k && isset($catalog[$k]) && is_array($catalog[$k])) {
            return $catalog[$k];
        }
        return array(
            'key'   => $k ?: 'custom',
            'label' => !empty($key_or_item['label']) ? $key_or_item['label'] : (!empty($key_or_item['text']) ? $key_or_item['text'] : ''),
            'icon'  => !empty($key_or_item['icon']) ? $key_or_item['icon'] : 'sparkles',
        );
    }

    $k = (string) $key_or_item;
    if (is_array($catalog) && isset($catalog[$k])) {
        if (is_array($catalog[$k])) {
            return array(
                'key'   => $k,
                'label' => !empty($catalog[$k]['label']) ? $catalog[$k]['label'] : $k,
                'icon'  => !empty($catalog[$k]['icon']) ? $catalog[$k]['icon'] : $k,
            );
        }
        return array('key' => $k, 'label' => (string) $catalog[$k], 'icon' => $k);
    }

    if (isset($defaults[$k])) {
        return $defaults[$k];
    }

    return array('key' => $k, 'label' => $k, 'icon' => $k);
}

/**
 * Flush rooms frontend cache when a room, room category, or room amenities catalog is updated
 */
function dl_flush_rooms_frontend_cache() {
    delete_transient('dl_rooms_frontend_cache_v2');
}
add_action('save_post_dl_room', 'dl_flush_rooms_frontend_cache');
add_action('deleted_post', 'dl_flush_rooms_frontend_cache');
add_action('trashed_post', 'dl_flush_rooms_frontend_cache');
add_action('created_dl_room_type', 'dl_flush_rooms_frontend_cache');
add_action('edited_dl_room_type', 'dl_flush_rooms_frontend_cache');
add_action('delete_dl_room_type', 'dl_flush_rooms_frontend_cache');
add_action('set_object_terms', function($object_id, $terms, $tt_ids, $taxonomy) {
    if ($taxonomy === 'dl_room_type') {
        dl_flush_rooms_frontend_cache();
    }
}, 10, 4);
add_action('update_option_dragonlegend_room_amenities_catalog', 'dl_flush_rooms_frontend_cache');
add_action('update_option_dragonlegend_room_amenities_title', 'dl_flush_rooms_frontend_cache');

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
            'image'         => 'https://media.dragonlegendhotel.com/2026/02/DBL-1.jpg',
            'gallery'       => array(
                'https://media.dragonlegendhotel.com/2026/02/DBL-1.jpg',
                'https://media.dragonlegendhotel.com/2019/05/Superior-6-e1567590038520.jpg',
                'https://media.dragonlegendhotel.com/2026/02/TWN-2-scaled.jpg'
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
            'image'         => 'https://media.dragonlegendhotel.com/2026/02/TWN-2-scaled.jpg',
            'gallery'       => array(
                'https://media.dragonlegendhotel.com/2026/02/TWN-2-scaled.jpg',
                'https://media.dragonlegendhotel.com/2026/02/DBL-1.jpg',
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
            'image'         => 'https://media.dragonlegendhotel.com/2019/06/juinior-suite2-e1567590383347.jpg',
            'gallery'       => array('https://media.dragonlegendhotel.com/2019/06/juinior-suite2-e1567590383347.jpg'),
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
            'image'         => 'https://media.dragonlegendhotel.com/2019/05/Superior-6-e1567590038520.jpg',
            'gallery'       => array('https://media.dragonlegendhotel.com/2019/05/Superior-6-e1567590038520.jpg'),
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
    // Rút gọn chuỗi ngày mở cửa dài dòng thành "Thứ 2 – Chủ Nhật (24/7)" cho gọn đẹp
    $html = str_replace(
        'Thứ Hai, Thứ Ba, Thứ Tư, Thứ Năm, Thứ Sáu, Thứ Bảy, Chủ Nhật',
        'Thứ 2 – Chủ Nhật',
        $html
    );

    $map_url = function_exists('dragonlegend_get_info') ? dragonlegend_get_info('map_embed') : '';
    if (!empty($map_url) && preg_match('/src=["\']([^"\']+)["\']/i', $map_url, $m)) {
        $map_url = $m[1];
    }
    if (empty($map_url) || strpos($map_url, '!1svi!2s!') !== false || strpos($map_url, '1769987129773') !== false) {
        $map_url = 'https://maps.google.com/maps?q=Dragon%20Legend%20H%E1%BA%A1%20Long%20Hotel,%20%C4%90%E1%BA%A1i%20l%E1%BB%99%20H%E1%BA%A1%20Long%20Marine,%20B%C3%A3i%20Ch%C3%A1y,%20H%E1%BA%A1%20Long&ll=20.9582273,107.0201151&t=m&z=16&hl=vi&ie=UTF8&iwloc=&output=embed';
    }

    $map_html = '<div class="rank-math-contact-section rank-math-contact-map rounded-2xl overflow-hidden shadow-sm border border-slate-200" style="min-height:240px;background:#f1f5f9;">
        <iframe src="' . esc_url($map_url) . '" width="100%" height="240" style="border:0;width:100%;height:240px;min-height:240px;display:block;" allowfullscreen="" referrerpolicy="no-referrer-when-downgrade" title="Google Map Dragon Legend Hotel"></iframe>
    </div>';

    if (preg_match('/<div[^>]*class=["\'][^"\']*rank-math-contact-map[^"\']*["\'][^>]*>.*?<\/div>/is', $html)) {
        $html = preg_replace('/<div[^>]*class=["\'][^"\']*rank-math-contact-map[^"\']*["\'][^>]*>.*?<\/div>/is', $map_html, $html, 1);
    } elseif (strpos($html, '<iframe') === false) {
        if (strpos($html, '</div><div class="clear"></div>') !== false) {
            $html = str_replace('</div><div class="clear"></div>', $map_html . '</div><div class="clear"></div>', $html);
        } elseif (strpos($html, '<div class="clear"></div>') !== false) {
            $html = str_replace('<div class="clear"></div>', $map_html . '<div class="clear"></div>', $html);
        } else {
            $html .= $map_html;
        }
    }
    return $html;
}

/**
 * Xử lý AJAX Form liên hệ (Trang liên hệ /lien-he/)
 * Gửi Email HTML thương hiệu + Thông báo qua Telegram & Zalo
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

    $contact_data = array(
        'date'    => current_time('d/m/Y H:i'),
        'name'    => $name,
        'phone'   => $phone,
        'email'   => $email,
        'subject' => $subject,
        'message' => $message,
        'ip'      => sanitize_text_field($_SERVER['REMOTE_ADDR'] ?? '')
    );

    // 1. Lưu vào database options để quản trị viên xem lại tại: Đơn đặt phòng -> Tin nhắn Liên hệ
    $inquiries = get_option('dl_contact_inquiries', array());
    if (!is_array($inquiries)) $inquiries = array();
    $inquiries[] = $contact_data;
    if (count($inquiries) > 200) {
        $inquiries = array_slice($inquiries, -200);
    }
    update_option('dl_contact_inquiries', $inquiries, false);

    // 2. Tập hợp danh sách Email Quản trị nhận thông báo (Từ Cấu hình Đặt phòng/Liên hệ + Email khách sạn + Admin Email)
    $recipient_emails = array();
    $email_enabled = true;
    $customer_email_enabled = true;

    if (class_exists('DragonLegend_Zalo_Service')) {
        $notif_settings = DragonLegend_Zalo_Service::get_settings();
        $email_enabled = !isset($notif_settings['email_enabled']) || $notif_settings['email_enabled'] === '1';
        $customer_email_enabled = !isset($notif_settings['customer_email_enabled']) || $notif_settings['customer_email_enabled'] === '1';

        if (!empty($notif_settings['admin_notification_emails'])) {
            foreach (explode(',', $notif_settings['admin_notification_emails']) as $em) {
                $em = trim($em);
                if (is_email($em)) {
                    $recipient_emails[] = $em;
                }
            }
        }
    }

    $hotel_info_email = function_exists('dragonlegend_get_info') ? trim(dragonlegend_get_info('email')) : '';
    if ($hotel_info_email && is_email($hotel_info_email)) {
        $recipient_emails[] = $hotel_info_email;
    }

    if (empty($recipient_emails)) {
        $admin_em = get_option('admin_email');
        if (is_email($admin_em)) {
            $recipient_emails[] = $admin_em;
        }
    }
    $recipient_emails = array_values(array_unique($recipient_emails));

    // 3. Gửi Email HTML cho Quản trị viên & Khách hàng
    if ($email_enabled && !empty($recipient_emails)) {
        $hotel_name    = get_bloginfo('name') ?: 'Dragon Legend Hotel';
        $hotel_phone   = function_exists('dragonlegend_get_info') ? dragonlegend_get_info('hotline') : '(+84) 203 3518 999';
        $hotel_address = function_exists('dragonlegend_get_info') ? dragonlegend_get_info('address') : 'Đại lộ Hạ Long Marine, Bãi Cháy, TP. Hạ Long';
        $logo_url      = 'https://media.dragonlegendhotel.com/2015/07/logoweb-1.png';
        $zalo_phone    = preg_replace('/[^0-9]/', '', $phone);
        if (strpos($zalo_phone, '0') === 0) {
            $zalo_phone = '84' . substr($zalo_phone, 1);
        }
        $admin_inq_url = admin_url('edit.php?post_type=dl_booking&page=dl-contact-inquiries');

        $mail_subject = '[' . $hotel_name . '] Liên hệ & Tư vấn mới: ' . $subject . ' - ' . $name;

        $html_body = '<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"><title>Liên hệ mới từ website</title></head>
<body style="margin:0;padding:25px 15px;background-color:#f1f5f9;font-family:\'Helvetica Neue\',Helvetica,Arial,sans-serif;color:#334155;line-height:1.6;">
  <div style="max-width:640px;margin:0 auto;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e2e8f0;box-shadow:0 10px 25px rgba(0,0,0,0.06);">
    <div style="background:linear-gradient(135deg,#07111e 0%,#0d1e33 100%);padding:28px;text-align:center;border-bottom:3px solid #c5a880;">
      <img src="' . esc_url($logo_url) . '" alt="' . esc_attr($hotel_name) . '" style="max-height:46px;width:auto;margin-bottom:10px;">
      <span style="display:block;font-size:11px;text-transform:uppercase;letter-spacing:2px;color:#c5a880;font-weight:700;">HỆ THỐNG CHĂM SÓC KHÁCH HÀNG</span>
      <h1 style="color:#ffffff;font-size:20px;font-weight:700;margin:6px 0 0 0;">YÊU CẦU LIÊN HỆ & TƯ VẤN MỚI</h1>
    </div>
    <div style="padding:28px;">
      <p style="margin-top:0;font-size:14px;color:#475569;">Kính gửi Bộ phận Lễ tân & Kinh doanh <strong>' . esc_html($hotel_name) . '</strong>,<br>Khách hàng vừa gửi một yêu cầu liên hệ trực tiếp từ trang Liên hệ trên website:</p>
      <table width="100%" cellspacing="0" cellpadding="10" style="font-size:13.5px;border:1px solid #e2e8f0;border-radius:10px;margin:18px 0;">
        <tr style="background:#f8fafc;">
          <td width="35%" style="color:#64748b;font-weight:600;">Họ và tên khách:</td>
          <td style="color:#0f172a;font-weight:700;font-size:15px;">' . esc_html($name) . '</td>
        </tr>
        <tr>
          <td style="color:#64748b;font-weight:600;">Số điện thoại:</td>
          <td>
            <a href="tel:' . esc_attr($phone) . '" style="color:#0284c7;font-weight:700;text-decoration:none;">' . esc_html($phone) . '</a>
            <a href="https://zalo.me/' . esc_attr($zalo_phone) . '" target="_blank" style="margin-left:10px;display:inline-block;padding:2px 8px;background:#0068ff;color:#fff;border-radius:4px;font-size:11px;text-decoration:none;font-weight:600;">Chat Zalo</a>
          </td>
        </tr>
        <tr style="background:#f8fafc;">
          <td style="color:#64748b;font-weight:600;">Địa chỉ Email:</td>
          <td>' . (!empty($email) ? '<a href="mailto:' . esc_attr($email) . '" style="color:#0284c7;text-decoration:none;">' . esc_html($email) . '</a>' : '<span style="color:#94a3b8;font-style:italic;">Không cung cấp</span>') . '</td>
        </tr>
        <tr>
          <td style="color:#64748b;font-weight:600;">Chủ đề / Nhu cầu:</td>
          <td><strong style="color:#b45309;">' . esc_html($subject) . '</strong></td>
        </tr>
        <tr style="background:#f8fafc;">
          <td style="color:#64748b;font-weight:600;vertical-align:top;">Nội dung chi tiết:</td>
          <td style="color:#1e293b;white-space:pre-line;">' . esc_html($message) . '</td>
        </tr>
        <tr>
          <td style="color:#64748b;font-weight:600;">Thời gian gửi:</td>
          <td style="color:#64748b;">' . esc_html(current_time('H:i:s - d/m/Y')) . '</td>
        </tr>
      </table>
      <div style="text-align:center;margin:26px 0 8px 0;">
        <a href="' . esc_url($admin_inq_url) . '" target="_blank" style="display:inline-block;background:linear-gradient(135deg,#c5a880 0%,#b59265 100%);color:#07111e;font-weight:700;font-size:13px;text-transform:uppercase;letter-spacing:1px;padding:12px 26px;text-decoration:none;border-radius:30px;">Xem Danh Sách Liên Hệ Trong WP-Admin &rarr;</a>
      </div>
    </div>
    <div style="background:#f8fafc;padding:16px 28px;border-top:1px solid #e2e8f0;text-align:center;font-size:12px;color:#64748b;">
      <strong>' . esc_html($hotel_name) . '</strong> — ' . esc_html($hotel_address) . ' | Hotline: ' . esc_html($hotel_phone) . '
    </div>
  </div>
</body>
</html>';

        $headers = array('Content-Type: text/html; charset=UTF-8');
        if (!empty($email) && is_email($email)) {
            $headers[] = 'Reply-To: ' . $name . ' <' . $email . '>';
        }
        @wp_mail($recipient_emails, $mail_subject, $html_body, $headers);

        // Gửi thư xác nhận tự động cho khách hàng nếu khách có nhập email
        if ($customer_email_enabled && !empty($email) && is_email($email)) {
            $guest_subject = '[' . $hotel_name . '] Xác nhận đã tiếp nhận yêu cầu tư vấn của Quý khách';
            $guest_body = '<!DOCTYPE html>
<html lang="vi">
<head><meta charset="UTF-8"></head>
<body style="margin:0;padding:25px 15px;background-color:#f1f5f9;font-family:\'Helvetica Neue\',Helvetica,Arial,sans-serif;color:#334155;line-height:1.6;">
  <div style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e2e8f0;">
    <div style="background:linear-gradient(135deg,#07111e 0%,#0d1e33 100%);padding:26px;text-align:center;border-bottom:3px solid #c5a880;">
      <img src="' . esc_url($logo_url) . '" alt="' . esc_attr($hotel_name) . '" style="max-height:44px;width:auto;margin-bottom:8px;">
      <h2 style="color:#ffffff;font-size:18px;margin:0;">CẢM ƠN QUÝ KHÁCH ĐÃ LIÊN HỆ</h2>
    </div>
    <div style="padding:26px;">
      <p style="margin-top:0;">Kính chào Quý khách <strong>' . esc_html($name) . '</strong>,</p>
      <p><strong>' . esc_html($hotel_name) . '</strong> xin trân trọng cảm ơn Quý khách đã quan tâm đến dịch vụ của khách sạn. Chúng tôi đã nhận được yêu cầu của Quý khách về chủ đề <strong>"' . esc_html($subject) . '"</strong>.</p>
      <div style="background:#f8fafc;border-left:4px solid #c5a880;padding:14px 18px;border-radius:6px;margin:18px 0;font-size:13.5px;">
        <strong>Nội dung Quý khách đã gửi:</strong><br>' . nl2br(esc_html($message)) . '
      </div>
      <p>Bộ phận Chăm sóc khách hàng & Lễ tân sẽ liên hệ lại với Quý khách qua số điện thoại <strong>' . esc_html($phone) . '</strong> trong thời gian sớm nhất.</p>
      <p style="margin-bottom:0;font-size:13px;color:#64748b;">Nếu cần hỗ trợ ngay lập tức, Quý khách vui lòng liên hệ Hotline 24/7: <strong style="color:#0284c7;">' . esc_html($hotel_phone) . '</strong>.</p>
    </div>
    <div style="background:#f8fafc;padding:14px;border-top:1px solid #e2e8f0;text-align:center;font-size:11px;color:#94a3b8;">
      ' . esc_html($hotel_name) . ' — ' . esc_html($hotel_address) . '
    </div>
  </div>
</body>
</html>';
            @wp_mail($email, $guest_subject, $guest_body, array('Content-Type: text/html; charset=UTF-8'));
        }
    }

    // 4. Gửi thông báo qua Telegram & Zalo (nếu được kích hoạt trong Cấu hình Đặt phòng & Liên hệ)
    if (class_exists('DragonLegend_Zalo_Service')) {
        DragonLegend_Zalo_Service::send_contact_notifications($contact_data);
    }

    wp_send_json_success(array(
        'message' => 'Cảm ơn Quý khách ' . esc_html($name) . '! Yêu cầu của Quý khách đã được gửi thành công. Bộ phận Chăm sóc khách hàng của Dragon Legend Hotel sẽ liên hệ lại trong thời gian sớm nhất.'
    ));
}

/**
 * --------------------------------------------------------------------------
 * 19. TỰ ĐỘNG TẠO & CẤU HÌNH MỤC LỤC BÀI VIẾT (OUTLINE / TABLE OF CONTENTS)
 * --------------------------------------------------------------------------
 */

function dl_get_outline_settings() {
    $defaults = array(
        'enabled'        => '1',
        'title'          => 'Mục lục bài viết (Outline)',
        'heading_levels' => '23', // '2', '23', '234'
        'min_headings'   => 2,
        'default_state'  => 'open', // 'open' hoặc 'closed'
        'position'       => 'before_first_h2', // 'before_first_h2' hoặc 'top'
        'show_numbering' => '1',
    );
    $saved = get_option('dl_post_outline_settings', array());
    if (!is_array($saved)) {
        $saved = array();
    }
    return wp_parse_args($saved, $defaults);
}

/**
 * Đăng ký trang Cấu hình Mục lục bài viết tại: WP-Admin -> Cài đặt -> Mục lục bài viết (Outline)
 */
add_action('admin_menu', 'dl_register_outline_settings_menu');
function dl_register_outline_settings_menu() {
    add_options_page(
        'Cấu hình Mục lục bài viết (Outline)',
        'Mục lục bài viết',
        'manage_options',
        'dl-outline-settings',
        'dl_render_outline_settings_page'
    );
}

function dl_render_outline_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_POST['dl_save_outline_settings']) && check_admin_referer('dl_outline_settings_nonce')) {
        $new_settings = array(
            'enabled'        => isset($_POST['enabled']) ? '1' : '0',
            'title'          => sanitize_text_field($_POST['title'] ?? 'Mục lục bài viết (Outline)'),
            'heading_levels' => in_array($_POST['heading_levels'] ?? '23', array('2', '23', '234'), true) ? $_POST['heading_levels'] : '23',
            'min_headings'   => max(1, min(10, intval($_POST['min_headings'] ?? 2))),
            'default_state'  => (($_POST['default_state'] ?? 'open') === 'closed') ? 'closed' : 'open',
            'position'       => (($_POST['position'] ?? 'before_first_h2') === 'top') ? 'top' : 'before_first_h2',
            'show_numbering' => isset($_POST['show_numbering']) ? '1' : '0',
        );
        update_option('dl_post_outline_settings', $new_settings);
        echo '<div class="notice notice-success is-dismissible"><p><strong>Đã lưu cấu hình Mục lục bài viết (Outline) thành công!</strong></p></div>';
    }

    $s = dl_get_outline_settings();
    ?>
    <div class="wrap">
        <h1 style="display:flex;align-items:center;gap:8px;margin-bottom:16px;">
            <span class="dashicons dashicons-editor-ul" style="font-size:24px;width:24px;height:24px;color:#b45309;"></span>
            <span>Cấu hình Mục lục bài viết (Outline / Table of Contents)</span>
        </h1>
        <p style="color:#475569;max-width:760px;">
            Hệ thống tự động quét các thẻ tiêu đề (<code>&lt;h2&gt;</code>, <code>&lt;h3&gt;</code>...) trong nội dung bài viết để tạo khung <strong>Mục lục bài viết (Outline)</strong>. Bạn có thể bật/tắt toàn bộ tại đây hoặc bật/tắt riêng cho từng bài viết ở khung bên phải trình soạn thảo bài viết.
        </p>

        <form method="post" action="" style="background:#fff;padding:24px 28px;border-radius:12px;border:1px solid #cbd5e1;max-width:760px;margin-top:16px;box-shadow:0 2px 8px rgba(0,0,0,0.03);">
            <?php wp_nonce_field('dl_outline_settings_nonce'); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">Trạng thái hoạt động</th>
                    <td>
                        <label style="font-weight:600;color:#0f172a;">
                            <input type="checkbox" name="enabled" value="1" <?php checked($s['enabled'], '1'); ?>>
                            Bật tự động hiển thị Mục lục bài viết (Outline)
                        </label>
                        <p class="description">Nếu bỏ chọn, khung mục lục sẽ được ẩn trên toàn bộ bài viết (trừ bài viết được bật riêng).</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="dl_outline_title">Tiêu đề khung Mục lục</label></th>
                    <td>
                        <input type="text" id="dl_outline_title" name="title" value="<?php echo esc_attr($s['title']); ?>" class="regular-text" style="width:100%;max-width:420px;">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="dl_outline_levels">Các thẻ Heading đưa vào Mục lục</label></th>
                    <td>
                        <select id="dl_outline_levels" name="heading_levels">
                            <option value="2" <?php selected($s['heading_levels'], '2'); ?>>Chỉ thẻ H2</option>
                            <option value="23" <?php selected($s['heading_levels'], '23'); ?>>Thẻ H2 và H3 (Khuyên dùng)</option>
                            <option value="234" <?php selected($s['heading_levels'], '234'); ?>>Thẻ H2, H3 và H4</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="dl_outline_min">Số lượng Heading tối thiểu</label></th>
                    <td>
                        <input type="number" id="dl_outline_min" name="min_headings" value="<?php echo esc_attr($s['min_headings']); ?>" min="1" max="10" class="small-text"> mục trở lên mới hiển thị Mục lục
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="dl_outline_state">Trạng thái mặc định</label></th>
                    <td>
                        <select id="dl_outline_state" name="default_state">
                            <option value="open" <?php selected($s['default_state'], 'open'); ?>>Mở sẵn danh sách mục lục</option>
                            <option value="closed" <?php selected($s['default_state'], 'closed'); ?>>Thu gọn sẵn (Bấm vào mới mở)</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="dl_outline_pos">Vị trí hiển thị trong bài</label></th>
                    <td>
                        <select id="dl_outline_pos" name="position">
                            <option value="before_first_h2" <?php selected($s['position'], 'before_first_h2'); ?>>Ngay trước thẻ H2 đầu tiên (Sau đoạn mở đầu)</option>
                            <option value="top" <?php selected($s['position'], 'top'); ?>>Đầu bài viết (Trên cùng nội dung)</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Đánh số thứ tự tự động</th>
                    <td>
                        <label>
                            <input type="checkbox" name="show_numbering" value="1" <?php checked($s['show_numbering'], '1'); ?>>
                            Hiển thị số thứ tự (1., 1.1., 2...) trước các mục chưa có số
                        </label>
                    </td>
                </tr>
            </table>
            <p class="submit" style="margin-bottom:0;">
                <button type="submit" name="dl_save_outline_settings" class="button button-primary button-large">Lưu cấu hình Mục lục</button>
            </p>
        </form>
    </div>
    <?php
}

/**
 * Metabox Bật / Tắt Mục lục riêng cho từng bài viết trong trình soạn thảo (Post Editor)
 */
add_action('add_meta_boxes', function() {
    add_meta_box(
        'dl_post_outline_metabox',
        'Mục lục bài viết (Outline)',
        'dl_render_post_outline_metabox',
        array('post', 'dl_offer'),
        'side',
        'default'
    );
});

function dl_render_post_outline_metabox($post) {
    wp_nonce_field('dl_post_outline_meta_nonce', 'dl_post_outline_meta_nonce_field');
    $mode = get_post_meta($post->ID, '_dl_outline_mode', true) ?: 'default';
    ?>
    <p style="margin:6px 0 8px;font-size:12px;color:#475569;">Tùy chọn hiển thị khung Mục lục (Outline) cho bài viết này:</p>
    <select name="dl_outline_mode" style="width:100%;">
        <option value="default" <?php selected($mode, 'default'); ?>>Theo cài đặt chung (Mặc định)</option>
        <option value="enable" <?php selected($mode, 'enable'); ?>>Luôn BẬT ở bài viết này</option>
        <option value="disable" <?php selected($mode, 'disable'); ?>>TẮT ở bài viết này</option>
    </select>
    <p style="margin:8px 0 0;font-size:11px;color:#64748b;">
        Cấu hình chung tại: <a href="<?php echo esc_url(admin_url('options-general.php?page=dl-outline-settings')); ?>" target="_blank">Cài đặt &rarr; Mục lục bài viết</a>
    </p>
    <?php
}

add_action('save_post_post', function($post_id) {
    if (!isset($_POST['dl_post_outline_meta_nonce_field']) || !wp_verify_nonce($_POST['dl_post_outline_meta_nonce_field'], 'dl_post_outline_meta_nonce')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (isset($_POST['dl_outline_mode'])) {
        $mode = in_array($_POST['dl_outline_mode'], array('default', 'enable', 'disable'), true) ? $_POST['dl_outline_mode'] : 'default';
        update_post_meta($post_id, '_dl_outline_mode', $mode);
    }
});

function dl_generate_heading_slug($text, &$used_slugs = array()) {
    $clean = wp_strip_all_tags(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
    $slug  = sanitize_title($clean);
    if (empty($slug)) {
        $slug = 'muc-noi-dung';
    }
    $base_slug = $slug;
    $counter   = 2;
    while (in_array($slug, $used_slugs, true)) {
        $slug = $base_slug . '-' . $counter;
        $counter++;
    }
    $used_slugs[] = $slug;
    return $slug;
}

function dl_render_post_outline($content) {
    if (is_admin() || !is_singular(array('post', 'dl_offer')) || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    // Tránh chạy trùng nếu đã có dl-post-outline
    if (strpos($content, 'dl-post-outline') !== false) {
        return $content;
    }

    $settings  = dl_get_outline_settings();
    $post_id   = get_the_ID();
    $post_mode = get_post_meta($post_id, '_dl_outline_mode', true) ?: 'default';

    if ($post_mode === 'disable') {
        return $content;
    }
    if ($post_mode === 'default' && $settings['enabled'] !== '1') {
        return $content;
    }

    $levels_pattern = preg_replace('/[^234]/', '', $settings['heading_levels'] ?: '23');
    if (empty($levels_pattern)) {
        $levels_pattern = '23';
    }

    // Tìm tất cả các thẻ Heading được cấu hình (H2, H3, H4) trong nội dung bài viết
    if (!preg_match_all('/<h([' . $levels_pattern . '])([^>]*)>(.*?)<\/h\1>/is', $content, $matches, PREG_SET_ORDER)) {
        return $content;
    }

    $min_headings = max(1, (int) $settings['min_headings']);
    if (count($matches) < $min_headings) {
        return $content;
    }

    $used_slugs    = array();
    $outline_items = array();
    $h2_index      = 0;
    $h3_index      = 0;
    $show_num      = ($settings['show_numbering'] === '1');

    foreach ($matches as $match) {
        $full_tag   = $match[0];
        $level      = (int) $match[1];
        $attrs      = $match[2];
        $inner_html = $match[3];
        $plain_text = trim(wp_strip_all_tags(html_entity_decode($inner_html, ENT_QUOTES, 'UTF-8')));

        if (empty($plain_text)) {
            continue;
        }

        // Kiểm tra xem thẻ H đã có id chưa
        if (preg_match('/\bid=[\'"]([^\'"]+)[\'"]/i', $attrs, $id_match)) {
            $anchor_id = $id_match[1];
            $used_slugs[] = $anchor_id;
        } else {
            $anchor_id = dl_generate_heading_slug($plain_text, $used_slugs);
            $new_tag   = '<h' . $level . $attrs . ' id="' . esc_attr($anchor_id) . '">' . $inner_html . '</h' . $level . '>';
            // Thay thế lần xuất hiện đầu tiên của thẻ H chưa có id
            $pos = strpos($content, $full_tag);
            if ($pos !== false) {
                $content = substr_replace($content, $new_tag, $pos, strlen($full_tag));
            }
        }

        if ($level === 2) {
            $h2_index++;
            $h3_index = 0;
            $number_label = $h2_index . '.';
        } else {
            $h3_index++;
            $number_label = ($h2_index > 0 ? $h2_index . '.' . $h3_index : $h3_index . '.');
        }

        // Nếu tiêu đề đã bắt đầu bằng số thứ tự (VD: "1. ...", "2.1. ...") thì không lặp lại số thứ tự
        $display_text = $plain_text;
        $has_own_num  = preg_match('/^\d+(\.\d+)?[\.\:\)\s]/u', $display_text);

        $outline_items[] = array(
            'level'       => $level,
            'id'          => $anchor_id,
            'text'        => $display_text,
            'num'         => ($show_num && !$has_own_num) ? $number_label : '',
        );
    }

    if (count($outline_items) < $min_headings) {
        return $content;
    }

    $open_attr     = ($settings['default_state'] === 'closed') ? '' : ' open';
    $outline_title = !empty($settings['title']) ? $settings['title'] : 'Mục lục bài viết (Outline)';

    // Xây dựng HTML cho khung Mục lục bài viết (Outline)
    $outline_html  = '<details' . $open_attr . ' class="dl-post-outline my-6 rounded-2xl border border-amber-200/80 bg-[#fdfaf1] shadow-sm overflow-hidden">';
    $outline_html .= '<summary class="dl-post-outline-header flex items-center justify-between px-5 py-3.5 bg-gradient-to-r from-[#320908] to-[#48100f] text-white cursor-pointer select-none">';
    $outline_html .= '<span class="flex items-center gap-2.5 font-display font-bold text-sm sm:text-base tracking-wide text-[#faecc6]">';
    $outline_html .= '<svg class="w-5 h-5 text-[#dfa843] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>';
    $outline_html .= '<span>' . esc_html($outline_title) . '</span>';
    $outline_html .= '<span class="ml-1 px-2 py-0.5 text-[11px] rounded-full bg-[#dfa843]/20 text-[#dfa843] border border-[#dfa843]/40 font-semibold">' . count($outline_items) . ' mục</span>';
    $outline_html .= '</span>';
    $outline_html .= '<span class="dl-outline-toggle-label text-xs text-[#dfa843] font-medium flex items-center gap-1">Thu gọn / Mở rộng <svg class="w-4 h-4 transition-transform duration-200 dl-outline-chevron" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></span>';
    $outline_html .= '</summary>';
    $outline_html .= '<nav aria-label="' . esc_attr($outline_title) . '" class="p-5 sm:p-6">';
    $outline_html .= '<ul class="dl-post-outline-list space-y-2 m-0 p-0 list-none">';

    foreach ($outline_items as $item) {
        $is_sub = ($item['level'] >= 3);
        $li_class = $is_sub
            ? 'dl-outline-item dl-outline-h3 pl-5 text-sm text-slate-600'
            : 'dl-outline-item dl-outline-h2 font-semibold text-sm sm:text-base text-[#320908]';

        $outline_html .= '<li class="' . $li_class . '">';
        $outline_html .= '<a href="#' . esc_attr($item['id']) . '" class="dl-outline-link inline-flex items-baseline gap-2 hover:text-[#dfa843] transition-colors no-underline">';
        if (!empty($item['num'])) {
            $outline_html .= '<span class="text-[#9b671a] font-bold shrink-0">' . esc_html($item['num']) . '</span>';
        } elseif ($is_sub) {
            $outline_html .= '<span class="text-[#dfa843] shrink-0">↳</span>';
        }
        $outline_html .= '<span>' . esc_html($item['text']) . '</span>';
        $outline_html .= '</a>';
        $outline_html .= '</li>';
    }

    $outline_html .= '</ul>';
    $outline_html .= '</nav>';
    $outline_html .= '</details>';

    // Chèn Outline theo vị trí được cấu hình
    if ($settings['position'] === 'top') {
        $content = $outline_html . $content;
    } else {
        $first_h2_pos = stripos($content, '<h2');
        if ($first_h2_pos !== false && $first_h2_pos > 0) {
            $content = substr_replace($content, $outline_html, $first_h2_pos, 0);
        } else {
            $content = $outline_html . $content;
        }
    }

    return $content;
}
add_filter('the_content', 'dl_render_post_outline', 20);

/**
 * --------------------------------------------------------------------------
 * 20. HỆ THỐNG PHÂN TRANG ĐỒNG BỘ CHO TOÀN BỘ ARCHIVE TEMPLATES
 * --------------------------------------------------------------------------
 */

/**
 * Đồng bộ số lượng bài viết mỗi trang (posts_per_page) trên các trang Archive
 * để chia hết cho lưới 3 cột (9 bài/trang) hoặc 2 cột (6 tin tuyển dụng/trang)
 */
function dragonlegend_optimize_archive_queries($query) {
    if (is_admin() || !$query->is_main_query()) {
        return;
    }

    if ($query->is_category() || $query->is_tag() || $query->is_home() || $query->is_post_type_archive('dl_offer') || $query->is_post_type_archive('dl_room') || $query->is_tax('dl_room_type')) {
        $query->set('posts_per_page', 9);
    } elseif ($query->is_post_type_archive('dl_job') || $query->is_tax('dl_job_department')) {
        $query->set('posts_per_page', 6);
    }
}
add_action('pre_get_posts', 'dragonlegend_optimize_archive_queries');

/**
 * Tự động khôi phục Rewrite Rules cho Sitemap & LLMs.txt (Rank Math) ở cuối chu kỳ init (priority 999)
 * nếu bị plugin khác (như AIOS, UpdraftPlus, WP-Optimize) gọi flush_rewrite_rules() quá sớm ở init priority 0.
 */
function dragonlegend_ensure_seo_rewrite_rules() {
    if (!class_exists('RankMath')) {
        return;
    }
    $rules = get_option('rewrite_rules');
    if (!is_array($rules) || !isset($rules['sitemap_index\.xml$']) || (!isset($rules['^llms\.txt$']) && !isset($rules['llms\.txt$']))) {
        flush_rewrite_rules(false);
    }
}
add_action('init', 'dragonlegend_ensure_seo_rewrite_rules', 999);

/**
 * Làm sạch template mặc định của the_posts_pagination() nếu được gọi trực tiếp
 */
add_filter('navigation_markup_template', function($template, $class) {
    return '<nav class="navigation pagination dl-pagination-nav %1$s" aria-label="%4$s"><div class="nav-links dl-pagination-list">%3$s</div></nav>';
}, 10, 2);

/**
 * Hàm hiển thị thanh phân trang chuẩn Luxury cho mọi trang Archive (Category, Tag, Room, Offer, Job...)
 *
 * @param WP_Query|null $custom_query Truyền WP_Query tùy chỉnh hoặc để null để dùng $wp_query chính.
 */
function dragonlegend_render_pagination($custom_query = null) {
    global $wp_query;

    $query = ($custom_query instanceof WP_Query) ? $custom_query : $wp_query;
    if (!$query) {
        return;
    }

    $total_pages = isset($query->max_num_pages) ? (int) $query->max_num_pages : 1;
    if ($total_pages <= 1) {
        return;
    }

    $current_page = max(1, (int) get_query_var('paged'), (int) get_query_var('page'));

    $links = paginate_links(array(
        'current'   => $current_page,
        'total'     => $total_pages,
        'mid_size'  => 2,
        'end_size'  => 1,
        'prev_next' => true,
        'prev_text' => '<span class="inline-flex items-center gap-1.5"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg><span>Trước</span></span>',
        'next_text' => '<span class="inline-flex items-center gap-1.5"><span>Sau</span><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>',
        'type'      => 'array',
    ));

    if (empty($links) || !is_array($links)) {
        return;
    }

    echo '<nav class="dl-pagination-nav mt-12 pt-8 border-t border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-4" aria-label="Phân trang danh sách">';
    echo '<div class="text-xs font-semibold text-slate-500 uppercase tracking-wider">';
    echo 'Trang <span class="text-navy-deep font-bold">' . esc_html($current_page) . '</span> / <span class="text-navy-deep font-bold">' . esc_html($total_pages) . '</span>';
    echo '</div>';
    echo '<div class="dl-pagination-list flex flex-wrap items-center justify-center gap-2">';
    foreach ($links as $link) {
        echo $link;
    }
    echo '</div>';
    echo '</nav>';
}



