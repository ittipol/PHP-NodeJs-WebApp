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
define('DB_NAME', 'ticketeasys_web');

/** MySQL database username */
define('DB_USER', 'admin');

/** MySQL database password */
define('DB_PASSWORD', 'ss1q2w3e4rzz');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         'l6m@>,!~X_[JCX TVcYwf< zgFBE>(H)X?8tX%&W^[/W6a3f}FA+}s~ b.2MpDdj');
define('SECURE_AUTH_KEY',  'uDME$g61s}%{3P!.6d~ug%Yey[.`HS9m)mVjuX;}T=Y4,l~3W%vQ$TaD~i96Su=-');
define('LOGGED_IN_KEY',    'ljcG|nUG&D8H%@oKn#f`0_BZ:J_GISW|{+7vx+?%cOVia8-#9E*6RSzAWp5hA1dW');
define('NONCE_KEY',        '_%?l>%G5_vz{F&ouC&H.9<6+YG1I^++yf4;d{ii]{EOcA|0:5[H/Pr:?q4_We2d;');
define('AUTH_SALT',        '8{JCr+|O+^ForLZAKn:h E{&O6K;C4g^c^tm[S;G_4Wz5jDs<HUj)|Unf+>Zd(M3');
define('SECURE_AUTH_SALT', 'nBF,a%Os]!Dy+N_jY~jJ=uR~y%PV]-to^VslN=A RTQ)RWDLHE;v(m^#o[o*Xg7~');
define('LOGGED_IN_SALT',   ':Bri2N}D<WOq8,NcjP<RYzbw2B1FnU(k=K0=Z[-iP;@sBU3]p(XNPS K2iLQE,f@');
define('NONCE_SALT',       '%^!kAE9a&-mRf%B^h5>sba?%K*;mv.?Dq%/9zD0@#NOP.gOK(1i/z{G7^0Y4*@w_');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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

define('FS_METHOD','direct');