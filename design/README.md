# Dragon Legend Hotel (Hạ Long) - Website Redesign

Dự án thiết kế lại toàn diện giao diện website **Dragon Legend Hotel** theo phong cách **Modern Neo-Classical Luxury** (Tân cổ điển hoàng gia hiện đại), kết hợp UI toolkit tiên tiến, hiệu ứng animation sinh động và hệ thống đặt phòng tương tác cao cấp.

---

## 🌟 Những Điểm Đột Phá & Tính Năng Nổi Bật

### 1. Thẩm mỹ & Nhận diện thương hiệu (Design System)
- **Tông màu chủ đạo**:
  - `Royal Champagne Gold` (`#C5A880`, `#D4AF37`): Tượng trưng cho sự sang trọng, quý phái.
  - `Ocean Deep Navy` (`#0B1A2D`, `#07111E`): Chiều sâu của vịnh biển kỳ quan di sản.
  - `Pearl White & Cream` (`#FAF8F5`, `#F4EFE7`): Cảm giác ấm cúng, thư thái.
- **Nghệ thuật Typography**:
  - `Playfair Display`: Tiêu đề sang trọng chuẩn phong cách khách sạn & resort 4-5 sao.
  - `Plus Jakarta Sans`: Nội dung chi tiết rõ nét, hiện đại, dễ đọc trên mọi thiết bị.
  - `Cormorant Garamond`: Nét chấm phá thanh thoát quý tộc.

### 2. UI Toolkit & Animations
- **Tailwind CSS**: Kiến trúc CSS utility-first tối ưu, tải trang siêu tốc.
- **AOS (Animate On Scroll)**: Hiệu ứng xuất hiện mượt mà khi cuộn trang (Fade Up, Zoom, Slide).
- **Swiper.js Carousel**: Băng chuyền vuốt chạm mượt mà cho các gói Combo ưu đãi và Đánh giá khách hàng.
- **Glassmorphism**: Hiệu ứng kính mờ cao cấp với `backdrop-filter: blur(16px)` cho Header và Booking Bar.
- **Micro-Interactions**: Hiệu ứng phát sáng ánh kim (Gold Shimmer Sheen), sóng lan tỏa Hotline (Hotline Ripple), bộ đếm số liệu nhảy tự động (138+ phòng, 4.8★, tầng 12...).

### 3. Bộ công cụ Đặt phòng thông minh (Booking Engine)
- **Quick Floating Booking Bar**: Lựa chọn ngày Check-in/Check-out tự động tính số đêm lưu trú, popup đếm số lượng phòng và khách.
- **Bộ lọc phòng tương tác**: Chuyển đổi linh hoạt giữa các hạng phòng (*Superior, Deluxe, Junior Suite, Family Suite*).
- **Modal chi tiết phòng**: Xem kích thước (m²), loại giường, tầm nhìn và đầy đủ tiện ích đi kèm.
- **Hệ thống tính giá tức thì**: Tự động tính toán tổng tiền, chiết khấu voucher (ví dụ: mã `DRAGON2026` giảm ngay 10%), thuế VAT & phí phục vụ 8%.
- **Hộp thoại xác nhận**: Cung cấp mã đặt phòng tự sinh (ví dụ `DLH-894215`) và hướng dẫn nhận phòng.

### 4. Trải nghiệm đa phương tiện
- **Tour thực tế ảo VR 360°**: Cửa sổ tương tác 360 độ khám phá toàn cảnh khách sạn, hồ bơi và phòng nghỉ.
- **Bản đồ Google Maps & Thước đo vị trí**: Khoảng cách đến Sun World, bãi tắm Bãi Cháy, Chợ đêm và cảng tàu du thuyền.
- **Concierge Hub góc màn hình**: Hotline nhấp nháy, chat Messenger và nút Back to top mượt mà.

---

## 📁 Cấu trúc thư mục

```
e:\PROJECT\DragonLegendHotel\
├── index.html          # Trang chủ hoàn chỉnh, chuẩn SEO & tương thích di động
├── css\
│   └── style.css       # Custom luxury CSS, hiệu ứng ánh kim, biến màu, keyframes
├── js\
│   ├── data.js         # Dữ liệu phòng, gói ưu đãi, tiện ích, đánh giá khách hàng
│   ├── booking.js      # Logic chọn ngày, stepper khách/phòng, tính giá, voucher
│   └── main.js         # Quản lý sticky header, mobile drawer, Swiper, AOS, bộ đếm số
├── assets\             # Thư mục chứa hình ảnh & biểu tượng bổ sung
├── server.js           # Máy chủ tĩnh Node.js chạy thử cục bộ
└── README.md           # Tài liệu hướng dẫn dự án
```

---

## 🚀 Cách mở và trải nghiệm website

### Cách 1: Khởi chạy máy chủ cục bộ (Khuyên dùng)
Mở PowerShell hoặc Terminal tại thư mục dự án và chạy:
```powershell
agy-node server.js
```
Sau đó mở trình duyệt và truy cập: **`http://localhost:3000`**

### Cách 2: Mở trực tiếp file HTML
Bạn có thể nhấp đúp trực tiếp vào file **`index.html`** hoặc kéo thả file vào Google Chrome, Microsoft Edge, Safari... để xem ngay giao diện.
