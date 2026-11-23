<?php
/**
 * Dragon Legend Content Manager - Settings Page Template
 *
 * @package DragonLegendContentManager
 */

if (!defined('ABSPATH')) {
    exit;
}

// Lấy dữ liệu hiện tại
$data = get_option(DragonLegend_Content_Manager::OPTION_KEY, array());
$defaults = DragonLegend_Content_Manager::get_defaults();

function dl_val($key, $data, $defaults) {
    if (isset($data[$key]) && $data[$key] !== '') {
        return esc_attr($data[$key]);
    }
    return isset($defaults[$key]) ? esc_attr($defaults[$key]) : '';
}

function dl_val_html($key, $data, $defaults) {
    if (isset($data[$key]) && $data[$key] !== '') {
        return esc_textarea($data[$key]);
    }
    return isset($defaults[$key]) ? esc_textarea($defaults[$key]) : '';
}

// Danh mục biểu tượng SVG hỗ trợ cho Tiện Nghi Nổi Bật
$amenity_icons = array(
    'shuttle'      => 'Xe đưa đón sân bay (shuttle)',
    'no-smoking'   => 'Phòng không hút thuốc (no-smoking)',
    'wifi'         => 'WiFi tốc độ cao (wifi)',
    'concierge'    => 'Dịch vụ phòng (concierge)',
    'family'       => 'Phòng gia đình (family)',
    'restaurant'   => 'Nhà hàng ẩm thực (restaurant)',
    'reception'    => 'Lễ tân phục vụ 24h (reception)',
    'elevator'     => 'Thang máy hiện đại (elevator)',
    'snowflake'    => 'Điều hòa nhiệt độ 2 chiều (snowflake)',
    'coffee'       => 'Bữa sáng buffet (coffee)',
    'pool'         => 'Hồ bơi vô cực / trong nhà (pool)',
    'parking'      => 'Bãi đỗ xe ô tô (parking)',
    'gym'          => 'Phòng tập Gym & Yoga (gym)',
    'spa'          => 'Spa & Thư giãn chuyên sâu (spa)',
    'conference'   => 'Sảnh tiệc & Hội nghị (conference)',
    'users'        => 'Hội nghị & Sự kiện (users)',
    'tv'           => 'Tivi màn hình phẳng (tv)',
    'bath'         => 'Bồn tắm cao cấp (bath)',
    'wine'         => 'Minibar / Rượu vang (wine)',
    'shield-check' => 'An ninh 24/7 (shield-check)',
    'sparkles'     => 'Tiện ích sang trọng (sparkles)',
    'sun'          => 'View biển / Ban công (sun)',
    'waves'        => 'Gần bãi tắm biển (waves)',
    'car'          => 'Đưa đón / Gara (car)',
    'gem'          => 'Trải nghiệm hoàng gia (gem)',
    'tennis'       => 'Sân Tennis & Thể thao (tennis)',
    'kids'         => 'Khu vui chơi trẻ em (kids)',
    'laundry'      => 'Giặt ủi & Giặt là (laundry)',
    'billiards'    => 'Bàn Bida & Giải trí (billiards)',
    'cocktail'     => 'Sky Bar & Cocktail (cocktail)',
    'bicycle'      => 'Xe đạp tham quan (bicycle)',
    'charging'     => 'Trạm sạc xe điện EV (charging)',
    'luggage'      => 'Giữ hành lý miễn phí (luggage)',
    'money'        => 'Thu đổi ngoại tệ (money)',
);
?>

