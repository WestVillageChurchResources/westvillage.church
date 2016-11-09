<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('WP_CACHE', true); //Added by WP-Cache Manager
define( 'WPCACHEHOME', '/nfs/c09/h01/mnt/209486/domains/sandbox.westvillage.church/html/wp-content/plugins/wp-super-cache/' ); //Added by WP-Cache Manager
define('DB_NAME', 'db209486_wordpress');

/** MySQL database username */
define('DB_USER', '1clk_wp_2aaCTSF');

/** MySQL database password */
define('DB_PASSWORD', '5JQOhwWY');

/** MySQL hostname */
define('DB_HOST', $_ENV{DATABASE_SERVER});

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'xqNjgMQD qiZiV2Ho 4oSSQuJ2 K85lF2Ku sl7YxlZn');
define('SECURE_AUTH_KEY',  '7GaBkPK7 Sn7e7DF2 rdLIy5Ej jZiOaSSz hTak5ss6');
define('LOGGED_IN_KEY',    '3Ms1Nki2 kmvBxIHf 5gs1XHPv pk8X8VEw hQqUcJkF');
define('NONCE_KEY',        'JsvQuffR on1DVy6x o8wotPnx 7Lya66Mz afmoQSQj');
define('AUTH_SALT',        '21xil5Wp qF1Shwna aZcgcA7b rFSzP3El Xn4ZMR55');
define('SECURE_AUTH_SALT', 'QGJvHqem aPSpsc4r 4DoHoGmw 4GcYWwrg XPGsjuKO');
define('LOGGED_IN_SALT',   'UMxngHfs hN3oR4KX bPlGHlvn 4QISn5k7 c4EPKgwY');
define('NONCE_SALT',       'ZI3UUlrz zccqlhu2 GdAOXfxr r5SRL5qG U1qViLlM');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'mainwp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
