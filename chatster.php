<?php
/**
 * Plugin Name: Chatster
 * Plugin URI: https://frankspress.com/
 * Description: Allows real time chat and get in touch interaction with BOT helper.
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
// If WooCommerce is not active do nothing.
if ( ! in_array('woocommerce/woocommerce.php', get_option( 'active_plugins') ) ) {
  define( 'CHATSTER_WOO_STATUS', FALSE );
}

define( 'CHATSTER_VERSION', '1.0.0' );
define( 'CHATSTER_DOMAIN', 'chatster_for_wc' );
define( 'CHATSTER_FILE_PATH', __FILE__ );
define( 'CHATSTER_PATH', plugin_dir_path( __FILE__ ) );
define( 'CHATSTER_URL_PATH', plugin_dir_url( __FILE__ ) );

/**
 * GENERAL HELPERS
 */
require_once( CHATSTER_PATH . '/includes/functions.global.php' );





if ( is_admin() ) {



}
