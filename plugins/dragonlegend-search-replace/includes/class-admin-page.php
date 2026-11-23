<?php
/**
 * Giao diện Quản trị cho Plugin Tìm kiếm & Thay thế CSDL
 */

if (!defined('ABSPATH')) {
    exit;
}

class DL_Search_Replace_Admin {

    public static function init() {
        add_action('admin_menu', array(__CLASS__, 'register_admin_menu'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_assets'));
    }

    public static function register_admin_menu() {
        add_management_page(
            'Tìm kiếm & Thay thế DB',
            'Tìm & Thay thế DB',
            'manage_options',
            'dl-search-replace',
            array(__CLASS__, 'render_admin_page')
        );
    }

    public static function enqueue_assets($hook) {
        if ($hook !== 'tools_page_dl-search-replace') {
            return;
        }

        wp_enqueue_style(
            'dl-search-replace-css',
            DL_SR_URL . 'assets/css/search-replace.css',
            array(),
            DL_SR_VERSION
        );

        wp_enqueue_script(
            'dl-search-replace-js',
            DL_SR_URL . 'assets/js/search-replace.js',
            array('jquery'),
            DL_SR_VERSION,
            true
        );
    }

    public static function render_admin_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('Bạn không có quyền truy cập trang này.', 'dragonlegend-search-replace'));
        }

        global $wpdb;
        $all_tables = DL_Search_Replace_Engine::get_tables();
        $report = null;

        // Xử lý khi submit form
        if (isset($_POST['dl_sr_nonce']) && wp_verify_nonce($_POST['dl_sr_nonce'], 'dl_sr_action')) {
            $search = isset($_POST['search_string']) ? wp_unslash($_POST['search_string']) : '';
            $replace = isset($_POST['replace_string']) ? wp_unslash($_POST['replace_string']) : '';
            $tables = isset($_POST['tables']) ? (array) $_POST['tables'] : array();
            $dry_run = isset($_POST['dry_run']) && $_POST['dry_run'] === '1';
            $case_sensitive = isset($_POST['case_sensitive']) && $_POST['case_sensitive'] === '1';
            $replace_guids = isset($_POST['replace_guids']) && $_POST['replace_guids'] === '1';

            $report = DL_Search_Replace_Engine::run(array(
                'search'         => $search,
                'replace'        => $replace,
                'tables'         => $tables,
                'dry_run'        => $dry_run,
                'case_sensitive' => $case_sensitive,
                'replace_guids'  => $replace_guids,
            ));
        }

        // Lấy thông tin số dòng của từng bảng
        $tables_info = array();
        foreach ($all_tables as $tbl) {
            $tables_info[$tbl] = DL_Search_Replace_Engine::get_table_row_count($tbl);
        }

        $current_home = get_option('home');
        $db_engine = defined('DB_ENGINE') ? strtoupper(DB_ENGINE) : 'MYSQL';

        ?>
        <div class="wrap dl-sr-wrap">
            <div class="dl-sr-header">
                <div class="dl-sr-title-area">
                    <h1 class="dl-sr-title">
                        <span class="dashicons dashicons-database-export"></span>
                        Tìm kiếm & Thay thế Cơ Sở Dữ Liệu
                    </h1>
                    <p class="dl-sr-subtitle">
                        Công cụ tìm kiếm & thay thế chuyên sâu cho WordPress. Tự động bảo toàn cấu trúc mảng tuần tự (PHP serialized) và JSON, hỗ trợ hoàn hảo cả <strong><?php echo esc_html($db_engine); ?></strong>.
                    </p>
                </div>
                <div class="dl-sr-badges">
                    <span class="dl-badge dl-badge-db"><span class="dashicons dashicons-database"></span> <?php echo esc_html($db_engine); ?> DB</span>
                    <span class="dl-badge dl-badge-safe"><span class="dashicons dashicons-shield"></span> Serialized Safe</span>
                </div>
            </div>

