<?php
/**
 * The base configuration for WordPress
 *
 * @package WordPress
 */

// ** Database settings - Managed via SQLite Integration ** //
define('DB_NAME', 'dragonlegend');
define('DB_USER', 'root');
define('DB_PASSWORD', '');
define('DB_HOST', 'localhost');
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');

define('DB_DIR', __DIR__ . '/wp-content/database/');
define('DB_FILE', '.ht.sqlite');

/**#@+
 * Authentication unique keys and salts.
 * (Ẩn khóa bảo mật thực tế trên môi trường public repository - sử dụng biến môi trường hoặc thay thế khi triển khai)
 */
define('AUTH_KEY',         getenv('WP_AUTH_KEY')         ?: 'put-your-unique-auth-key-here');
define('SECURE_AUTH_KEY',  getenv('WP_SECURE_AUTH_KEY')  ?: 'put-your-unique-secure-auth-key-here');
define('LOGGED_IN_KEY',    getenv('WP_LOGGED_IN_KEY')    ?: 'put-your-unique-logged-in-key-here');
define('NONCE_KEY',        getenv('WP_NONCE_KEY')        ?: 'put-your-unique-nonce-key-here');
define('AUTH_SALT',        getenv('WP_AUTH_SALT')        ?: 'put-your-unique-auth-salt-here');
define('SECURE_AUTH_SALT', getenv('WP_SECURE_AUTH_SALT') ?: 'put-your-unique-secure-auth-salt-here');
define('LOGGED_IN_SALT',   getenv('WP_LOGGED_IN_SALT')   ?: 'put-your-unique-logged-in-salt-here');
define('NONCE_SALT',       getenv('WP_NONCE_SALT')       ?: 'put-your-unique-nonce-salt-here');

/**
 * WordPress database table prefix.
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 */
define('WP_DEBUG', false);
define('WP_DEBUG_LOG', false);
define('WP_DEBUG_DISPLAY', false);

/**
 * Local URL and Direct Filesystem
 */
define('WP_HOME', getenv('WP_HOME') ?: 'http://localhost:8000');
define('WP_SITEURL', getenv('WP_SITEURL') ?: 'http://localhost:8000');
define('FS_METHOD', 'direct');

/**
 * Cloudflare R2 - Advanced Media Offloader Credentials (Redacted for security)
 */
define('ADVMO_CLOUDFLARE_R2_KEY',      getenv('ADVMO_CLOUDFLARE_R2_KEY')      ?: 'YOUR_CLOUDFLARE_R2_ACCESS_KEY');
define('ADVMO_CLOUDFLARE_R2_SECRET',   getenv('ADVMO_CLOUDFLARE_R2_SECRET')   ?: 'YOUR_CLOUDFLARE_R2_SECRET_KEY');
define('ADVMO_CLOUDFLARE_R2_ENDPOINT', getenv('ADVMO_CLOUDFLARE_R2_ENDPOINT') ?: 'https://<ACCOUNT_ID>.r2.cloudflarestorage.com');
define('ADVMO_CLOUDFLARE_R2_BUCKET',   getenv('ADVMO_CLOUDFLARE_R2_BUCKET')   ?: 'dragonlegend-media');
define('ADVMO_CLOUDFLARE_R2_DOMAIN',   getenv('ADVMO_CLOUDFLARE_R2_DOMAIN')   ?: 'https://media.dragonlegendhotel.com');

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
