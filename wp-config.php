<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('WP_CACHE', true);
define( 'WPCACHEHOME', 'C:\xampp\htdocs\wp-content\plugins\wp-super-cache/' );
define( 'DB_NAME', 'wp' );

/** Database username */
define( 'DB_USER', 'saikrishna' );

/** Database password */
define( 'DB_PASSWORD', 'Saikrishna@123' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         'U7,5YrKHt~kHl|AZ;.[^dgIN&IdKZKcC=#I|drKowJcyCHl-Y:OTN;Iw|Kotz0^J' );
define( 'SECURE_AUTH_KEY',  'CWN,?_(^C=.N0ipS|!1(cAP=4pT1P |$Jm(s*O;-W;CuBDaqt.}U++:!v8c]|X7u' );
define( 'LOGGED_IN_KEY',    'tN,Ao.GDt NA(HE8 .?sMR? E6q@8Xmd#`dF>`s2)Q0r[R.s1/DvvaUnj7kYr:4N' );
define( 'NONCE_KEY',        '+}Fq^h83`v]o!Gc0bozWv1kO7p27,^Yl^E;8;_N1k~(O98^~58(.Ak.;!w{b@:nm' );
define( 'AUTH_SALT',        'Fj@Eux,xMtm.aj.S4o3cTfF+?no1V;m+,CEJD.gGY+w2Ohg<#-6*rB.&RWmVD_Gy' );
define( 'SECURE_AUTH_SALT', '^Rb]Ime!q1|L|zOrz{aUV6$9${1`_[1Dw{`E]U#PIKKcsC4YtnoiLVP>repy^2NG' );
define( 'LOGGED_IN_SALT',   '@/vLL8H3phnq&0=3<P1`l}~k|{,;,xS5I;EoRs]TJc#4 QE+%YBk]G-JS?@f]u]S' );
define( 'NONCE_SALT',       '+-Qb@rQ;JNMy?VZnIE<#an/q_G*/2Zw=Ml!I;A5FW6k1wj2I4IDD<~M<ESSm^:/B' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
