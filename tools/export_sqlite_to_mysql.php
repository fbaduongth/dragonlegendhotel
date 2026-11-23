<?php
/**
 * Script to export SQLite WordPress database to MySQL SQL dump
 */

$sqlite_path = __DIR__ . '/../wp-content/database/.ht.sqlite';
if (!file_exists($sqlite_path)) {
    die("SQLite database not found at $sqlite_path\n");
}

$sqlite = new PDO("sqlite:" . $sqlite_path);
$sqlite->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

require_once __DIR__ . '/../wp-load.php';
require_once ABSPATH . 'wp-admin/includes/schema.php';

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

$out = "-- Dragon Legend Hotel MySQL Dump\n";
$out .= "-- Generated from SQLite database: wp-content/database/.ht.sqlite\n";
$out .= "-- Date: " . date('Y-m-d H:i:s') . "\n\n";
$out .= "SET NAMES utf8mb4;\n";
$out .= "SET FOREIGN_KEY_CHECKS = 0;\n";
$out .= "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n\n";

// Parse schema queries
$schema_queries = array_filter(array_map('trim', explode(';', $schema_sql)));
$create_table_map = [];
foreach ($schema_queries as $q) {
    if (preg_match('/CREATE TABLE\s+([`]?\w+[`]?)/i', $q, $m)) {
        $name = trim($m[1], '`');
        $create_table_map[$name] = $q . ';';
    }
}

function escape_sql_value($val) {
    if ($val === null) {
        return 'NULL';
    }
    if (is_int($val) || is_float($val)) {
        return (string)$val;
    }
    $val = (string)$val;
    // Check if numeric string that is safe without quotes? Better to quote strings or use addslashes/hex/standard escaping.
    // PDO quote or standard MySQL escape:
    $escaped = '';
    $len = strlen($val);
    for ($i = 0; $i < $len; $i++) {
        $c = $val[$i];
        switch ($c) {
            case "\0":
                $escaped .= '\\0';
                break;
            case "\n":
                $escaped .= '\\n';
                break;
            case "\r":
                $escaped .= '\\r';
                break;
            case '\\':
                $escaped .= '\\\\';
                break;
            case '\'':
                $escaped .= "\\'";
                break;
            case '"':
                $escaped .= '\\"';
                break;
            case "\032":
                $escaped .= '\\Z';
                break;
            default:
                $escaped .= $c;
        }
    }
    return "'" . $escaped . "'";
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

$target_file = __DIR__ . '/../dragonlegend_mysql.sql';
file_put_contents($target_file, $out);
echo "Successfully exported SQLite database to MySQL format!\n";
echo "File: $target_file\n";
echo "Size: " . round(filesize($target_file) / 1024, 2) . " KB\n";
