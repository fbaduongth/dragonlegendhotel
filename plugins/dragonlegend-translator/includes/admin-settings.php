<?php
/**
 * Dragon Legend Auto Translator - Admin Settings View
 * Giao diện quản lý cấu hình dịch tự động trong WP-Admin
 */

if (!defined('ABSPATH')) {
    exit;
}

$options = get_option('dl_translator_options', array(
    'enabled'      => '1',
    'default_lang' => 'vi',
    'session_mode' => 'session_cookie',
));
?>
<div class="wrap" style="max-width: 1000px;">
    <h1 style="display:flex; align-items:center; gap:10px; font-weight:700; color:#0f172a;">
        <span class="dashicons dashicons-translation" style="font-size:28px; width:28px; height:28px; color:#c5a880;"></span>
        <?php esc_html_e('Cấu Hình Dịch Ngôn Ngữ Tự Động', 'dragonlegend-translator'); ?>
    </h1>
    <p class="description" style="font-size: 13px; color: #64748b; margin-bottom: 20px;">
        Hệ thống dịch tự động đa ngôn ngữ chuẩn khách sạn quốc tế, hỗ trợ 3 ngôn ngữ: <strong>Tiếng Việt (mặc định)</strong>, <strong>Tiếng Anh</strong>, <strong>Tiếng Trung</strong>. Tự động lưu phiên duyệt web và đảm bảo an toàn tuyệt đối cho thứ hạng SEO Google.
    </p>

    <!-- Info Cards Row -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap:16px; margin-bottom:24px;">
        <!-- Card 1: Engine -->
        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:18px; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
                <span style="font-size:20px;">⚡</span>
                <h3 style="margin:0; font-size:14px; font-weight:700; color:#1e293b;">Công Nghệ Dịch</h3>
            </div>
            <p style="margin:0; font-size:12px; color:#64748b; line-height:1.6;">
                Sử dụng <strong>Google Translate Element Engine</strong> dịch trực tiếp tại trình duyệt client, tốc độ dịch tức thì dưới 1 giây mà không làm chậm máy chủ.
            </p>
        </div>

        <!-- Card 2: Session Persistence -->
        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:18px; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
                <span style="font-size:20px;">🔒</span>
                <h3 style="margin:0; font-size:14px; font-weight:700; color:#1e293b;">Lưu Giữ Phiên (Session)</h3>
            </div>
            <p style="margin:0; font-size:12px; color:#64748b; line-height:1.6;">
                Khi khách hàng chọn Tiếng Anh hoặc Tiếng Trung, phiên làm việc sẽ được duy trì liên tục qua <strong>Session Cookie</strong> cho đến khi thoát website hoặc bấm chọn lại Tiếng Việt.
            </p>
        </div>

        <!-- Card 3: SEO Safe Guarantee -->
        <div style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:18px; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
            <div style="display:flex; align-items:center; gap:8px; margin-bottom:8px;">
                <span style="font-size:20px;">🛡️</span>
                <h3 style="margin:0; font-size:14px; font-weight:700; color:#1e293b;">Bảo Vệ SEO 100%</h3>
            </div>
            <p style="margin:0; font-size:12px; color:#64748b; line-height:1.6;">
                Không tạo URL ảo, không sinh tham số rác gây trùng lặp nội dung. Googlebot luôn thu thập mã HTML gốc tiếng Việt chuẩn SEO.
            </p>
        </div>
    </div>

    <!-- Main Settings Box -->
    <div style="background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:24px; box-shadow:0 1px 3px rgba(0,0,0,0.05);">
        <h2 style="font-size:16px; font-weight:700; color:#0f172a; margin-top:0; border-bottom:1px solid #f1f5f9; padding-bottom:12px;">
            Cấu Hình Trực Tiếp
        </h2>

        <form method="post" action="options.php">
            <?php settings_fields('dl_translator_group'); ?>

            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row" style="font-size:13px; font-weight:600; color:#334155;">
                        Trạng Thái Plugin
                    </th>
                    <td>
                        <label style="display:inline-flex; align-items:center; gap:8px; cursor:pointer;">
                            <input type="checkbox" name="dl_translator_options[enabled]" value="1" <?php checked('1', $options['enabled'] ?? '1'); ?> />
                            <span style="font-weight:600; color:#16a34a;">Đang kích hoạt dịch tự động</span>
                        </label>
                        <p class="description">Bật hoặc tắt toàn bộ thanh chuyển ngôn ngữ ngoài frontend.</p>
                    </td>
                </tr>

                <tr>
                    <th scope="row" style="font-size:13px; font-weight:600; color:#334155;">
                        Danh Sách Ngôn Ngữ
                    </th>
                    <td>
                        <div style="display:flex; flex-direction:column; gap:8px;">
                            <label style="display:inline-flex; align-items:center; gap:8px;">
                                <input type="checkbox" checked disabled />
                                <span style="font-size:16px;">🇻🇳</span>
                                <strong>Tiếng Việt (vi)</strong> <em style="color:#64748b;">— Ngôn ngữ gốc mặc định của website</em>
                            </label>
                            <label style="display:inline-flex; align-items:center; gap:8px;">
                                <input type="checkbox" checked disabled />
                                <span style="font-size:16px;">🇬🇧</span>
                                <strong>Tiếng Anh (en)</strong> <em style="color:#64748b;">— English</em>
                            </label>
                            <label style="display:inline-flex; align-items:center; gap:8px;">
                                <input type="checkbox" checked disabled />
                                <span style="font-size:16px;">🇨🇳</span>
                                <strong>Tiếng Trung (zh-CN)</strong> <em style="color:#64748b;">— 中文 (简体)</em>
                            </label>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th scope="row" style="font-size:13px; font-weight:600; color:#334155;">
                        Vị Trí Hiển Thị
                    </th>
                    <td>
                        <div style="background:#f8fafc; border:1px dashed #cbd5e1; border-radius:8px; padding:12px; max-width:600px;">
                            <p style="margin:0 0 6px 0; font-size:12px; font-weight:600; color:#0f172a;">
                                📍 Vị trí mặc định:
                            </p>
                            <p style="margin:0; font-size:12px; color:#475569;">
                                Trên thanh Topbar ở đỉnh trang, <strong>ngay phía sau mục Tour VR 360°</strong>:
                                <code style="display:block; margin-top:6px; background:#07111e; color:#c5a880; padding:6px 10px; border-radius:6px; font-size:11px;">
                                    Hotline: (+84) 203 3518 999 &nbsp;|&nbsp; Tour VR 360° &nbsp;|&nbsp; 🇻🇳 Tiếng Việt &nbsp;|&nbsp; 🇬🇧 Tiếng Anh &nbsp;|&nbsp; 🇨🇳 Tiếng Trung
                                </code>
                            </p>
                        </div>
                    </td>
                </tr>

                <tr>
                    <th scope="row" style="font-size:13px; font-weight:600; color:#334155;">
                        Phần Tử Miễn Trừ Dịch
                    </th>
                    <td>
                        <p style="margin:0 0 6px 0; font-size:12px; color:#64748b;">
                            Các thành phần sau đã được tự động gắn cờ <code>notranslate</code> để Google không dịch sai:
                        </p>
                        <ul style="margin:0; padding-left:18px; font-size:12px; color:#475569; line-height:1.8;">
                            <li>Tên thương hiệu khách sạn: <strong>Dragon Legend Hotel</strong></li>
                            <li>Số điện thoại hotline: <code>(+84) 203 3518 999</code></li>
                            <li>Mã giảm giá & ưu đãi: <code>DRAGON2026</code></li>
                            <li>Tên nhà phát triển website: <strong>Viettechsoft</strong></li>
                        </ul>
                    </td>
                </tr>
            </table>

            <div style="margin-top:20px; padding-top:16px; border-top:1px solid #f1f5f9;">
                <?php submit_button(__('Lưu Cấu Hình', 'dragonlegend-translator'), 'primary', 'submit', false); ?>
            </div>
        </form>
    </div>
</div>