            <!-- BÁO CÁO KẾT QUẢ NẾU VỪA CHẠY -->
            <?php if ($report !== null): ?>
                <?php if (!empty($report['errors'])): ?>
                    <div class="dl-sr-alert dl-alert-error">
                        <span class="dashicons dashicons-warning"></span>
                        <div>
                            <strong>Có lỗi xảy ra:</strong>
                            <ul>
                                <?php foreach ($report['errors'] as $err): ?>
                                    <li><?php echo esc_html($err); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="dl-sr-alert <?php echo $report['dry_run'] ? 'dl-alert-info' : 'dl-alert-success'; ?>">
                        <span class="dashicons <?php echo $report['dry_run'] ? 'dashicons-info' : 'dashicons-yes-alt'; ?>"></span>
                        <div>
                            <h3 class="dl-alert-heading">
                                <?php if ($report['dry_run']): ?>
                                    🔍 Kết Quả Chạy Thử Nghiệm (Dry Run)
                                <?php else: ?>
                                    ✅ Đã Thực Thi Cập Nhật Thành Công Vào CSDL!
                                <?php endif; ?>
                            </h3>
                            <p>
                                Tìm chuỗi: <code><?php echo esc_html($report['search']); ?></code> → Thay bằng: <code><?php echo esc_html($report['replace']); ?></code>
                            </p>
                            <div class="dl-stats-grid">
                                <div class="dl-stat-box">
                                    <div class="dl-stat-num"><?php echo number_format($report['total_scanned']); ?></div>
                                    <div class="dl-stat-label">Dòng đã quét</div>
                                </div>
                                <div class="dl-stat-box">
                                    <div class="dl-stat-num"><?php echo number_format($report['total_updated']); ?></div>
                                    <div class="dl-stat-label"><?php echo $report['dry_run'] ? 'Dòng sẽ thay đổi' : 'Dòng đã cập nhật'; ?></div>
                                </div>
                                <div class="dl-stat-box">
                                    <div class="dl-stat-num"><?php echo number_format($report['total_changes']); ?></div>
                                    <div class="dl-stat-label">Số vị trí phát hiện</div>
                                </div>
                                <div class="dl-stat-box">
                                    <div class="dl-stat-num"><?php echo esc_html($report['duration']); ?>s</div>
                                    <div class="dl-stat-label">Thời gian chạy</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bảng chi tiết từng Table -->
                    <div class="dl-sr-card dl-mb-20">
                        <h3 class="dl-card-title"><span class="dashicons dashicons-list-view"></span> Chi tiết theo từng bảng</h3>
                        <table class="dl-table-results">
                            <thead>
                                <tr>
                                    <th>Tên Bảng</th>
                                    <th>Số dòng quét</th>
                                    <th>Số dòng khớp</th>
                                    <th>Số lần xuất hiện</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($report['tables_data'] as $tbl_name => $tbl_stat): ?>
                                    <tr class="<?php echo $tbl_stat['changes'] > 0 ? 'dl-row-highlight' : ''; ?>">
                                        <td><strong><?php echo esc_html($tbl_name); ?></strong></td>
                                        <td><?php echo number_format($tbl_stat['scanned']); ?></td>
                                        <td><?php echo number_format($tbl_stat['updated']); ?></td>
                                        <td><strong><?php echo number_format($tbl_stat['changes']); ?></strong></td>
                                        <td>
                                            <?php if ($tbl_stat['changes'] > 0): ?>
                                                <span class="dl-tag dl-tag-active"><?php echo $report['dry_run'] ? 'Tìm thấy' : 'Đã sửa'; ?></span>
                                            <?php else: ?>
                                                <span class="dl-tag dl-tag-empty">Không có</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <!-- FORM TÌM KIẾM & THAY THẾ -->
            <form method="post" action="" id="dl-sr-form" class="dl-sr-form-grid">
                <?php wp_nonce_field('dl_sr_action', 'dl_sr_nonce'); ?>

                <div class="dl-sr-card">
                    <h2 class="dl-card-title"><span class="dashicons dashicons-search"></span> Thiết lập Từ khóa</h2>

                    <!-- Gợi ý nhanh cho đổi Domain -->
                    <div class="dl-preset-box">
                        <span class="dashicons dashicons-lightbulb"></span>
                        <div class="dl-preset-content">
                            <strong>Gợi ý chuyển tên miền Local lên Server:</strong>
                            <p>Nhấp vào đây để tự động điền URL Local hiện tại (<code><?php echo esc_html($current_home); ?></code>) vào ô Tìm kiếm:</p>
                            <button type="button" class="button button-small dl-preset-btn" data-url="<?php echo esc_attr($current_home); ?>">
                                Điền URL Local vào Tìm kiếm
                            </button>
                        </div>
                    </div>

                    <div class="dl-form-group">
                        <label for="search_string" class="dl-label">Tìm kiếm chuỗi này (Search for) <span class="dl-required">*</span></label>
                        <input type="text" id="search_string" name="search_string" class="regular-text dl-input-full" placeholder="Ví dụ: http://localhost:8000" value="<?php echo isset($_POST['search_string']) ? esc_attr($_POST['search_string']) : ''; ?>" required />
                        <p class="description">Chuỗi ký tự hoặc đường dẫn URL bạn muốn tìm trong cơ sở dữ liệu.</p>
                    </div>

