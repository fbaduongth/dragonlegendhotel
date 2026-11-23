<?php
/**
 * Engine tìm kiếm và thay thế dữ liệu CSDL WordPress
 * Tương thích cả SQLite và MySQL/MariaDB
 * Bảo toàn an toàn 100% dữ liệu Serialized và JSON
 */

if (!defined('ABSPATH')) {
    exit;
}

class DL_Search_Replace_Engine {

    /**
     * Lấy danh sách tất cả các bảng trong CSDL
     */
    public static function get_tables() {
        global $wpdb;

        $tables = $wpdb->get_col("SHOW TABLES");

        // Fallback cho SQLite thuần nếu SHOW TABLES không trả về
        if (empty($tables) && defined('DB_ENGINE') && DB_ENGINE === 'sqlite') {
            $tables = $wpdb->get_col("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
        }

        return !empty($tables) ? $tables : array();
    }

    /**
     * Lấy danh sách các cột và khóa chính (Primary Keys) của bảng
     */
    public static function get_table_columns_info($table) {
        global $wpdb;

        $columns = array();
        $primary_keys = array();

        $cols = $wpdb->get_results("DESCRIBE `{$table}`");

        if (!empty($cols)) {
            foreach ($cols as $col) {
                $columns[] = $col->Field;
                if (!empty($col->Key) && strtoupper($col->Key) === 'PRI') {
                    $primary_keys[] = $col->Field;
                }
            }
        } else {
            // Fallback SQLite PRAGMA table_info
            $table_info = $wpdb->get_results("PRAGMA table_info(`{$table}`)");
            if (!empty($table_info)) {
                foreach ($table_info as $info) {
                    $columns[] = $info->name;
                    if (!empty($info->pk)) {
                        $primary_keys[] = $info->name;
                    }
                }
            }
        }

        return array(
            'columns'      => $columns,
            'primary_keys' => $primary_keys,
        );
    }

    /**
     * Đếm tổng số dòng trong bảng
     */
    public static function get_table_row_count($table) {
        global $wpdb;
        return (int) $wpdb->get_var("SELECT COUNT(*) FROM `{$table}`");
    }

    /**
     * Thực thi tìm kiếm và thay thế
     *
     * @param array $args Cấu hình thực thi
     * @return array Kết quả chi tiết
     */
    public static function run($args = array()) {
        global $wpdb;

        $defaults = array(
            'search'           => '',
            'replace'          => '',
            'tables'           => array(),
            'dry_run'          => true,
            'case_sensitive'   => false,
            'replace_guids'    => false,
            'batch_size'       => 1000,
        );

        $params = wp_parse_args($args, $defaults);

        $search = (string) $params['search'];
        $replace = (string) $params['replace'];
        $selected_tables = (array) $params['tables'];
        $dry_run = (bool) $params['dry_run'];
        $case_sensitive = (bool) $params['case_sensitive'];
        $replace_guids = (bool) $params['replace_guids'];
        $batch_size = max(100, (int) $params['batch_size']);

        $start_time = microtime(true);

        $report = array(
            'search'          => $search,
            'replace'         => $replace,
            'dry_run'         => $dry_run,
            'total_scanned'   => 0,
            'total_updated'   => 0,
            'total_changes'   => 0,
            'tables_data'     => array(),
            'duration'        => 0,
            'errors'          => array(),
        );

        if ($search === '') {
            $report['errors'][] = 'Chuỗi tìm kiếm không được để trống.';
            return $report;
        }

        if (empty($selected_tables)) {
            $report['errors'][] = 'Vui lòng chọn ít nhất một bảng để thực thi.';
            return $report;
        }

        foreach ($selected_tables as $table) {
            $table = sanitize_text_field($table);
            $table_info = self::get_table_columns_info($table);
            $columns = $table_info['columns'];
            $primary_keys = $table_info['primary_keys'];

            if (empty($columns)) {
                continue;
            }

            $table_scanned = 0;
            $table_updated = 0;
            $table_changes = 0;

            $total_rows = self::get_table_row_count($table);
            if ($total_rows === 0) {
                $report['tables_data'][$table] = array(
                    'scanned' => 0,
                    'updated' => 0,
                    'changes' => 0,
                );
                continue;
            }

            $pages = ceil($total_rows / $batch_size);

            for ($page = 0; $page < $pages; $page++) {
                $offset = $page * $batch_size;
                $rows = $wpdb->get_results("SELECT * FROM `{$table}` LIMIT {$batch_size} OFFSET {$offset}");

                if (empty($rows)) {
                    break;
                }

                foreach ($rows as $row) {
                    $table_scanned++;
                    $row_needs_update = false;
                    $update_data = array();

                    foreach ($columns as $column) {
                        // Bỏ qua GUID nếu người dùng không chọn thay thế GUIDs
                        if (!$replace_guids && strtolower($column) === 'guid') {
                            continue;
                        }

                        $original_val = $row->$column;

                        // Chỉ xử lý nếu có dữ liệu chuỗi hoặc serialize/json
                        if (!is_string($original_val) || $original_val === '') {
                            continue;
                        }

                        // Kiểm tra sơ bộ xem chuỗi có chứa từ khóa không
                        $has_search = $case_sensitive 
                            ? (strpos($original_val, $search) !== false)
                            : (stripos($original_val, $search) !== false);

                        if (!$has_search) {
                            continue;
                        }

                        $sub_count = 0;
                        $new_val = self::recursive_replace(
                            $original_val,
                            $search,
                            $replace,
                            !$case_sensitive,
                            $sub_count
                        );

                        if ($sub_count > 0 && $new_val !== $original_val) {
                            $table_changes += $sub_count;
                            $row_needs_update = true;
                            $update_data[$column] = $new_val;
                        }
                    }

                    if ($row_needs_update) {
                        $table_updated++;

                        // Nếu không phải chạy thử (Dry Run), thực hiện update vào CSDL
                        if (!$dry_run) {
                            $where = array();
                            if (!empty($primary_keys)) {
                                foreach ($primary_keys as $pk) {
                                    $where[$pk] = $row->$pk;
                                }
                            } else {
                                // Fallback nếu bảng không có khóa chính
                                foreach ($update_data as $col => $val) {
                                    $where[$col] = $row->$col;
                                }
                            }

                            if (!empty($where)) {
                                $wpdb->update($table, $update_data, $where);
                            }
                        }
                    }
                }
            }

            $report['total_scanned'] += $table_scanned;
            $report['total_updated'] += $table_updated;
            $report['total_changes'] += $table_changes;

            $report['tables_data'][$table] = array(
                'scanned' => $table_scanned,
                'updated' => $table_updated,
                'changes' => $table_changes,
            );
        }

        $report['duration'] = round(microtime(true) - $start_time, 3);
        return $report;
    }

    /**
     * Hàm đệ quy tìm kiếm và thay thế an toàn cho Chuỗi, Mảng, Đối tượng, JSON và Serialized data
     */
    public static function recursive_replace($data, $search, $replace, $case_insensitive = true, &$count = 0) {
        if (is_string($data)) {
            // 1. Kiểm tra chuỗi PHP Serialized
            if (is_serialized($data)) {
                $unserialized = @unserialize($data);
                if ($unserialized !== false || $data === 'b:0;') {
                    $replaced = self::recursive_replace($unserialized, $search, $replace, $case_insensitive, $count);
                    return serialize($replaced);
                }
            }

            // 2. Kiểm tra chuỗi JSON
            $trimmed = trim($data);
            if ((str_starts_with($trimmed, '{') && str_ends_with($trimmed, '}')) || 
                (str_starts_with($trimmed, '[') && str_ends_with($trimmed, ']'))) {
                $json = json_decode($data, true);
                if (json_last_error() === JSON_ERROR_NONE && (is_array($json) || is_object($json))) {
                    $replaced = self::recursive_replace($json, $search, $replace, $case_insensitive, $count);
                    return json_encode($replaced, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                }
            }

            // 3. Chuỗi thông thường
            $sub_count = 0;
            if ($case_insensitive) {
                $result = str_ireplace($search, $replace, $data, $sub_count);
            } else {
                $result = str_replace($search, $replace, $data, $sub_count);
            }
            $count += $sub_count;
            return $result;

        } elseif (is_array($data)) {
            $new_array = array();
            foreach ($data as $key => $val) {
                $new_key = $key;
                if (is_string($key)) {
                    $new_key = self::recursive_replace($key, $search, $replace, $case_insensitive, $count);
                }
                $new_array[$new_key] = self::recursive_replace($val, $search, $replace, $case_insensitive, $count);
            }
            return $new_array;

        } elseif (is_object($data)) {
            $class_name = get_class($data);
            if ($class_name === '__PHP_Incomplete_Class') {
                return $data; // Không thể serialize lại class bị khuyết tật
            }

            $props = get_object_vars($data);
            foreach ($props as $key => $val) {
                $data->$key = self::recursive_replace($val, $search, $replace, $case_insensitive, $count);
            }
            return $data;
        }

        return $data;
    }
}
