# Hướng Dẫn Sử Dụng & Mở Rộng Hệ Thống WordPress Dragon Legend Hotel

Chào mừng bạn đến với dự án thiết kế lại website **Dragon Legend Hotel (Hạ Long)** trên nền tảng **WordPress**. 

Hệ thống được xây dựng theo tiêu chuẩn kiến trúc hiện đại, phân tách hoàn toàn giữa **Theme (Giao diện hiển thị)** và **Plugin (Tính năng nghiệp vụ, Quản trị dữ liệu & Metaboxes)**.

---

## ⚡ 1. Khởi Động Nhanh Máy Chủ Local Trên Máy Tính

Hệ thống đã được tích hợp sẵn máy chủ **FrankenPHP (PHP 8.5) kèm SQLite** trực tiếp trong thư mục dự án, không cần cài đặt thêm MySQL, Apache hay Docker.

- **Khởi động website**: Mở Terminal tại thư mục dự án và chạy:
  ```bash
  ./start-server.sh
  ```
- **Truy cập website**:
  - Trang chủ giao diện: **[http://localhost:8000](http://localhost:8000)**
  - Trang Quản trị: **[http://localhost:8000/wp-admin](http://localhost:8000/wp-admin)**
  - Tài khoản quản trị: **`admin`** | Mật khẩu: **`********`** *(Thiết lập qua biến môi trường `WP_ADMIN_PASS`)*
- **Dừng máy chủ**:
  ```bash
  ./stop-server.sh
  ```

---

## 📁 2. Cấu Trúc Dự Án

```
DragonLegendHotel/
├── dragonlegend-theme.zip       # Gói cài đặt Theme (Cài qua WP-Admin)
├── dragonlegend-core.zip        # Gói cài đặt Core Plugin (Cài qua WP-Admin)
│
├── themes/
│   └── dragonlegend/            # Mã nguồn giao diện chính
│       ├── style.css            # Khai báo theme & style cơ bản
│       ├── functions.php        # Khởi tạo theme, enqueues, ajax localize, helpers
│       ├── header.php           # Header, Schema JSON-LD 4-sao, Navigation bar
│       ├── footer.php           # Footer, AJAX Booking Modal, VR 360 Modal
│       ├── front-page.php       # Trang chủ động WP_Query
│       ├── single-dl_room.php   # Chi tiết từng hạng phòng nghỉ
│       ├── archive-dl_room.php  # Danh sách tất cả hạng phòng
│       ├── single-dl_offer.php  # Chi tiết gói ưu đãi & combo
│       ├── page.php, single.php, index.php, 404.php
│       └── assets/              # CSS, JS (booking.js, main.js)
│
├── plugins/
│   └── dragonlegend-core/       # Mã nguồn Plugin nghiệp vụ
│       ├── dragonlegend-core.php# Entry file, hooks kích hoạt
│       └── includes/
│           ├── class-post-types.php     # Đăng ký CPTs & Taxonomy
│           ├── class-metaboxes.php      # Hệ thống Metabox OOP & Hook mở rộng
│           ├── class-coupon-manager.php # Quản lý mã giảm giá (DRAGON2026...)
│           ├── class-booking-handler.php# AJAX nhận đơn, tính tiền, gửi email
│           └── sample-data.php          # Dữ liệu mẫu 4 phòng, 3 ưu đãi, 4 dịch vụ
│
└── design/                      # Bản vẽ giao diện mẫu gốc (HTML/CSS/JS)
```

---

## 🚀 2. Hướng Dẫn Cài Đặt Lên Website WordPress

### Cách 1: Cài đặt qua giao diện WP-Admin (Khuyên dùng)
1. Đăng nhập vào trang quản trị WordPress (`/wp-admin`).
2. **Cài đặt Plugin**:
   - Vào menu **Gói mở rộng (Plugins)** &rarr; **Cài mới (Add New)** &rarr; **Tải gói mở rộng lên (Upload Plugin)**.
   - Chọn file `dragonlegend-core.zip` và bấm **Cài đặt ngay (Install Now)**.
   - Bấm **Kích hoạt (Activate)**.
   > *Ngay khi kích hoạt, plugin sẽ tự động nạp sẵn dữ liệu mẫu (4 hạng phòng, 3 gói ưu đãi, 4 dịch vụ, 4 đánh giá) nếu website chưa có dữ liệu.*
3. **Cài đặt Theme**:
   - Vào menu **Giao diện (Appearance)** &rarr; **Giao diện (Themes)** &rarr; **Thêm mới (Add New)** &rarr; **Tải giao diện lên (Upload Theme)**.
   - Chọn file `dragonlegend-theme.zip` và bấm **Cài đặt ngay (Install Now)**.
   - Bấm **Kích hoạt (Activate)**.
4. **Cài đặt Trang chủ**:
   - Vào **Cài đặt (Settings)** &rarr; **Đọc (Reading)**.
   - Tại mục *Bố cục trang chủ*, chọn **Một trang tĩnh (A static page)**.
   - Chọn trang chủ sử dụng template **Front Page Template** (hoặc tạo một trang mới có tên "Trang chủ" và gán mẫu này).

### Cách 2: Copy trực tiếp vào thư mục mã nguồn
- Copy thư mục `plugins/dragonlegend-core` vào `wp-content/plugins/`
- Copy thư mục `themes/dragonlegend` vào `wp-content/themes/`
- Truy cập WP-Admin kích hoạt Plugin và Theme.

---

## 🛠️ 3. Quản Trị Hệ Thống Trong WP-Admin

Sau khi kích hoạt Plugin, thanh menu bên trái WP-Admin sẽ xuất hiện các mục quản lý chuyên biệt:

### 3.1. Quản lý Hạng Phòng (`dl_room`)
- Quản lý các hạng phòng: *Superior Double/Twin, Deluxe Ocean, Junior Suite, Family Grand Suite...*
- **Metaboxes hỗ trợ sẵn:**
  - Mô tả ngắn / Slogan phòng
  - Giá phòng / đêm & Giá gốc niêm yết (VNĐ)
  - Diện tích phòng (m²)
  - Loại giường & Sức chứa tối đa
  - Tầm nhìn / View
  - Huy hiệu nổi bật (*Phổ biến nhất, Bán chạy, Thượng lưu...*)
  - Điểm đánh giá sao & Lượt review
  - Tiện nghi phòng (Checklist trực quan)
  - Thư viện ảnh phòng (Gallery Media Uploader)

### 3.2. Quản lý Gói Ưu Đãi & Khuyến Mãi (`dl_offer`)
- Quản lý các combo: *Summer Deal, Early Bird, MICE Gala...*
- **Metaboxes hỗ trợ:**
  - Tag ưu đãi (*Ưu đãi mùa hè, Tiết kiệm thông minh...*)
  - Huy hiệu giảm giá (*Best Seller, Giảm 20%...*)
  - Giá hiển thị (*Chỉ từ 690.000 VNĐ / Khách*)
  - Thời hạn áp dụng
  - Danh sách quyền lợi đi kèm (Nhập mỗi dòng một quyền lợi)

### 3.3. Quản lý Dịch Vụ & Tiện Ích Khách Sạn (`dl_amenity`)
- Quản lý: *Hồ bơi vô cực & Gym, Nhà hàng Legend Gourmet, Grand Ballroom, Lotus Spa...*
- **Metaboxes hỗ trợ:**
  - Vị trí / Tầng (*Tầng 12, Tầng 2 & M...*)
  - Điểm nổi bật (Highlights)
  - Mã Icon SVG (*pool, restaurant, ballroom, lotus-spa*)

### 3.4. Quản lý Đánh Giá Từ Khách Hàng (`dl_testimonial`)
- Quản lý các phản hồi thực tế từ du khách.
- **Metaboxes hỗ trợ:**
  - Quốc gia / Tỉnh thành (*Canada, Nam Định, Hà Nội...*)
  - Số sao đánh giá (1 - 5)
  - Thời gian lưu trú (*Tháng 2, 2026*)
  - Tiêu đề nhận xét (*Vượt qua sự mong đợi của tôi!*)

### 3.5. Quản lý Đơn Đặt Phòng (`dl_booking`)
- Khi du khách điền form đặt phòng tại popup website:
  - Hệ thống tự động tính tiền, áp dụng voucher giảm giá, cộng thuế/phí phục vụ (8%).
  - Tự động sinh mã đặt phòng dạng `DLH-XXXXXX` và lưu thành một bài viết trong **Đơn Đặt Phòng**.
  - Gửi email thông báo tự động đến bộ phận lễ tân khách sạn và gửi email xác nhận cho khách hàng.
  - Quản trị viên vào xem chi tiết đơn và cập nhật trạng thái: **Chờ xác nhận &rarr; Đã xác nhận &rarr; Đã hoàn thành &rarr; Đã hủy**.

---

## 👨‍💻 4. Hướng Dẫn Lập Trình Viên Mở Rộng Metabox (Developer Guide)

Kiến trúc `DragonLegend_Metaboxes` cung cấp các Filter Hooks mạnh mẽ. Bạn có thể mở rộng bất kỳ trường nào trong file `functions.php` của theme hoặc trong một plugin con mà không phải chỉnh sửa mã nguồn gốc của Core Plugin!

### 4.1. Thêm trường mới vào Hạng Phòng (Room)
Sử dụng filter `dragonlegend_room_metabox_fields`:

```php
/**
 * Thêm các trường tùy biến vào Hạng phòng Dragon Legend Hotel
 */
add_filter('dragonlegend_room_metabox_fields', function($fields, $post_id) {
    // 1. Thêm trường chọn hướng ban công
    $fields['_dl_room_balcony_direction'] = array(
        'label'       => 'Hướng ban công đón nắng',
        'type'        => 'select',
        'options'     => array(
            'dong' => 'Hướng Đông (Đón bình minh)',
            'tay'  => 'Hướng Tây (Ngắm hoàng hôn)',
            'nam'  => 'Hướng Nam (Gió biển mát mẻ)',
        ),
        'description' => 'Chọn hướng chính của ban công phòng.',
    );

    // 2. Thêm trường số tầng áp dụng
    $fields['_dl_room_floor_range'] = array(
        'label'       => 'Khoảng tầng',
        'type'        => 'text',
        'description' => 'Ví dụ: Tầng 5 đến Tầng 10',
    );

    // 3. Thêm ghi chú nội bộ cho buồng phòng
    $fields['_dl_room_housekeeping_notes'] = array(
        'label'       => 'Ghi chú buồng phòng (Nội bộ)',
        'type'        => 'textarea',
        'rows'        => 3,
        'description' => 'Chỉ hiển thị cho nhân viên quản lý.',
    );

    return $fields;
}, 10, 2);
```

### 4.2. Thêm tiện nghi mới vào danh sách checklist
Sử dụng filter `dragonlegend_standard_amenities`:

```php
add_filter('dragonlegend_standard_amenities', function($amenities) {
    $amenities['jacuzzi']     = 'Bồn sục massage nước nóng Jacuzzi';
    $amenities['kitchen']     = 'Bếp mini nấu ăn gia đình';
    $amenities['soundbar']    = 'Dàn âm thanh vòm cao cấp';
    return $amenities;
});
```

### 4.3. Thêm mã khuyến mãi / Voucher mới
Sử dụng filter `dragonlegend_promo_codes`:

```php
add_filter('dragonlegend_promo_codes', function($coupons) {
    $coupons['TET2027'] = array(
        'percent' => 25,
        'label'   => 'Ưu đãi đặc biệt Tết 2027 - Giảm 25%',
        'active'  => true
    );
    return $coupons;
});
```

### 4.4. Đăng ký một Meta Box hoàn toàn mới
Sử dụng action `dragonlegend_add_meta_boxes` và `dragonlegend_save_meta_box`:

```php
// Đăng ký Metabox mới
add_action('dragonlegend_add_meta_boxes', function() {
    add_meta_box(
        'dl_room_360_tour',
        'Liên Kết Tour 360 Riêng Của Phòng',
        'my_custom_room_360_callback',
        'dl_room',
        'side',
        'default'
    );
});

function my_custom_room_360_callback($post) {
    $url = get_post_meta($post->ID, '_dl_room_vr_url', true);
    ?>
    <p>Nhập URL tour 360 độ riêng cho căn phòng này:</p>
    <input type="url" name="_dl_room_vr_url" value="<?php echo esc_url($url); ?>" class="widefat">
    <?php
}

// Lưu dữ liệu Metabox mới
add_action('dragonlegend_save_meta_box', function($post_id, $post) {
    if (isset($_POST['_dl_room_vr_url'])) {
        update_post_meta($post_id, '_dl_room_vr_url', esc_url_raw($_POST['_dl_room_vr_url']));
    }
}, 10, 2);
```

---

## 🛡️ 5. An Toàn & Bảo Mật
- Mọi thao tác form đều được bảo vệ bằng **WordPress Nonces** (`wp_verify_nonce` / `check_ajax_referer`).
- Kiểm tra quyền hạn người dùng trước khi lưu (`current_user_can('edit_post')`).
- Dữ liệu đầu vào được sanitize nghiêm ngặt: `sanitize_text_field`, `sanitize_textarea_field`, `sanitize_email`, `intval`, `floatval`.
- Dữ liệu đầu ra được escape đầy đủ: `esc_html`, `esc_attr`, `esc_url`.
