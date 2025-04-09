<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'ibexfork_wp_pvpo8' );

/** Database username */
define( 'DB_USER', 'ibexfork_wp_5csnz' );

/** Database password */
define( 'DB_PASSWORD', 'm!89$1fupJ1tT^Zf' );

/** Database hostname */
define( 'DB_HOST', 'localhost:3306' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', '1FsGwh0[F8376zB6Y|2CGA9m264Yf3jSOP7K3)Q_gfr24-79S899A-|~0|3sRzg)');
define('SECURE_AUTH_KEY', 'ra!R05Ql8k6yChY[8E*;fQ!;u9Q2r/6/*gQqHc2%__4qg%QHf~y3)Q@!2FkR:%m7');
define('LOGGED_IN_KEY', '#;DSyY9u+)1*8gSj8gW+]][03+/O4g8#so%3wF*~C[d@!Eo@2/0)z9x3I~e2_@Yn');
define('NONCE_KEY', '/KpO+@14~F@89Y7SbPUV8/L&)+2w:8v!STK2b:9Kz|jj1;4&7Gn;i37L++NXhGe+');
define('AUTH_SALT', 'O-xGX48FRTXjKa6#~Km57&[UDm51WNPx4zZ1~mm3k5VPQ9wTCP3Dv5D0fAT7F-N)');
define('SECURE_AUTH_SALT', 'L!#pgIppQ6f6RK[3N~~+1Y(uQ938Zx~~bq)_64~-:B]#F6*%EM3JWq0N1/U~fEvD');
define('LOGGED_IN_SALT', 'jt-aNmv6C942mA2b%qnh&[:|F3Z4DHV!]THF7764%vB7JQ1EH6!5C;9d65(](-/B');
define('NONCE_SALT', 'i]j9H7nGX0:#;8&9f6Z:8b474z8[3BE1@5mixQJd[]9Oy*H5]b2hjnJP(ShMIsC@');


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'egOp0966P_';


/* Add any custom values between this line and the "stop editing" line. */

//define('WP_ALLOW_MULTISITE', true);
//define('WP_AUTO_UPDATE_CORE', false);

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

//define( 'DISABLE_WP_CRON', true );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
