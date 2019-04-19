<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'shop_togetherwebake_org');

/** MySQL database username */
define('DB_USER', 'shoptogetherweba');

/** MySQL database password */
define('DB_PASSWORD', 'LHA*cZmA');

/** MySQL hostname */
define('DB_HOST', 'mysql.shop.togetherwebake.org');

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
define('AUTH_KEY',         'c29UsXBLGI03wc7dvJ%som3*MrUZ$"8/o#D&+E(ZS(SdsXJOkF#k~"d8rK0eXDK*');
define('SECURE_AUTH_KEY',  '6%868@oK)q*(yP&/I@|y"Sf/"|i@8O?ydufvBBktxq)xncL^td;Ot3rh4^0c@LFQ');
define('LOGGED_IN_KEY',    'Wmn7mg461puH~Y1sN#MsBy2B~5/248@B;Dc1B`:y^R+TTu/*d&Mz5a4IEo_OUbD2');
define('NONCE_KEY',        '?oCcZq3Q~Zr5FpvyixY9kBKc!d+0a)ec+34Z/MCa&7__PpQxr4/oW&M&*+?!~U6q');
define('AUTH_SALT',        'Q:7F!n4G~01`&!ahteNU(T#?jo~2VP5BjN~sTSEMM4cpLVp02F0aK~z45Gjhr6S5');
define('SECURE_AUTH_SALT', 'U@e(eNJjxW&%oo+f%*n1!2^M4H*NCwtg?hCjkz&;QidA7EWQ1f04lw_h)|*qR4HH');
define('LOGGED_IN_SALT',   '"Vq!jRqf+?pELiQ`_pqk0:v?~43aBjaMq~0NWN:YQz4m7ksFxuHeF;Bze~d;cYx`');
define('NONCE_SALT',       'w1~m0WJWfgjKi"!CUm6CqJL2q"d39$b7WI?SG^W|dr?g1ozTFu!Lk(mVm2_9$LI(');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_utuu5u_';

/**
 * Limits total Post Revisions saved per Post/Page.
 * Change or comment this line out if you would like to increase or remove the limit.
 */
define('WP_POST_REVISIONS',  10);

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

