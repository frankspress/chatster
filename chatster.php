<?php
/**
 * Plugin Name: Chatster
 * Plugin URI: https://frankspress.com/
 * Description: Allows real time chat and get in touch interaction with custom BOT helper.
 * Author: Frank Pagano
 * Author URI: https://frankspress.com
 * Text Domain: chatster
 * Version: 1.0.0
 * Copyright (c) 2020 Frankspress
 * License: GPLv2 or later
 *
 * If you don't have a copy of the license please go to <http://www.gnu.org/licenses/>.
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * If WooCommerce is active it sets a constant.
 */
if ( in_array( 'woocommerce/woocommerce.php', get_option( 'active_plugins' ) ) ) {
  define( 'CHATSTER_WOO_STATUS', TRUE );
}

define( 'CHATSTER_VERSION', '1.0.0' );
define( 'CHATSTER_DOMAIN', 'chatster' );
define( 'CHATSTER_KEY',  get_option( 'chatster_enc_key' ) );
define( 'CHATSTER_FILE_PATH', __FILE__ );
define( 'CHATSTER_PATH', plugin_dir_path( __FILE__ ) );
define( 'CHATSTER_URL_PATH', plugin_dir_url( __FILE__ ) );
define( 'CHATSTER_FONTAWESOME_URL', 'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' );
define( 'CHATSTER_AUTOCOMPLETE', 'https://cdn.jsdelivr.net/autocomplete.js/0.37.1/autocomplete.jquery.min.js' );

/**
 * Load Chatster
 */
require_once( CHATSTER_PATH . '/includes/core/chatster-loader.php' );

/**
 * Adds Translation Support
 */
add_action( 'plugins_loaded', function() {
    load_plugin_textdomain( CHATSTER_DOMAIN, FALSE, basename( CHATSTER_PATH ) . '/languages/' );
});
