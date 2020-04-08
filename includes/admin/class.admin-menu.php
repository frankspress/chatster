<?php

if ( ! defined( 'ABSPATH' ) ) exit;



/**
 * Adds the Admin Menu
 */
class AdminMenu
{

  function __construct() {
      add_action( 'admin_menu', array( $this, 'add_submenu_page' ), 20);
  }

  public function add_submenu_page() {
    
      add_submenu_page( 'woocommerce', __( 'Chatster', CHATSTER_DOMAIN ), __( 'Chatster', CHATSTER_DOMAIN ), 'view_woocommerce_reports', 'chatster-menu', array( 'DisplayManager', 'find_view'));

      add_action('admin_print_scripts-woocommerce_page_chatster-menu', function() {
          wp_enqueue_style( 'wp-color-picker' );
          wp_enqueue_style( 'chatster-admin', CHATSTER_URL_PATH . '/assets/css/style-admin.css');
          wp_enqueue_script( 'chatster-admin', CHATSTER_URL_PATH . '/assets/js/chat-admin.js',  array('jquery', 'wp-color-picker'), 1.0, true);
          wp_localize_script( 'chatster-admin', 'chatsterDataAdmin', array(
            'api_base_url' => esc_url_raw( rest_url('chatster/v1') ),
            'nonce' => wp_create_nonce( 'wp_rest' )
          ) );
      });

  }



}

new AdminMenu();