<div class="wrap dl-admin-wrap">
    
    <!-- Header Banner -->
    <div class="dl-admin-header">
        <div>
            <h1>Quản Lý Nội Dung Tĩnh Website</h1>
            <p>Tùy chỉnh linh hoạt câu chữ, tiêu đề, số liệu và hình ảnh toàn website (Ngoại trừ Menu Chính & Menu Chân Trang)</p>
        </div>
        <div class="dl-badge-luxury">
            ★ Dragon Legend 4-Star Luxury
        </div>
    </div>

    <?php settings_errors(); ?>

    <form method="post" action="options.php">
        <?php settings_fields('dragonlegend_static_content_group'); ?>

        <!-- Tab Navigation Buttons -->
        <div class="dl-tabs-nav">
            <button type="button" class="dl-tab-btn active" data-tab="tab-header">
                <span class="dashicons dashicons-heading"></span> Đầu Trang (Header)
            </button>
            <button type="button" class="dl-tab-btn" data-tab="tab-hero">
                <span class="dashicons dashicons-format-image"></span> Banner Chính (Hero)
            </button>
            <button type="button" class="dl-tab-btn" data-tab="tab-stats">
                <span class="dashicons dashicons-chart-bar"></span> Chỉ Số Nổi Bật (Stats)
            </button>
            <button type="button" class="dl-tab-btn" data-tab="tab-about">
                <span class="dashicons dashicons-building"></span> Về Khách Sạn (About)
            </button>
            <button type="button" class="dl-tab-btn" data-tab="tab-amenities">
                <span class="dashicons dashicons-star-filled"></span> Tiện Nghi Nổi Bật
            </button>
            <button type="button" class="dl-tab-btn" data-tab="tab-sections">
                <span class="dashicons dashicons-editor-kitchensink"></span> Tiêu Đề Các Khối
            </button>
            <button type="button" class="dl-tab-btn" data-tab="tab-location">
                <span class="dashicons dashicons-location-alt"></span> Vị Trí & Bản Đồ
            </button>
            <button type="button" class="dl-tab-btn" data-tab="tab-footer">
                <span class="dashicons dashicons-admin-links"></span> Chân Trang & Mạng Xã Hội
            </button>
            <button type="button" class="dl-tab-btn" data-tab="tab-analytics" style="color: #0284c7; font-weight: 600;">
                <span class="dashicons dashicons-analytics"></span> Google Tag & GA4
            </button>
        </div>

        <!-- ========================================== -->
        <!-- TAB 1: HEADER & TOPBAR                     -->
        <!-- ========================================== -->
        <div id="tab-header" class="dl-tab-pane active">
            <h2 class="dl-section-heading">Thông Tin Chung & Thanh Điều Hướng (Header)</h2>
            <p class="dl-section-desc">Cấu hình thanh thông báo ưu đãi trên cùng, số hotline, logo và nút hành động đầu trang.</p>

            <div class="dl-card-box">
                <h3 class="dl-card-box-title">Dải Thông Báo Khuyến Mãi (Top Notification Bar)</h3>
                <div class="dl-form-grid">
                    <div class="dl-form-group">
                        <label>Thông điệp cam kết:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[topbar_badge]" value="<?php echo dl_val('topbar_badge', $data, $defaults); ?>">
                        <p class="description">Hiển thị góc trái thanh trên cùng.</p>
                    </div>
                    <div class="dl-form-group">
                        <label>Mã giảm giá ưu đãi:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[topbar_promo]" value="<?php echo dl_val('topbar_promo', $data, $defaults); ?>">
                        <p class="description">Ví dụ: Nhập mã DRAGON2026 giảm ngay 10%</p>
                    </div>
                </div>
            </div>

            <div class="dl-card-box">
                <h3 class="dl-card-box-title">Thông Tin Liên Hệ & Thương Hiệu Header</h3>
                <div class="dl-form-grid">
                    <div class="dl-form-group">
                        <label>Tên khách sạn:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[hotel_name]" value="<?php echo dl_val('hotel_name', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group">
                        <label>Hotline hiển thị:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[hotel_hotline]" value="<?php echo dl_val('hotel_hotline', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group">
                        <label>Số điện thoại bấm gọi (tel:):</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[hotel_hotline_tel]" value="<?php echo dl_val('hotel_hotline_tel', $data, $defaults); ?>">
                        <p class="description">Định dạng quốc tế, ví dụ: +842033518999</p>
                    </div>
                    <div class="dl-form-group">
                        <label>Email liên hệ:</label>
                        <input type="email" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[hotel_email]" value="<?php echo dl_val('hotel_email', $data, $defaults); ?>">
                    </div>
                </div>
            </div>

            <div class="dl-card-box">
                <h3 class="dl-card-box-title">Logo & Nút Đặt Phòng Header</h3>
                <div class="dl-form-grid">
                    <div class="dl-form-group full-width">
                        <label>Hình ảnh Logo Header:</label>
                        <div class="dl-image-upload-wrap">
                            <div class="dl-image-preview" id="preview-header-logo">
                                <?php $logo_url = dl_val('header_logo_url', $data, $defaults); ?>
                                <?php if ($logo_url) : ?>
                                    <img src="<?php echo esc_url($logo_url); ?>" alt="Logo">
                                <?php else : ?>
                                    <span class="placeholder">Chưa có ảnh</span>
                                <?php endif; ?>
                            </div>
                            <div style="flex:1;">
                                <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[header_logo_url]" value="<?php echo $logo_url; ?>" data-preview="#preview-header-logo" style="margin-bottom:8px;">
                                <button type="button" class="button dl-upload-btn">Chọn ảnh từ thư viện</button>
                            </div>
                        </div>
                    </div>
                    <div class="dl-form-group">
                        <label>Tên nút đặt phòng:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[header_book_btn_text]" value="<?php echo dl_val('header_book_btn_text', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group">
                        <label>Đường dẫn nút đặt phòng:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[header_book_btn_url]" value="<?php echo dl_val('header_book_btn_url', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group">
                        <label>Đường dẫn Tour VR 360°:</label>
                        <input type="url" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[header_vr_url]" value="<?php echo dl_val('header_vr_url', $data, $defaults); ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 2: HERO BANNER                         -->
        <!-- ========================================== -->
        <div id="tab-hero" class="dl-tab-pane">
            <h2 class="dl-section-heading">Banner Đầu Trang (Hero Section)</h2>
            <p class="dl-section-desc">Tùy biến tiêu đề lớn kiêu hãnh, lời dẫn và ảnh nền phong cảnh khách sạn trên màn hình đầu tiên.</p>

            <div class="dl-card-box">
                <div class="dl-form-grid">
                    <div class="dl-form-group full-width">
                        <label>Huy hiệu nhỏ trên tiêu đề:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[hero_badge_text]" value="<?php echo dl_val('hero_badge_text', $data, $defaults); ?>">
                        <p class="description">Ví dụ: Khách Sạn Tân Cổ Điển 4 Sao Bãi Cháy</p>
                    </div>
                    <div class="dl-form-group">
                        <label>Tiêu đề H1 - Dòng 1 (Màu trắng):</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[hero_h1_line1]" value="<?php echo dl_val('hero_h1_line1', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group">
                        <label>Tiêu đề H1 - Dòng 2 (Màu vàng kim ánh sáng):</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[hero_h1_line2]" value="<?php echo dl_val('hero_h1_line2', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group full-width">
                        <label>Đoạn giới thiệu / Phụ đề Hero:</label>
                        <textarea rows="3" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[hero_description]"><?php echo dl_val_html('hero_description', $data, $defaults); ?></textarea>
                    </div>
                    <div class="dl-form-group full-width">
                        <label>Ảnh nền Banner Hero:</label>
                        <div class="dl-image-upload-wrap">
                            <div class="dl-image-preview" id="preview-hero-bg">
                                <?php $hero_bg = dl_val('hero_bg_image', $data, $defaults); ?>
                                <?php if ($hero_bg) : ?>
                                    <img src="<?php echo esc_url($hero_bg); ?>" alt="Hero BG">
                                <?php else : ?>
                                    <span class="placeholder">Chưa có ảnh</span>
                                <?php endif; ?>
                            </div>
                            <div style="flex:1;">
                                <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[hero_bg_image]" value="<?php echo $hero_bg; ?>" data-preview="#preview-hero-bg" style="margin-bottom:8px;">
                                <button type="button" class="button dl-upload-btn">Chọn ảnh từ thư viện</button>
                            </div>
                        </div>
                    </div>
                    <div class="dl-form-group">
                        <label>Nút thao tác 1 - Tên nút:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[hero_btn1_text]" value="<?php echo dl_val('hero_btn1_text', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group">
                        <label>Nút thao tác 1 - Liên kết:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[hero_btn1_url]" value="<?php echo dl_val('hero_btn1_url', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group">
                        <label>Nút thao tác 2 - Tên nút:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[hero_btn2_text]" value="<?php echo dl_val('hero_btn2_text', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group">
                        <label>Nút thao tác 2 - Liên kết:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[hero_btn2_url]" value="<?php echo dl_val('hero_btn2_url', $data, $defaults); ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 3: STATS COUNTER                       -->
        <!-- ========================================== -->
        <div id="tab-stats" class="dl-tab-pane">
            <h2 class="dl-section-heading">Dải Chỉ Số Nổi Bật (Stats Counter Bar)</h2>
            <p class="dl-section-desc">Cấu hình 4 con số thống kê uy tín hiển thị ngay dưới thanh tìm kiếm đặt phòng.</p>

            <div class="dl-form-grid">
                <!-- Chỉ số 1 -->
                <div class="dl-card-box">
                    <h3 class="dl-card-box-title">Chỉ Số 1 (Phòng nghỉ)</h3>
                    <div class="dl-form-group">
                        <label>Con số thống kê:</label>
                        <input type="number" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[stats_num_1]" value="<?php echo dl_val('stats_num_1', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group">
                        <label>Ký tự đơn vị đi kèm:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[stats_unit_1]" value="<?php echo dl_val('stats_unit_1', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group">
                        <label>Nhãn mô tả:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[stats_label_1]" value="<?php echo dl_val('stats_label_1', $data, $defaults); ?>">
                    </div>
                </div>

                <!-- Chỉ số 2 -->
                <div class="dl-card-box">
                    <h3 class="dl-card-box-title">Chỉ Số 2 (Sao Quốc tế)</h3>
                    <div class="dl-form-group">
                        <label>Con số thống kê:</label>
                        <input type="number" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[stats_num_2]" value="<?php echo dl_val('stats_num_2', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group">
                        <label>Ký tự đơn vị đi kèm:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[stats_unit_2]" value="<?php echo dl_val('stats_unit_2', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group">
                        <label>Nhãn mô tả:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[stats_label_2]" value="<?php echo dl_val('stats_label_2', $data, $defaults); ?>">
                    </div>
                </div>

                <!-- Chỉ số 3 -->
                <div class="dl-card-box">
                    <h3 class="dl-card-box-title">Chỉ Số 3 (Tầng tiện ích)</h3>
                    <div class="dl-form-group">
                        <label>Con số thống kê:</label>
                        <input type="number" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[stats_num_3]" value="<?php echo dl_val('stats_num_3', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group">
                        <label>Ký tự đơn vị đi kèm:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[stats_unit_3]" value="<?php echo dl_val('stats_unit_3', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group">
                        <label>Nhãn mô tả:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[stats_label_3]" value="<?php echo dl_val('stats_label_3', $data, $defaults); ?>">
                    </div>
                </div>

                <!-- Chỉ số 4 -->
                <div class="dl-card-box">
                    <h3 class="dl-card-box-title">Chỉ Số 4 (Tỷ lệ View)</h3>
                    <div class="dl-form-group">
                        <label>Con số thống kê:</label>
                        <input type="number" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[stats_num_4]" value="<?php echo dl_val('stats_num_4', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group">
                        <label>Ký tự đơn vị đi kèm:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[stats_unit_4]" value="<?php echo dl_val('stats_unit_4', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group">
                        <label>Nhãn mô tả:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[stats_label_4]" value="<?php echo dl_val('stats_label_4', $data, $defaults); ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 4: ABOUT HOTEL                         -->
        <!-- ========================================== -->
        <div id="tab-about" class="dl-tab-pane">
            <h2 class="dl-section-heading">Khối Về Khách Sạn (About Us)</h2>
            <p class="dl-section-desc">Câu chuyện thương hiệu, hình ảnh ghép nghệ thuật và các cam kết cốt lõi.</p>

            <div class="dl-card-box">
                <h3 class="dl-card-box-title">Tiêu Đề & Lời Tự Sự</h3>
                <div class="dl-form-grid">
                    <div class="dl-form-group">
                        <label>Tagline nhỏ:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[about_tagline]" value="<?php echo dl_val('about_tagline', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group">
                        <label>Tiêu đề lớn:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[about_title]" value="<?php echo dl_val('about_title', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group full-width">
                        <label>Đoạn văn 1:</label>
                        <textarea rows="3" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[about_p1]"><?php echo dl_val_html('about_p1', $data, $defaults); ?></textarea>
                    </div>
                    <div class="dl-form-group full-width">
                        <label>Đoạn văn 2:</label>
                        <textarea rows="3" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[about_p2]"><?php echo dl_val_html('about_p2', $data, $defaults); ?></textarea>
                    </div>
                </div>
            </div>

            <div class="dl-card-box">
                <h3 class="dl-card-box-title">4 Tiêu Chí Điểm Nhấn</h3>
                <div class="dl-form-grid">
                    <div class="dl-form-group">
                        <label>Điểm nhấn 1:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[about_badge1_title]" value="<?php echo dl_val('about_badge1_title', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group">
                        <label>Điểm nhấn 2:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[about_badge2_title]" value="<?php echo dl_val('about_badge2_title', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group">
                        <label>Điểm nhấn 3:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[about_badge3_title]" value="<?php echo dl_val('about_badge3_title', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group">
                        <label>Điểm nhấn 4:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[about_badge4_title]" value="<?php echo dl_val('about_badge4_title', $data, $defaults); ?>">
                    </div>
                </div>
            </div>

            <div class="dl-card-box">
                <h3 class="dl-card-box-title">Hình Ảnh Minh Họa & Nút Bấm</h3>
                <div class="dl-form-grid">
                    <div class="dl-form-group full-width">
                        <label>Ảnh lớn chính (Không gian sảnh):</label>
                        <div class="dl-image-upload-wrap">
                            <div class="dl-image-preview" id="preview-about-main">
                                <?php $about_main = dl_val('about_image_main', $data, $defaults); ?>
                                <?php if ($about_main) : ?>
                                    <img src="<?php echo esc_url($about_main); ?>" alt="About Main">
                                <?php else : ?>
                                    <span class="placeholder">Chưa có ảnh</span>
                                <?php endif; ?>
                            </div>
                            <div style="flex:1;">
                                <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[about_image_main]" value="<?php echo $about_main; ?>" data-preview="#preview-about-main" style="margin-bottom:8px;">
                                <button type="button" class="button dl-upload-btn">Chọn ảnh từ thư viện</button>
                            </div>
                        </div>
                    </div>
                    <div class="dl-form-group full-width">
                        <label>Ảnh nhỏ góc (Phòng ngủ):</label>
                        <div class="dl-image-upload-wrap">
                            <div class="dl-image-preview" id="preview-about-sub">
                                <?php $about_sub = dl_val('about_image_sub', $data, $defaults); ?>
                                <?php if ($about_sub) : ?>
                                    <img src="<?php echo esc_url($about_sub); ?>" alt="About Sub">
                                <?php else : ?>
                                    <span class="placeholder">Chưa có ảnh</span>
                                <?php endif; ?>
                            </div>
                            <div style="flex:1;">
                                <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[about_image_sub]" value="<?php echo $about_sub; ?>" data-preview="#preview-about-sub" style="margin-bottom:8px;">
                                <button type="button" class="button dl-upload-btn">Chọn ảnh từ thư viện</button>
                            </div>
                        </div>
                    </div>
                    <div class="dl-form-group">
                        <label>Tên nút liên hệ:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[about_btn_contact_text]" value="<?php echo dl_val('about_btn_contact_text', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group">
                        <label>Tên nút xem 360:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[about_btn_vr_text]" value="<?php echo dl_val('about_btn_vr_text', $data, $defaults); ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB AMENITIES: POPULAR AMENITIES           -->
        <!-- ========================================== -->
        <div id="tab-amenities" class="dl-tab-pane">
            <h2 class="dl-section-heading">
                <span class="dashicons dashicons-star-filled" style="color: #d97706; font-size: 22px; width: 22px; height: 22px;"></span> 
                Quản Lý Tiện Nghi Nổi Bật (Popular Amenities)
            </h2>
            <p class="dl-section-desc">Tùy chỉnh tiêu đề khối và dễ dàng Thêm, Sửa, Xoá, Sắp xếp các tiện ích nổi bật hiển thị ở vị trí trang trọng trên trang chủ.</p>

            <!-- Tiêu đề khối -->
            <div class="dl-card-box">
                <h3 class="dl-card-box-title">Tiêu Đề & Lời Dẫn Khối Tiện Nghi Nổi Bật</h3>
                <div class="dl-form-grid">
                    <div class="dl-form-group">
                        <label>Tagline (Dòng chữ nhỏ):</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[sec_amenities_popular_tag]" value="<?php echo dl_val('sec_amenities_popular_tag', $data, $defaults); ?>">
                        <p class="description">Mặc định: Trải Nghiệm Khách Sạn</p>
                    </div>
                    <div class="dl-form-group">
                        <label>Tiêu đề chính:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[sec_amenities_popular_ttl]" value="<?php echo dl_val('sec_amenities_popular_ttl', $data, $defaults); ?>">
                        <p class="description">Mặc định: Tiện Nghi Nổi Bật Được Yêu Thích</p>
                    </div>
                    <div class="dl-form-group full-width">
                        <label>Lời mô tả:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[sec_amenities_popular_dsc]" value="<?php echo dl_val('sec_amenities_popular_dsc', $data, $defaults); ?>">
                        <p class="description">Mô tả khái quát hệ thống tiện ích đồng bộ của khách sạn.</p>
                    </div>
                </div>
            </div>

            <!-- Bảng Quản Lý Danh Sách Tiện Nghi Nổi Bật -->
            <div class="dl-card-box">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 12px;">
                    <div>
                        <h3 class="dl-card-box-title" style="margin-bottom: 4px;">Danh Sách Tiện Nghi Trang Chủ</h3>
                        <p class="dl-section-desc" style="margin-bottom: 0;">Quản lý từng dòng tiện nghi: chọn Icon SVG, gõ Tên & Mô tả. Dùng nút ▲ ▼ để sắp xếp thứ tự hiển thị.</p>
                    </div>
                    <button type="button" class="button button-primary dl-btn-add-amenity" style="background: #0f766e; border-color: #0d9488; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; padding: 6px 16px; height: auto;">
                        <span class="dashicons dashicons-plus-alt2" style="font-size: 16px; width: 16px; height: 16px;"></span> Thêm Tiện Nghi Mới
                    </button>
                </div>

                <div class="dl-table-responsive" style="overflow-x: auto; background: #fff; border-radius: 8px; border: 1px solid #cbd5e1; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <table class="wp-list-table widefat fixed striped dl-amenities-table" style="border: none; margin: 0; min-width: 720px;">
                        <thead>
                            <tr style="background: #07111e;">
                                <th style="width: 80px; text-align: center; color: #c5a880; font-weight: 700; padding: 12px 8px;">Thứ tự</th>
                                <th style="width: 240px; color: #c5a880; font-weight: 700; padding: 12px 10px;">Biểu tượng (Icon)</th>
                                <th style="width: 230px; color: #c5a880; font-weight: 700; padding: 12px 10px;">Tên tiện nghi</th>
                                <th style="color: #c5a880; font-weight: 700; padding: 12px 10px;">Lời mô tả ngắn</th>
                                <th style="width: 65px; text-align: center; color: #c5a880; font-weight: 700; padding: 12px 8px;">Xoá</th>
                            </tr>
                        </thead>
                        <tbody id="dl-amenities-tbody">
                            <?php
                            $popular_amenities_list = DragonLegend_Content_Manager::get_popular_amenities();
                            foreach ($popular_amenities_list as $a_idx => $am_item) :
                                $cur_name = isset($am_item['name']) ? $am_item['name'] : '';
                                $cur_icon = isset($am_item['icon']) ? $am_item['icon'] : 'sparkles';
                                $cur_desc = isset($am_item['desc']) ? $am_item['desc'] : '';
                            ?>
                            <tr class="dl-amenity-row" data-index="<?php echo $a_idx; ?>">
                                <td style="text-align: center; vertical-align: middle; padding: 10px 6px;">
                                    <div style="display: flex; align-items: center; justify-content: center; gap: 5px;">
                                        <span class="dl-row-number" style="display: inline-flex; align-items: center; justify-content: center; width: 24px; height: 24px; background: #07111e; color: #c5a880; font-weight: 700; border-radius: 50%; font-size: 11px;">
                                            <?php echo $a_idx + 1; ?>
                                        </span>
                                        <div style="display: flex; flex-direction: column; gap: 2px;">
                                            <button type="button" class="dl-btn-row-up" title="Di chuyển lên trên" style="border: 1px solid #cbd5e1; background: #f8fafc; cursor: pointer; border-radius: 3px; font-size: 8px; line-height: 1; padding: 3px 4px; color: #334155;">▲</button>
                                            <button type="button" class="dl-btn-row-down" title="Di chuyển xuống dưới" style="border: 1px solid #cbd5e1; background: #f8fafc; cursor: pointer; border-radius: 3px; font-size: 8px; line-height: 1; padding: 3px 4px; color: #334155;">▼</button>
                                        </div>
                                    </div>
                                </td>
                                <td style="vertical-align: middle; padding: 10px;">
                                    <div style="display: flex; align-items: center; gap: 6px;">
                                        <div class="dl-icon-preview-box" style="width: 34px; height: 34px; border-radius: 6px; background: #fef3c7; border: 1px solid #fde68a; display: flex; align-items: center; justify-content: center; color: #b45309; flex-shrink: 0; overflow: hidden;">
                                            <?php echo function_exists('dl_get_amenity_svg') ? dl_get_amenity_svg($cur_icon, "w-5 h-5 text-gold") : '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3z"/></svg>'; ?>
                                        </div>
                                        <select class="dl-amenity-select-icon" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[popular_amenities][<?php echo $a_idx; ?>][icon]" style="flex: 1; max-width: 150px; font-size: 12px; height: 32px;">
                                            <?php if (!empty($cur_icon) && !isset($amenity_icons[$cur_icon])) : ?>
                                                <option value="<?php echo esc_attr($cur_icon); ?>" selected>File tải (<?php echo esc_html(basename(parse_url($cur_icon, PHP_URL_PATH))); ?>)</option>
                                            <?php endif; ?>
                                            <?php foreach ($amenity_icons as $icon_val => $icon_label) : ?>
                                                <option value="<?php echo esc_attr($icon_val); ?>" <?php selected($cur_icon, $icon_val); ?>>
                                                    <?php echo esc_html($icon_label); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="button" class="button button-small dl-btn-upload-amenity-icon" title="Tải lên biểu tượng (SVG / Ảnh) từ máy tính" style="padding: 0 7px; height: 32px; display: inline-flex; align-items: center; justify-content: center; background: #fff; border-color: #cbd5e1; flex-shrink: 0; color: #0f766e;">
                                            <span class="dashicons dashicons-upload" style="font-size: 14px; width: 14px; height: 14px;"></span>
                                        </button>
                                    </div>
                                </td>
                                <td style="vertical-align: middle; padding: 10px;">
                                    <input type="text" class="dl-amenity-input-name" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[popular_amenities][<?php echo $a_idx; ?>][name]" value="<?php echo esc_attr($cur_name); ?>" placeholder="VD: Gym & Yoga" style="width: 100%; font-weight: 600; color: #0f172a; height: 32px;">
                                </td>
                                <td style="vertical-align: middle; padding: 10px;">
                                    <input type="text" class="dl-amenity-input-desc" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[popular_amenities][<?php echo $a_idx; ?>][desc]" value="<?php echo esc_attr($cur_desc); ?>" placeholder="Mô tả ngắn gọn về tiện nghi" style="width: 100%; height: 32px;">
                                </td>
                                <td style="text-align: center; vertical-align: middle; padding: 10px 6px;">
                                    <button type="button" class="dl-btn-delete-row" title="Xoá tiện nghi này" style="background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; border-radius: 6px; padding: 5px 8px; cursor: pointer; transition: all 0.2s; display: inline-flex; align-items: center; justify-content: center;">
                                        <span class="dashicons dashicons-trash" style="font-size: 15px; width: 15px; height: 15px;"></span>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div style="margin-top: 14px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                    <button type="button" class="button button-primary dl-btn-add-amenity" style="background: #0f766e; border-color: #0d9488; font-weight: 600; display: inline-flex; align-items: center; gap: 6px; padding: 6px 18px; height: auto;">
                        <span class="dashicons dashicons-plus-alt2" style="font-size: 16px; width: 16px; height: 16px;"></span> Thêm Tiện Nghi Mới
                    </button>
                    <span style="font-size: 12px; color: #64748b;">
                        <em>💡 Mẹo: Bấm ▲ hoặc ▼ để đổi vị trí hiển thị trên trang chủ. Sau đó bấm nút <strong>"Lưu Toàn Bộ Thay Đổi"</strong> ở thanh nổi phía dưới màn hình.</em>
                    </span>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 5: SECTION HEADINGS                    -->
        <!-- ========================================== -->
        <div id="tab-sections" class="dl-tab-pane">
            <h2 class="dl-section-heading">Tiêu Đề & Lời Dẫn Các Khối Trang Chủ</h2>
            <p class="dl-section-desc">Chỉnh sửa nhanh Tagline, Tiêu đề lớn và Lời mô tả của từng khu vực nội dung trên trang chủ.</p>

            <!-- Khối Tiện nghi nổi bật -->
            <div class="dl-card-box" style="border-left: 4px solid #c5a880; background: #fffcf8;">
                <h3 class="dl-card-box-title">1. Khối Tiện Nghi Nổi Bật Được Yêu Thích</h3>
                <p style="color: #64748b; font-size: 13px; margin: 0 0 12px 0;">Tiêu đề, lời dẫn và toàn bộ 12 mục tiện nghi nổi bật được quản lý chi tiết tại tab riêng.</p>
                <button type="button" class="button button-secondary dl-switch-tab-btn" data-target="tab-amenities" style="color: #8c6a38; border-color: #c5a880; font-weight: 600;">
                    <span class="dashicons dashicons-star-filled" style="vertical-align: text-top; color: #d97706;"></span> Chuyển sang Tab Tiện Nghi Nổi Bật
                </button>
            </div>

            <!-- Khối Hạng phòng -->
            <div class="dl-card-box">
                <h3 class="dl-card-box-title">2. Khối Hạng Phòng Nghỉ Hoàng Gia</h3>
                <div class="dl-form-grid">
                    <div class="dl-form-group">
                        <label>Tagline:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[sec_rooms_tag]" value="<?php echo dl_val('sec_rooms_tag', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group">
                        <label>Tiêu đề lớn:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[sec_rooms_ttl]" value="<?php echo dl_val('sec_rooms_ttl', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group full-width">
                        <label>Lời mô tả:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[sec_rooms_dsc]" value="<?php echo dl_val('sec_rooms_dsc', $data, $defaults); ?>">
                    </div>
                </div>
            </div>

            <!-- Khối Ưu đãi -->
            <div class="dl-card-box">
                <h3 class="dl-card-box-title">3. Khối Ưu Đãi & Combo Trải Nghiệm</h3>
                <div class="dl-form-grid">
                    <div class="dl-form-group">
                        <label>Tagline:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[sec_offers_tag]" value="<?php echo dl_val('sec_offers_tag', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group">
                        <label>Tiêu đề lớn:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[sec_offers_ttl]" value="<?php echo dl_val('sec_offers_ttl', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group full-width">
                        <label>Lời mô tả:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[sec_offers_dsc]" value="<?php echo dl_val('sec_offers_dsc', $data, $defaults); ?>">
                    </div>
                </div>
            </div>

            <!-- Khối Dịch vụ & Tiện ích -->
            <div class="dl-card-box">
                <h3 class="dl-card-box-title">4. Khối Dịch Vụ & Tiện Ích Đỉnh Cao</h3>
                <div class="dl-form-grid">
                    <div class="dl-form-group">
                        <label>Tagline:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[sec_amenities_tag]" value="<?php echo dl_val('sec_amenities_tag', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group">
                        <label>Tiêu đề lớn:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[sec_amenities_ttl]" value="<?php echo dl_val('sec_amenities_ttl', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group full-width">
                        <label>Lời mô tả:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[sec_amenities_dsc]" value="<?php echo dl_val('sec_amenities_dsc', $data, $defaults); ?>">
                    </div>
                </div>
            </div>

            <!-- Khối Đánh giá -->
            <div class="dl-card-box">
                <h3 class="dl-card-box-title">5. Khối Đánh Giá Khách Hàng (Reviews)</h3>
                <div class="dl-form-grid">
                    <div class="dl-form-group">
                        <label>Tagline:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[sec_reviews_tag]" value="<?php echo dl_val('sec_reviews_tag', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group">
                        <label>Tiêu đề lớn:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[sec_reviews_ttl]" value="<?php echo dl_val('sec_reviews_ttl', $data, $defaults); ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 6: LOCATION & MAP                      -->
        <!-- ========================================== -->
        <div id="tab-location" class="dl-tab-pane">
            <h2 class="dl-section-heading">Vị Trí Chiến Lược & Địa Điểm Lân Cận</h2>
            <p class="dl-section-desc">Cấu hình địa chỉ, link nhúng Google Maps và khoảng cách đến các khu vui chơi nổi tiếng.</p>

            <div class="dl-card-box">
                <h3 class="dl-card-box-title">Thông Tin Vị Trí & Bản Đồ</h3>
                <div class="dl-form-grid">
                    <div class="dl-form-group">
                        <label>Tagline khối:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[sec_contact_tag]" value="<?php echo dl_val('sec_contact_tag', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group">
                        <label>Tiêu đề khối:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[sec_contact_ttl]" value="<?php echo dl_val('sec_contact_ttl', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group full-width">
                        <label>Đoạn mô tả vị trí:</label>
                        <textarea rows="2" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[sec_contact_dsc]"><?php echo dl_val_html('sec_contact_dsc', $data, $defaults); ?></textarea>
                    </div>
                    <div class="dl-form-group full-width">
                        <label>Địa chỉ khách sạn hiển thị:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[hotel_address]" value="<?php echo dl_val('hotel_address', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group full-width">
                        <label>Đường dẫn nhúng Google Maps (Iframe Embed URL):</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[map_embed_url]" value="<?php echo dl_val('map_embed_url', $data, $defaults); ?>">
                        <p class="description">Chỉ cần dán link src từ mã nhúng của Google Maps.</p>
                    </div>
                </div>
            </div>

            <div class="dl-card-box">
                <h3 class="dl-card-box-title">Danh Sách 4 Địa Điểm Nổi Tiếng Lân Cận</h3>
                
                <!-- Địa điểm 1 -->
                <div style="padding:12px; background:#fff; border-radius:8px; margin-bottom:12px; border:1px solid #e2e8f0;">
                    <div class="dl-form-grid" style="margin-bottom:0;">
                        <div class="dl-form-group">
                            <label>Địa điểm 1 - Tên:</label>
                            <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[attraction_1_name]" value="<?php echo dl_val('attraction_1_name', $data, $defaults); ?>">
                        </div>
                        <div class="dl-form-group">
                            <label>Khoảng cách & thời gian:</label>
                            <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[attraction_1_dist]" value="<?php echo dl_val('attraction_1_dist', $data, $defaults); ?>">
                        </div>
                        <div class="dl-form-group full-width" style="margin-bottom:0;">
                            <label>Mô tả ngắn:</label>
                            <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[attraction_1_desc]" value="<?php echo dl_val('attraction_1_desc', $data, $defaults); ?>">
                        </div>
                    </div>
                </div>

                <!-- Địa điểm 2 -->
                <div style="padding:12px; background:#fff; border-radius:8px; margin-bottom:12px; border:1px solid #e2e8f0;">
                    <div class="dl-form-grid" style="margin-bottom:0;">
                        <div class="dl-form-group">
                            <label>Địa điểm 2 - Tên:</label>
                            <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[attraction_2_name]" value="<?php echo dl_val('attraction_2_name', $data, $defaults); ?>">
                        </div>
                        <div class="dl-form-group">
                            <label>Khoảng cách & thời gian:</label>
                            <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[attraction_2_dist]" value="<?php echo dl_val('attraction_2_dist', $data, $defaults); ?>">
                        </div>
                        <div class="dl-form-group full-width" style="margin-bottom:0;">
                            <label>Mô tả ngắn:</label>
                            <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[attraction_2_desc]" value="<?php echo dl_val('attraction_2_desc', $data, $defaults); ?>">
                        </div>
                    </div>
                </div>

                <!-- Địa điểm 3 -->
                <div style="padding:12px; background:#fff; border-radius:8px; margin-bottom:12px; border:1px solid #e2e8f0;">
                    <div class="dl-form-grid" style="margin-bottom:0;">
                        <div class="dl-form-group">
                            <label>Địa điểm 3 - Tên:</label>
                            <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[attraction_3_name]" value="<?php echo dl_val('attraction_3_name', $data, $defaults); ?>">
                        </div>
                        <div class="dl-form-group">
                            <label>Khoảng cách & thời gian:</label>
                            <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[attraction_3_dist]" value="<?php echo dl_val('attraction_3_dist', $data, $defaults); ?>">
                        </div>
                        <div class="dl-form-group full-width" style="margin-bottom:0;">
                            <label>Mô tả ngắn:</label>
                            <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[attraction_3_desc]" value="<?php echo dl_val('attraction_3_desc', $data, $defaults); ?>">
                        </div>
                    </div>
                </div>

                <!-- Địa điểm 4 -->
                <div style="padding:12px; background:#fff; border-radius:8px; border:1px solid #e2e8f0;">
                    <div class="dl-form-grid" style="margin-bottom:0;">
                        <div class="dl-form-group">
                            <label>Địa điểm 4 - Tên:</label>
                            <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[attraction_4_name]" value="<?php echo dl_val('attraction_4_name', $data, $defaults); ?>">
                        </div>
                        <div class="dl-form-group">
                            <label>Khoảng cách & thời gian:</label>
                            <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[attraction_4_dist]" value="<?php echo dl_val('attraction_4_dist', $data, $defaults); ?>">
                        </div>
                        <div class="dl-form-group full-width" style="margin-bottom:0;">
                            <label>Mô tả ngắn:</label>
                            <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[attraction_4_desc]" value="<?php echo dl_val('attraction_4_desc', $data, $defaults); ?>">
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 7: FOOTER & SOCIAL                     -->
        <!-- ========================================== -->
        <div id="tab-footer" class="dl-tab-pane">
            <h2 class="dl-section-heading">Chân Trang, Mạng Xã Hội & Tiện Ích Nổi</h2>
            <p class="dl-section-desc">Cấu hình mô tả thương hiệu chân trang, các liên kết mạng xã hội, form nhận tin và dòng bản quyền.</p>

            <!-- Hướng dẫn Menu Chân Trang -->
            <div class="dl-card-box" style="border-left: 4px solid #c5a880; background: #fffcf8;">
                <h3 class="dl-card-box-title">2 Cột Menu Chân Trang: Thông Tin & Quy Định Chính Sách</h3>
                <p style="color: #64748b; font-size: 13px; margin: 0 0 12px 0;">
                    Hai cột liên kết <strong>"Thông tin"</strong> và <strong>"Quy định & Chính sách"</strong> ở chân trang được quản lý động hoàn toàn thông qua hệ thống Menu của WordPress. Bạn có thể tự do thêm, sửa, xóa hoặc thay đổi thứ tự các liên kết.
                </p>
                <a href="<?php echo esc_url(admin_url('nav-menus.php')); ?>" class="button button-secondary" style="color: #8c6a38; border-color: #c5a880; font-weight: 600;">
                    <span class="dashicons dashicons-menu" style="vertical-align: text-top; color: #d97706;"></span> Quản Lý Menu Chân Trang (Giao Diện → Menu)
                </a>
            </div>

            <!-- Logo & Thương Hiệu Chân Trang -->
            <div class="dl-card-box">
                <h3 class="dl-card-box-title">Logo & Tên Thương Hiệu Chân Trang</h3>
                <div class="dl-form-grid">
                    <div class="dl-form-group">
                        <label>Tên thương hiệu chân trang:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[footer_brand_title]" value="<?php echo dl_val('footer_brand_title', $data, $defaults); ?>">
                        <p class="description">Hiển thị ở cột 1 chân trang (Ví dụ: Dragon Legend Hotel Hạ Long).</p>
                    </div>
                    <div class="dl-form-group">
                        <label>Phụ đề / Số sao chân trang:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[footer_brand_subtitle]" value="<?php echo dl_val('footer_brand_subtitle', $data, $defaults); ?>">
                        <p class="description">Ví dụ: HẠ LONG ★★★★ hoặc Khách sạn 4 sao Bãi Cháy.</p>
                    </div>
                    <div class="dl-form-group full-width">
                        <label>Hình ảnh Logo Chân Trang:</label>
                        <div class="dl-image-upload-wrap">
                            <div class="dl-image-preview" id="preview-footer-logo">
                                <?php $footer_logo_val = dl_val('footer_logo_url', $data, $defaults); ?>
                                <?php if ($footer_logo_val) : ?>
                                    <img src="<?php echo esc_url($footer_logo_val); ?>" alt="Footer Logo">
                                <?php else : ?>
                                    <span class="placeholder">Chưa có ảnh</span>
                                <?php endif; ?>
                            </div>
                            <div style="flex:1;">
                                <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[footer_logo_url]" value="<?php echo $footer_logo_val; ?>" data-preview="#preview-footer-logo" style="margin-bottom:8px;">
                                <button type="button" class="button dl-upload-btn">Chọn ảnh từ thư viện</button>
                                <p class="description" style="margin-top:6px;">Nếu để trống, hệ thống sẽ tự động dùng Logo từ phần Đầu Trang (Header).</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dl-card-box">
                <h3 class="dl-card-box-title">Mô Tả Thương Hiệu Chân Trang</h3>
                <div class="dl-form-group">
                    <label>Đoạn giới thiệu ngắn:</label>
                    <textarea rows="3" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[footer_desc]"><?php echo dl_val_html('footer_desc', $data, $defaults); ?></textarea>
                </div>
            </div>

            <div class="dl-card-box">
                <h3 class="dl-card-box-title">Liên Kết Mạng Xã Hội & Chat</h3>
                <div class="dl-form-grid">
                    <div class="dl-form-group">
                        <label>Facebook Fanpage:</label>
                        <input type="url" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[social_facebook]" value="<?php echo dl_val('social_facebook', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group">
                        <label>TikTok:</label>
                        <input type="url" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[social_tiktok]" value="<?php echo dl_val('social_tiktok', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group">
                        <label>YouTube Channel:</label>
                        <input type="url" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[social_youtube]" value="<?php echo dl_val('social_youtube', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group">
                        <label>Facebook Messenger:</label>
                        <input type="url" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[social_messenger]" value="<?php echo dl_val('social_messenger', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group">
                        <label>Zalo Chat:</label>
                        <input type="url" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[social_zalo]" value="<?php echo dl_val('social_zalo', $data, $defaults); ?>">
                    </div>
                </div>
            </div>

            <div class="dl-card-box">
                <h3 class="dl-card-box-title">Hộp Đăng Ký Nhận Tin Ưu Đãi (Newsletter)</h3>
                <div class="dl-form-grid">
                    <div class="dl-form-group">
                        <label>Tiêu đề hộp nhận tin:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[footer_newsletter_title]" value="<?php echo dl_val('footer_newsletter_title', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group">
                        <label>Chữ nút bấm:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[footer_newsletter_btn]" value="<?php echo dl_val('footer_newsletter_btn', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group full-width">
                        <label>Lời mô tả quyền lợi:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[footer_newsletter_desc]" value="<?php echo dl_val('footer_newsletter_desc', $data, $defaults); ?>">
                    </div>
                </div>
            </div>

            <div class="dl-card-box">
                <h3 class="dl-card-box-title">Dòng Bản Quyền & Chứng Nhận</h3>
                <div class="dl-form-grid">
                    <div class="dl-form-group full-width">
                        <label>Dòng bản quyền (Copyright):</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[footer_copyright]" value="<?php echo dl_val('footer_copyright', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group">
                        <label>Nhãn chứng nhận 1:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[footer_subtext_1]" value="<?php echo dl_val('footer_subtext_1', $data, $defaults); ?>">
                    </div>
                    <div class="dl-form-group">
                        <label>Nhãn chứng nhận 2:</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[footer_subtext_2]" value="<?php echo dl_val('footer_subtext_2', $data, $defaults); ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- TAB 8: GOOGLE TAG & GA4 ANALYTICS          -->
        <!-- ========================================== -->
        <div id="tab-analytics" class="dl-tab-pane">
            <h2 class="dl-section-heading">Cấu Hình Google Tag, Google Analytics 4 (GA4) & Tag Manager</h2>
            <p class="dl-section-desc">Quản lý mã đo lường Google Tag và GA4 cho website hoạt động trên cả môi trường trực tuyến (live) và môi trường phát triển cục bộ (localhost).</p>

            <div class="dl-card-box">
                <h3 class="dl-card-box-title" style="color: #0284c7;">
                    <span class="dashicons dashicons-analytics"></span> Cấu Hình Google Analytics 4 (GA4) & Google Tag (gtag.js)
                </h3>
                <div class="dl-form-grid">
                    <div class="dl-form-group">
                        <label>Mã Đo Lường GA4 (Measurement ID):</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[ga4_measurement_id]" value="<?php echo dl_val('ga4_measurement_id', $data, $defaults); ?>" placeholder="VD: G-XXXXXXXXXX" style="font-family: monospace; font-weight: bold; font-size: 14px;">
                        <p class="description">Nhập mã đo lường bắt đầu bằng <code>G-</code> lấy từ Google Analytics. Hệ thống sẽ tự động chèn thư viện <code>gtag.js</code> chuẩn vào thẻ <code>&lt;head&gt;</code> của toàn bộ website.</p>
                    </div>

                    <div class="dl-form-group">
                        <label>Mã Google Tag Manager (GTM ID - Tùy chọn):</label>
                        <input type="text" name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[google_tag_manager_id]" value="<?php echo dl_val('google_tag_manager_id', $data, $defaults); ?>" placeholder="VD: GTM-XXXXXXX" style="font-family: monospace; font-size: 14px;">
                        <p class="description">Để trống nếu không sử dụng GTM. Nếu nhập, hệ thống sẽ chèn cả đoạn mã script trong <code>&lt;head&gt;</code> và đoạn <code>&lt;noscript&gt;</code> sau mở <code>&lt;body&gt;</code>.</p>
                    </div>
                </div>
            </div>

            <div class="dl-card-box">
                <h3 class="dl-card-box-title">
                    <span class="dashicons dashicons-code-standards"></span> Mã Nhúng Tùy Biến (Custom Tracking Scripts)
                </h3>
                <div class="dl-form-grid">
                    <div class="dl-form-group full-width">
                        <label>Mã chèn trong thẻ &lt;head&gt; (Header Scripts):</label>
                        <textarea name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[custom_header_scripts]" rows="4" style="font-family: monospace; font-size: 12px;" placeholder="<!-- Dán mã Google Ads, Facebook Pixel, TikTok Pixel... tại đây -->"><?php echo dl_val_html('custom_header_scripts', $data, $defaults); ?></textarea>
                        <p class="description">Mã này sẽ được chèn trực tiếp vào phần <code>&lt;head&gt;</code> trước khi trang web tải xong.</p>
                    </div>

                    <div class="dl-form-group full-width">
                        <label>Mã chèn ngay sau thẻ &lt;body&gt; (Body Scripts):</label>
                        <textarea name="<?php echo DragonLegend_Content_Manager::OPTION_KEY; ?>[custom_body_scripts]" rows="3" style="font-family: monospace; font-size: 12px;" placeholder="<!-- Dán mã theo dõi sau mở thẻ body tại đây -->"><?php echo dl_val_html('custom_body_scripts', $data, $defaults); ?></textarea>
                        <p class="description">Mã này sẽ được chèn ngay sau thẻ mở <code>&lt;body&gt;</code>.</p>
                    </div>
                </div>
            </div>

            <div class="dl-card-box" style="background: #f0fdf4; border-color: #bbf7d0;">
                <h3 class="dl-card-box-title" style="color: #166534;">
                    <span class="dashicons dashicons-google"></span> Trạng Thái Plugin Site Kit by Google
                </h3>
                <p style="font-size: 13px; color: #166534; margin: 0 0 12px 0;">
                    Plugin chính thức <strong>Site Kit by Google</strong> đã được cài đặt và kích hoạt thành công trên hệ thống WordPress của bạn.
                </p>
                <a href="<?php echo admin_url('admin.php?page=googlesitekit-dashboard'); ?>" class="button button-primary" style="background: #0284c7; border-color: #0369a1; font-weight: 600;">
                    🚀 Mở Bảng Điều Khiển Site Kit & Bắt Đầu Kết Nối Google
                </a>
            </div>
        </div>

        <!-- Sticky Submit Bar -->
        <div class="dl-submit-bar">
            <span class="note">
                <strong>Ghi chú:</strong> Các Menu điều hướng (Menu Chính, Menu Thông tin, Menu Quy định) được quản lý riêng tại <em>Giao diện &rarr; Menu</em>.
            </span>
            <input type="submit" name="submit" class="button button-primary dl-save-btn" value="Lưu Toàn Bộ Thay Đổi">
        </div>

    </form>
</div>

<!-- INLINE SCRIPT XỬ LÝ TRỰC TIẾP TRÁNH BỊ BROWSER CACHE -->
<script type="text/javascript">
(function($) {
    'use strict';

    // 1. Kho SVG Icon cho Tiện Nghi Nổi Bật
    var DL_ICONS_SVG = {
        'gym': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m6.5 6.5 11 11"/><path d="m21 21-1-1"/><path d="m3 3 1 1"/><path d="m18 22 4-4"/><path d="m2 6 4-4"/><path d="m3 10 7-7"/><path d="m14 21 7-7"/></svg>',
        'dumbbell': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m6.5 6.5 11 11"/><path d="m21 21-1-1"/><path d="m3 3 1 1"/><path d="m18 22 4-4"/><path d="m2 6 4-4"/><path d="m3 10 7-7"/><path d="m14 21 7-7"/></svg>',
        'spa': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3c-1.5 2.5-3 5-3 8a3 3 0 0 0 6 0c0-3-1.5-5.5-3-8Z"/><path d="M12 11c-2.5-1-5-1-7.5.5C3 13 3 16 6 18c2.5 1.5 5 0 6-7Z"/><path d="M12 11c2.5-1 5-1 7.5.5 1.5 1.5 1.5 4.5-1.5 6.5-2.5 1.5-5 0-6-7Z"/></svg>',
        'lotus': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3c-1.5 2.5-3 5-3 8a3 3 0 0 0 6 0c0-3-1.5-5.5-3-8Z"/><path d="M12 11c-2.5-1-5-1-7.5.5C3 13 3 16 6 18c2.5 1.5 5 0 6-7Z"/><path d="M12 11c2.5-1 5-1 7.5.5 1.5 1.5 1.5 4.5-1.5 6.5-2.5 1.5-5 0-6-7Z"/></svg>',
        'conference': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
        'users': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
        'shuttle': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-1.1 0-2 .9-2 2v7c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>',
        'no-smoking': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="2" y1="2" x2="22" y2="22"/><path d="M18 12H7a1 1 0 0 0-1 1v2a1 1 0 0 0 1 1h11a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1Z"/><path d="M22 12v4"/><path d="M18 8c0-2.5-2-2.5-2-5"/><path d="M22 8c0-2.5-2-2.5-2-5"/></svg>',
        'wifi': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13a10 10 0 0 1 14 0"/><path d="M8.5 16.5a5 5 0 0 1 7 0"/><path d="M2 8.82a15 15 0 0 1 20 0"/><line x1="12" y1="20" x2="12.01" y2="20"/></svg>',
        'concierge': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/><circle cx="12" cy="2" r="1"/></svg>',
        'family': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
        'restaurant': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2v20"/><path d="M21 15V2a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"/><path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/><path d="M7 2v20"/></svg>',
        'reception': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
        'elevator': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="m9 10 3-3 3 3"/><path d="m9 14 3 3 3-3"/></svg>',
        'snowflake': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="2" y1="12" x2="22" y2="12"/><line x1="12" y1="2" x2="12" y2="22"/><path d="m20 16-4-4 4-4"/><path d="m4 8 4 4-4 4"/><path d="m16 4-4 4-4-4"/><path d="m8 20 4-4 4 4"/></svg>',
        'coffee': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 8h1a4 4 0 1 1 0 8h-1"/><path d="M3 8h14v9a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4Z"/><line x1="6" y1="2" x2="6" y2="4"/><line x1="10" y1="2" x2="10" y2="4"/><line x1="14" y1="2" x2="14" y2="4"/></svg>',
        'pool': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 18c1.5 0 2.5 1 4 1s2.5-1 4-1 2.5 1 4 1 2.5-1 4-1 2.5 1 4 1"/><path d="M2 22c1.5 0 2.5 1 4 1s2.5-1 4-1 2.5 1 4 1 2.5-1 4-1 2.5 1 4 1"/><path d="m7 12 3-3 2.5 2.5"/><circle cx="17" cy="5" r="2"/><path d="m19 9-4 2-3.5-3.5"/></svg>',
        'parking': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="4"/><path d="M9 17V7h4a3 3 0 0 1 0 6H9"/></svg>',
        'tv': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="15" x="2" y="7" rx="2"/><polyline points="17 2 12 7 7 2"/></svg>',
        'bath': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6h6a2 2 0 0 1 2 2v2H7V8a2 2 0 0 1 2-2Z"/><path d="M2 12h20v4a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5v-4Z"/><path d="M4 21v1"/><path d="M20 21v1"/></svg>',
        'wine': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8 22h8"/><path d="M12 15v7"/><path d="M12 15a5 5 0 0 0 5-5c0-2-.5-4-2-8H9c-1.5 4-2 6-2 8a5 5 0 0 0 5 5Z"/></svg>',
        'shield-check': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>',
        'sparkles': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3L12 3z"/></svg>',
        'sun': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>',
        'waves': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 6c.6.5 1.2 1 2.5 1C7 7 7 5 9.5 5c2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/><path d="M2 12c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/><path d="M2 18c.6.5 1.2 1 2.5 1 2.5 0 2.5-2 5-2 2.6 0 2.4 2 5 2 2.5 0 2.5-2 5-2 1.3 0 1.9.5 2.5 1"/></svg>',
        'car': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-1.1 0-2 .9-2 2v7c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>',
        'gem': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h12l4 6-10 12L2 9Z"/><path d="M11 3 8 9l4 12 4-12-3-6"/><path d="M2 9h20"/></svg>',
        'tennis': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20"/><path d="M2 12a14.5 14.5 0 0 0 20 0"/></svg>',
        'kids': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="5" r="3"/><path d="M6.5 9a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2v-2a2 2 0 0 0-2-2Z"/><path d="m9 15 3 6 3-6"/></svg>',
        'laundry': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="20" x="3" y="2" rx="2"/><circle cx="12" cy="13" r="5"/><path d="M12 10a3 3 0 0 0-3 3"/><circle cx="7" cy="6" r="1"/></svg>',
        'billiards': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="4"/><path d="M12 10v4"/></svg>',
        'cocktail': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m8 22 4-11V2"/><path d="m16 22-4-11"/><path d="M4 2h16l-8 9-8-9Z"/></svg>',
        'bicycle': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="5.5" cy="17.5" r="3.5"/><circle cx="18.5" cy="17.5" r="3.5"/><path d="M15 6a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm-3 11.5L9 6H5m7 11.5 3-7h4.5"/></svg>',
        'charging': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="20" x="4" y="2" rx="2"/><path d="M9 11h6l-4 6v-4H8l3-6"/></svg>',
        'luggage': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="14" x="4" y="7" rx="2"/><path d="M9 7V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v3"/><path d="M4 12h16"/></svg>',
        'money': '<svg class="w-5 h-5 text-gold" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="12" x="2" y="6" rx="2"/><circle cx="12" cy="12" r="2"/><path d="M6 12h.01M18 12h.01"/></svg>'
    };

    function reindexAmenitiesTable() {
        var optKey = '<?php echo esc_js(DragonLegend_Content_Manager::OPTION_KEY); ?>';
        $('#dl-amenities-tbody tr.dl-amenity-row').each(function(idx) {
            var tr = $(this);
            tr.attr('data-index', idx);
            tr.find('.dl-row-number').text(idx + 1);
            tr.find('.dl-amenity-input-name').attr('name', optKey + '[popular_amenities][' + idx + '][name]');
            tr.find('.dl-amenity-select-icon').attr('name', optKey + '[popular_amenities][' + idx + '][icon]');
            tr.find('.dl-amenity-input-desc').attr('name', optKey + '[popular_amenities][' + idx + '][desc]');
        });
    }

    $(document).ready(function() {
        // A. Chuyển Tab
        $(document).off('click.dl_tab').on('click.dl_tab', '.dl-tab-btn', function(e) {
            e.preventDefault();
            var targetTab = $(this).data('tab');
            $('.dl-tab-btn').removeClass('active');
            $(this).addClass('active');
            $('.dl-tab-pane').removeClass('active');
            $('#' + targetTab).addClass('active');
            if (typeof localStorage !== 'undefined') {
                localStorage.setItem('dl_active_tab', targetTab);
            }
        });

        if (typeof localStorage !== 'undefined') {
            var savedTab = localStorage.getItem('dl_active_tab');
            if (savedTab && $('#' + savedTab).length) {
                $('.dl-tab-btn[data-tab="' + savedTab + '"]').trigger('click');
            }
        }

        $(document).off('click.dl_switch_tab').on('click.dl_switch_tab', '.dl-switch-tab-btn', function(e) {
            e.preventDefault();
            var targetTab = $(this).data('target');
            if (targetTab && $('.dl-tab-btn[data-tab="' + targetTab + '"]').length) {
                $('.dl-tab-btn[data-tab="' + targetTab + '"]').trigger('click');
                $('html, body').animate({ scrollTop: $('.dl-tabs-nav').offset().top - 40 }, 300);
            }
        });

        // B. Đổi Icon SVG Preview ngay lập tức khi chọn select
        $(document).off('change.dl_select_icon').on('change.dl_select_icon', '.dl-amenity-select-icon', function() {
            var val = $(this).val();
            var preview = $(this).closest('td').find('.dl-icon-preview-box');
            if (val && (val.indexOf('http://') === 0 || val.indexOf('https://') === 0 || val.indexOf('/') === 0)) {
                preview.html('<img src="' + val + '" style="width: 22px; height: 22px; object-fit: contain;">');
            } else if (DL_ICONS_SVG[val]) {
                preview.html(DL_ICONS_SVG[val]);
            } else {
                preview.html('<span class="dashicons dashicons-star-filled"></span>');
            }
        });

        // B2. Nút Tải lên Icon riêng (SVG / PNG / JPG)
        $(document).off('click.dl_upload_icon').on('click.dl_upload_icon', '.dl-btn-upload-amenity-icon', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var btn = $(this);
            var td = btn.closest('td');
            var select = td.find('.dl-amenity-select-icon');
            var preview = td.find('.dl-icon-preview-box');

            var customUploader = wp.media({
                title: 'Tải lên hoặc chọn biểu tượng Icon (SVG / PNG / JPG)',
                button: { text: 'Sử dụng biểu tượng này' },
                multiple: false,
                library: { type: 'image' }
            }).on('select', function() {
                var attachment = customUploader.state().get('selection').first().toJSON();
                var iconUrl = attachment.url;
                var fileName = attachment.filename || iconUrl.split('/').pop();

                var existingOpt = select.find('option[value="' + iconUrl + '"]');
                if (existingOpt.length) {
                    existingOpt.prop('selected', true);
                } else {
                    var newOpt = $('<option>', {
                        value: iconUrl,
                        text: '📁 File tải: ' + fileName,
                        selected: true
                    });
                    select.prepend(newOpt);
                }

                select.val(iconUrl).trigger('change');
            }).open();
        });

        // C. Nút Di chuyển lên (chỉ nhảy đúng 1 bước)
        $(document).off('click.dl_row_up').on('click.dl_row_up', '.dl-btn-row-up', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var row = $(this).closest('tr.dl-amenity-row');
            var prev = row.prev('tr.dl-amenity-row');
            if (prev.length) {
                row.insertBefore(prev);
                reindexAmenitiesTable();
                row.css('background', '#fef3c7');
                setTimeout(function() { row.css('background', ''); }, 300);
            }
        });

        // D. Nút Di chuyển xuống (chỉ nhảy đúng 1 bước)
        $(document).off('click.dl_row_down').on('click.dl_row_down', '.dl-btn-row-down', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var row = $(this).closest('tr.dl-amenity-row');
            var next = row.next('tr.dl-amenity-row');
            if (next.length) {
                row.insertAfter(next);
                reindexAmenitiesTable();
                row.css('background', '#fef3c7');
                setTimeout(function() { row.css('background', ''); }, 300);
            }
        });

        // E. Nút Xoá tiện nghi
        $(document).off('click.dl_delete_row').on('click.dl_delete_row', '.dl-btn-delete-row', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var total = $('#dl-amenities-tbody tr.dl-amenity-row').length;
            if (total <= 1) {
                alert('Cần giữ lại ít nhất 1 tiện nghi nổi bật!');
                return;
            }
            var row = $(this).closest('tr.dl-amenity-row');
            var name = row.find('.dl-amenity-input-name').val() || 'tiện nghi này';
            if (confirm('Bạn có chắc chắn muốn xoá "' + name + '" không?')) {
                row.fadeOut(200, function() {
                    $(this).remove();
                    reindexAmenitiesTable();
                });
            }
        });

        // F. Nút Thêm tiện nghi mới (chỉ thêm đúng 1 dòng)
        $(document).off('click.dl_add_amenity').on('click.dl_add_amenity', '.dl-btn-add-amenity', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var tbody = $('#dl-amenities-tbody');
            var firstRow = tbody.find('tr.dl-amenity-row').first();
            var newRow;

            if (firstRow.length) {
                newRow = firstRow.clone();
                newRow.find('.dl-amenity-input-name').val('');
                newRow.find('.dl-amenity-input-desc').val('');
                // Bỏ các option file tự tải lên ở dòng mới để tránh trùng lặp
                newRow.find('.dl-amenity-select-icon option').each(function() {
                    var val = $(this).val();
                    if (val && (val.indexOf('http://') === 0 || val.indexOf('https://') === 0 || val.indexOf('/') === 0)) {
                        $(this).remove();
                    }
                });
                newRow.find('.dl-amenity-select-icon').val('sparkles');
                newRow.find('.dl-icon-preview-box').html(DL_ICONS_SVG['sparkles'] || '✨');
            }

            if (newRow) {
                tbody.append(newRow);
                reindexAmenitiesTable();
                newRow.css('background', '#ecfdf5');
                newRow.find('.dl-amenity-input-name').focus();
                setTimeout(function() { newRow.css('background', ''); }, 400);
            }
        });

        // G. Đồng bộ re-index trước khi submit form
        $('form[action="options.php"]').on('submit', function() {
            reindexAmenitiesTable();
        });

        // H. Media Uploader cho các ô input ảnh chung
        $('.dl-upload-btn').on('click', function(e) {
            e.preventDefault();
            var button = $(this);
            var inputField = button.siblings('input[type="text"], input[type="url"]');
            var previewContainer = button.closest('.dl-form-group').find('.dl-image-preview');

            var customUploader = wp.media({
                title: 'Chọn hoặc tải ảnh lên cho website Dragon Legend',
                button: { text: 'Sử dụng ảnh này' },
                multiple: false
            }).on('select', function() {
                var attachment = customUploader.state().get('selection').first().toJSON();
                inputField.val(attachment.url).trigger('change');
                if (previewContainer.length) {
                    previewContainer.html('<img src="' + attachment.url + '" alt="Preview">');
                }
            }).open();
        });

        $('input[data-preview]').on('input change', function() {
            var val = $(this).val();
            var previewSelector = $(this).data('preview');
            var preview = $(previewSelector);
            if (preview.length) {
                if (val) {
                    preview.html('<img src="' + val + '" alt="Preview">');
                } else {
                    preview.html('<span class="placeholder">Chưa có ảnh</span>');
                }
            }
        });
    });
})(jQuery);
</script>
