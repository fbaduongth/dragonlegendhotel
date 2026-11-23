<?php
/**
 * Export SQLite database to MySQL SQL dump tailored for live domain:
 * https://dragonlegendhotel.com/demo2026
 */

$sqlite_path = __DIR__ . '/../wp-content/database/.ht.sqlite';
if (!file_exists($sqlite_path)) {
    die("SQLite database not found at $sqlite_path\n");
}

$sqlite = new PDO("sqlite:" . $sqlite_path);
$sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

require_once __DIR__ . '/../wp-load.php';
require_once ABSPATH . 'wp-admin/includes/schema.php';

$from_url = 'http://localhost:8000';
$to_url   = 'https://dragonlegendhotel.com/demo2026';

function recursive_replace(&$data, $from, $to) {
    if (is_string($data)) {
        $data = str_replace($from, $to, $data);
    } elseif (is_array($data)) {
        foreach ($data as $k => &$v) {
            recursive_replace($v, $from, $to);
        }
    } elseif (is_object($data)) {
        foreach ($data as $k => &$v) {
            recursive_replace($v, $from, $to);
        }
    }
}

function safe_replace_string($str, $from, $to) {
    if (!is_string($str)) {
        return $str;
    }
    if (!is_serialized($str)) {
        return str_replace($from, $to, $str);
    }
    $unserialized = @unserialize($str);
    if ($unserialized === false && $str !== serialize(false)) {
        return str_replace($from, $to, $str);
    }
    recursive_replace($unserialized, $from, $to);
    return serialize($unserialized);
}

function escape_sql_value($val) {
    if ($val === null) {
        return 'NULL';
    }
    if (is_int($val) || is_float($val)) {
        return (string)$val;
    }
    $val = (string)$val;
    $escaped = '';
    $len = strlen($val);
    for ($i = 0; $i < $len; $i++) {
        $c = $val[$i];
        switch ($c) {
            case "\0":  $escaped .= '\\0'; break;
            case "\n":  $escaped .= '\\n'; break;
            case "\r":  $escaped .= '\\r'; break;
            case '\\':  $escaped .= '\\\\'; break;
            case '\'':  $escaped .= "\\'"; break;
            case '"':   $escaped .= '\\"'; break;
            case "\032": $escaped .= '\\Z'; break;
            default:    $escaped .= $c;
        }
    }
    return "'" . $escaped . "'";
}

$schema_sql = wp_get_db_schema();
$tables = [
    'wp_users',
    'wp_usermeta',
    'wp_termmeta',
    'wp_terms',
    'wp_term_taxonomy',
    'wp_term_relationships',
    'wp_commentmeta',
    'wp_comments',
    'wp_links',
    'wp_options',
    'wp_postmeta',
    'wp_posts'
];

$out = "-- Dragon Legend Hotel MySQL Dump for https://dragonlegendhotel.com/demo2026\n";
$out .= "-- Target Domain: $to_url\n";
$out .= "-- Date: " . date('Y-m-d H:i:s') . "\n\n";
$out .= "SET NAMES utf8mb4;\n";
$out .= "SET FOREIGN_KEY_CHECKS = 0;\n";
$out .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n\n";

$schema_queries = array_filter(array_map('trim', explode(';', $schema_sql)));
$create_table_map = [];
foreach ($schema_queries as $q) {
    if (preg_match('/CREATE TABLE\s+([`]?\w+[`]?)/i', $q, $m)) {
        $name = trim($m[1], '`');
        $create_table_map[$name] = $q . ';';
    }
}

foreach ($tables as $table) {
    $out .= "-- --------------------------------------------------------\n";
    $out .= "-- Table structure and data for `$table`\n";
    $out .= "-- --------------------------------------------------------\n";
    $out .= "DROP TABLE IF EXISTS `$table`;\n";
    if (isset($create_table_map[$table])) {
        $out .= $create_table_map[$table] . "\n\n";
    }

    $stmt = $sqlite->query("SELECT * FROM `$table`");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (!empty($rows)) {
        $columns = array_keys($rows[0]);
        $col_names = '`' . implode('`, `', $columns) . '`';
        
        $chunk_size = 50;
        $chunks = array_chunk($rows, $chunk_size);
        foreach ($chunks as $chunk) {
            $val_rows = [];
            foreach ($chunk as $row) {
                // Special handling for domain replace & sqlite plugin removal
                if ($table === 'wp_options') {
                    if ($row['option_name'] === 'active_plugins') {
                        $plugins = @unserialize($row['option_value']);
                        if (is_array($plugins)) {
                            $plugins = array_values(array_filter($plugins, function($p) {
                                return $p !== 'sqlite-database-integration/load.php';
                            }));
                            $row['option_value'] = serialize($plugins);
                        }
                    } else {
                        $row['option_value'] = safe_replace_string($row['option_value'], $from_url, $to_url);
                    }
                } elseif ($table === 'wp_posts') {
                    $row['guid'] = str_replace($from_url, $to_url, $row['guid']);
                    $row['post_content'] = str_replace($from_url, $to_url, $row['post_content']);
                } elseif ($table === 'wp_postmeta') {
                    $row['meta_value'] = safe_replace_string($row['meta_value'], $from_url, $to_url);
                } elseif ($table === 'wp_users') {
                    $row['user_url'] = str_replace($from_url, $to_url, $row['user_url']);
                }

                $vals = [];
                foreach ($row as $val) {
                    $vals[] = escape_sql_value($val);
                }
                $val_rows[] = '(' . implode(', ', $vals) . ')';
            }
            $out .= "INSERT INTO `$table` ($col_names) VALUES\n" . implode(",\n", $val_rows) . ";\n";
        }
        $out .= "\n";
    }
}

$out .= "SET FOREIGN_KEY_CHECKS = 1;\n";

$target_file = __DIR__ . '/../dragonlegend_mysql_demo2026.sql';
file_put_contents($target_file, $out);
echo "Successfully generated: $target_file\n";
echo "Size: " . round(filesize($target_file) / 1024, 2) . " KB\n";
