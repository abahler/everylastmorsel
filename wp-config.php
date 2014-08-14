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
define('DB_NAME', 'everylastmorsel');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'root');

/** MySQL hostname */
define('DB_HOST', 'everylastmorsel.dev');

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
define('AUTH_KEY',         'h<OCvnVTxp]5dPX&5|G#aeSS2ObB[rRMBh]7-e=@DIK;Os7H40b5e62-}`]sUIo|');
define('SECURE_AUTH_KEY',  'z+#[jp:{p|h[Chzp]r5g^9NJy]i{#j|5`P*vJeos9ztP%ZFDx)nT&G@B>862|#f*');
define('LOGGED_IN_KEY',    'z--9+}Z4H@*|`!Y1ZyX_zzAcY.(:Xo1^Xj%9|rk;Q]~k3%!aT343>U2&;$@J)AtC');
define('NONCE_KEY',        '_*ia:G>9.-G]]<MlnajE)COsefeI|Sp4.+2R%%b/6>m5wp*C}hozEu0%ny!zNpP+');
define('AUTH_SALT',        '}FG)gTT!:WE%[b4q+Axo8ViZk}K_GF4XpjBar-gt7EE= vrH + :b)^Mr,]e?SE8');
define('SECURE_AUTH_SALT', '7{mZ/raRbL8j.Lw3[Ly4$+{ED!,}iZVwVR1iEo:K%O7LCUE(kr)F:`@-j6_r>jR6');
define('LOGGED_IN_SALT',   'yqO9&gkHykyEOm|X&T9ll{9j6r<o-UKK*;IMs!}$1T4x0VSE$Lh:;6Fq_K7`Ny0r');
define('NONCE_SALT',       'iKlpBp=<+|;D^)-cdT~E>v#}0-sb}q+_~ksI7SKZM/9_.R&8Fi.-KjrZFkI_U|3E');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
