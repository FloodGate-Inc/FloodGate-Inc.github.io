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
define( 'DB_NAME', 'emedica_wp253' );

/** MySQL database username */
define( 'DB_USER', 'emedica_wp253' );

/** MySQL database password */
define( 'DB_PASSWORD', '2OSZ6@1[hp' );

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
define( 'AUTH_KEY',         'q8uiid72otpwf1kbye385ina1xdbgvmrsnoyrbibluqhipwoiwinz0zeckyjsgps' );
define( 'SECURE_AUTH_KEY',  '168x1vsjoqcfukyflprm1thv2h64ll8ggjihyscn4wssuplapo4qnlrwbf22yb5g' );
define( 'LOGGED_IN_KEY',    'l0dtckfn9sf0g4ops60ur929pe4tl892wp12cqy8lzm113nbbooazdtmfnvmugh6' );
define( 'NONCE_KEY',        '1gieq0zzplybkjzx6e9dkrsevw4cckc7xgrvvavbfjnt9c8zeuxu6guskuppkgll' );
define( 'AUTH_SALT',        'l0x2xjmjlflfb9jouapclsdygah0veyodvozbmzez6nrmpsap3wdbumfzag5xkiy' );
define( 'SECURE_AUTH_SALT', 'ryqyfxbkzoxcowtdhe59opvn6tlbjfgplnheabvenuei6bfnnwkirs7jerzivxla' );
define( 'LOGGED_IN_SALT',   'sqquy5vo0tmthyo4vmju66qxsjnyfmlgqwj4v1ujqsde9wtupo9r1jf4anuahr2c' );
define( 'NONCE_SALT',       'fcfi46ika9ciwtoaskcuqlaffgghspllphh9lldehiikuqrekfgw68pq5nzfoois' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wptt_';

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
