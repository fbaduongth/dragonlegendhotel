<?php
/**
 * Automated WordPress Local Installer
 */

// Define installation flag
define('WP_INSTALLING', true);

// Load WordPress
require_once __DIR__ . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/upgrade.php';
require_once ABSPATH . 'wp-admin/includes/plugin.php';

echo "1. Checking if WordPress is already installed..." . PHP_EOL;

if (is_blog_installed()) {
    echo "WordPress is already installed!" . PHP_EOL;
} else {
    echo "Installing WordPress database tables..." . PHP_EOL;
    $admin_pass = getenv('WP_ADMIN_PASS') ?: 'CHANGE_ME_PASSWORD';
    $result = wp_install(
        'Dragon Legend Hotel Hạ Long',
        'admin',
        'info@dragonlegendhotel.com',
        true,
        '',
        $admin_pass
    );
    echo "WordPress installed successfully!" . PHP_EOL;
}

echo "2. Activating theme: dragonlegend..." . PHP_EOL;
switch_theme('dragonlegend');
echo "Active theme: " . get_stylesheet() . PHP_EOL;

echo "3. Activating plugins: dragonlegend-core & sqlite-database-integration..." . PHP_EOL;
activate_plugin('sqlite-database-integration/load.php');
activate_plugin('dragonlegend-core/dragonlegend-core.php');

$active_plugins = get_option('active_plugins');
print_r($active_plugins);

echo "4. Setting up Homepage (Front Page)..." . PHP_EOL;
// Check if front page already exists
$homepage = get_page_by_path('trang-chu');
if (!$homepage) {
    $homepage_id = wp_insert_post(array(
        'post_title'     => 'Trang Chủ',
        'post_name'      => 'trang-chu',
        'post_status'    => 'publish',
        'post_type'      => 'page',
        'page_template'  => 'front-page.php',
    ));
} else {
    $homepage_id = $homepage->ID;
}

update_option('show_on_front', 'page');
update_option('page_on_front', $homepage_id);
update_option('permalink_structure', '/%postname%/');

echo "Homepage set to Page ID: $homepage_id" . PHP_EOL;

echo "5. Seeding sample hotel data (Rooms, Offers, Amenities, Testimonials)..." . PHP_EOL;
if (class_exists('DragonLegend_Sample_Data')) {
    DragonLegend_Sample_Data::init_sample_data_if_empty();
    echo "Sample data populated successfully!" . PHP_EOL;
} else {
    echo "Notice: DragonLegend_Sample_Data class not found." . PHP_EOL;
}

// Summary
$room_count = wp_count_posts('dl_room')->publish;
$offer_count = wp_count_posts('dl_offer')->publish;
$amenity_count = wp_count_posts('dl_amenity')->publish;
$review_count = wp_count_posts('dl_testimonial')->publish;

echo "=====================================================" . PHP_EOL;
echo "🎉 DEPLOYMENT SUMMARY:" . PHP_EOL;
echo "- Site Title: " . get_bloginfo('name') . PHP_EOL;
echo "- Site URL: " . home_url() . PHP_EOL;
echo "- Theme: " . wp_get_theme()->get('Name') . " v" . wp_get_theme()->get('Version') . PHP_EOL;
echo "- Hạng phòng (Rooms): $room_count phòng" . PHP_EOL;
echo "- Gói ưu đãi (Offers): $offer_count gói" . PHP_EOL;
echo "- Dịch vụ (Amenities): $amenity_count dịch vụ" . PHP_EOL;
echo "- Đánh giá (Testimonials): $review_count đánh giá" . PHP_EOL;
echo "- Admin Account: admin (set via WP_ADMIN_PASS)" . PHP_EOL;
echo "- Login URL: " . wp_login_url() . PHP_EOL;
echo "=====================================================" . PHP_EOL;