                    <div class="dl-form-group">
                        <label for="replace_string" class="dl-label">Thay thế bằng chuỗi này (Replace with)</label>
                        <input type="text" id="replace_string" name="replace_string" class="regular-text dl-input-full" placeholder="Ví dụ: https://dragonlegendhotel.com" value="<?php echo isset($_POST['replace_string']) ? esc_attr($_POST['replace_string']) : ''; ?>" />
                        <p class="description">Chuỗi ký tự hoặc đường dẫn mới sẽ thay thế cho chuỗi cũ.</p>
                    </div>

                    <div class="dl-form-group">
                        <label class="dl-label">Tuỳ chọn an toàn & nâng cao</label>
                        <div class="dl-options-list">
                            <label class="dl-checkbox-item dl-dry-run-label">
                                <input type="checkbox" name="dry_run" id="dl-dry-run" value="1" <?php checked(!isset($_POST['dry_run']) || $_POST['dry_run'] === '1'); ?> />
                                <div>
                                    <strong>Chạy thử nghiệm (Dry Run) — Khuyến nghị chạy trước</strong>
                                    <p class="description">Chỉ quét và xem trước báo cáo số lượng bản ghi tìm thấy mà KHÔNG ghi đè bất kỳ thay đổi nào vào CSDL.</p>
                                </div>
                            </label>

                            <label class="dl-checkbox-item">
                                <input type="checkbox" name="case_sensitive" value="1" <?php checked(isset($_POST['case_sensitive']) && $_POST['case_sensitive'] === '1'); ?> />
                                <div>
                                    <strong>Phân biệt chữ hoa / chữ thường (Case-sensitive)</strong>
                                    <p class="description">Mặc định không phân biệt chữ hoa/thường để tìm kiếm bao quát hơn.</p>
                                </div>
                            </label>

                            <label class="dl-checkbox-item">
                                <input type="checkbox" name="replace_guids" value="1" <?php checked(isset($_POST['replace_guids']) && $_POST['replace_guids'] === '1'); ?> />
                                <div>
                                    <strong>Thay thế cả cột GUIDs trong bảng wp_posts</strong>
                                    <p class="description">Nên chọn khi bạn di chuyển website sang domain mới hoàn toàn.</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="dl-form-actions">
                        <button type="submit" id="dl-submit-btn" class="button button-primary button-hero dl-btn-execute">
                            <span class="dashicons dashicons-controls-play"></span>
                            <span id="dl-btn-text">Bắt đầu Chạy Thử (Dry Run)</span>
                        </button>
                    </div>
                </div>

                <!-- CỘT CHỌN BẢNG -->
                <div class="dl-sr-card">
                    <div class="dl-table-header-flex">
                        <h2 class="dl-card-title"><span class="dashicons dashicons-editor-table"></span> Chọn các Bảng (Tables)</h2>
                        <div class="dl-table-quick-actions">
                            <button type="button" class="button button-small" id="dl-select-all">Chọn tất cả</button>
                            <button type="button" class="button button-small" id="dl-select-none">Bỏ chọn</button>
                        </div>
                    </div>
                    <p class="description dl-mb-15">Chọn các bảng cần quét tìm kiếm. Mặc định đã chọn sẵn tất cả các bảng dữ liệu WordPress.</p>

                    <div class="dl-tables-checklist">
                        <?php foreach ($all_tables as $tbl): 
                            $row_count = isset($tables_info[$tbl]) ? $tables_info[$tbl] : 0;
                            $is_wp_table = str_starts_with($tbl, $wpdb->prefix);
                            $checked = (!isset($_POST['tables']) && $is_wp_table) || (isset($_POST['tables']) && in_array($tbl, (array) $_POST['tables']));
                        ?>
                            <label class="dl-table-row-item">
                                <input type="checkbox" name="tables[]" value="<?php echo esc_attr($tbl); ?>" class="dl-table-checkbox" <?php checked($checked); ?> />
                                <span class="dl-tbl-name"><?php echo esc_html($tbl); ?></span>
                                <span class="dl-tbl-rows"><?php echo number_format($row_count); ?> dòng</span>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <!-- Hộp cảnh báo an toàn -->
                    <div class="dl-safety-note">
                        <span class="dashicons dashicons-shield-alt"></span>
                        <div>
                            <strong>Lưu ý sao lưu dữ liệu:</strong>
                            <p>Trước khi thực hiện chạy thật (bỏ tích Chạy thử nghiệm), hãy đảm bảo bạn đã sao lưu file database (file <code>wp-content/database/.ht.sqlite</code> đối với SQLite hoặc xuất SQL đối với MySQL).</p>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <?php
    }
}
