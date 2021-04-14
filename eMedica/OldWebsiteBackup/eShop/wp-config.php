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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'emedica_wp822' );

/** MySQL database username */
define( 'DB_USER', 'emedica_wp822' );

/** MySQL database password */
define( 'DB_PASSWORD', '!4(p4S3Tr9' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'ipoxuvoo3jjky1cc5cmfp0rdngx5pgz10gavxv7blnay1i7rwtg34lyhiexxqtzd' );
define( 'SECURE_AUTH_KEY',  't9nygtu27v5qcperwz7b1fihl6ulhoeov8zhpc29jyd2tppnmc12lisgynlym6d6' );
define( 'LOGGED_IN_KEY',    'eykmtihci7htjtube2xzgsxak5uc9gzszawgsjfvwawi1fvjpke8dlafsowgs9cr' );
define( 'NONCE_KEY',        'tlhvyefdvgmxcw4js52saqmpcivg6cgakvrjcibksjir90xquqngfkto4pmgoklb' );
define( 'AUTH_SALT',        'ikcbpjosjd58mcqsw7yaytnywsgqbclgydb3k2eh7bfveyiynx1dcdhycrxfc2y1' );
define( 'SECURE_AUTH_SALT', 'tvcyg3iesn6kphab3qwx3j54fndb5zj7lvfnukk4bv5qvf2stekpo569wgjho6u3' );
define( 'LOGGED_IN_SALT',   'gfmmo1fgq7jjnm6ubmvahmkq9quka4tc9giqi8ppqpi73f7gclalc9tqzjfd9luy' );
define( 'NONCE_SALT',       'vhzhwvpvj0lxztfh0fijoy7wo2oidrjwwzke8qugc0peyqzyjmesk0m3ykejjgyg' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wpko_';

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
define( 'WP_DEBUG', false );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
