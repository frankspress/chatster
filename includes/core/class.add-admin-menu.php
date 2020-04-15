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

      add_submenu_page( 'woocommerce', __( 'Chatster', CHATSTER_DOMAIN ), $this->get_menu_title_link() , 'view_woocommerce_reports', 'chatster-menu', array( 'DisplayManager', 'find_admin_view'));

      $menu_page = add_menu_page( 'chatster-menu',
                                  'Chatster',
                                  'manage_options',
                                  'chatster',
                                  array( 'DisplayManager', 'find_admin_view'), 'dashicons-format-status'
      );

      add_action('admin_print_scripts-'.$menu_page, function() {
          wp_enqueue_style( 'wp-color-picker' );
          wp_enqueue_style( 'chatster-css-admin', CHATSTER_URL_PATH . '/assets/css/style-admin.css');
          wp_enqueue_script( 'chatster-chat-admin', CHATSTER_URL_PATH . '/assets/js/chat-admin.js',  array('jquery'), 1.0, true);
          wp_enqueue_script( 'chatster-request-admin', CHATSTER_URL_PATH . '/assets/js/request-admin.js',  array('jquery'), 1.0, true);
          wp_enqueue_script( 'chatster-settings-admin', CHATSTER_URL_PATH . '/assets/js/settings-admin.js',  array('jquery', 'wp-color-picker'), 1.0, true);
          wp_localize_script( 'chatster-chat-admin', 'chatsterDataAdmin', array(
            'api_base_url' => esc_url_raw( rest_url('chatster/v1') ),
            'nonce' => wp_create_nonce( 'wp_rest' )
          ) );
      });

      add_action('admin_print_scripts-woocommerce_page_chatster-menu', function() {
          wp_enqueue_style( 'wp-color-picker' );
          wp_enqueue_style( 'chatster-css-admin', CHATSTER_URL_PATH . '/assets/css/style-admin.css');
          wp_enqueue_script( 'chatster-chat-admin', CHATSTER_URL_PATH . '/assets/js/chat-admin.js',  array('jquery'), 1.0, true);
          wp_enqueue_script( 'chatster-request-admin', CHATSTER_URL_PATH . '/assets/js/request-admin.js',  array('jquery'), 1.0, true);
          wp_enqueue_script( 'chatster-settings-admin', CHATSTER_URL_PATH . '/assets/js/settings-admin.js',  array('jquery', 'wp-color-picker'), 1.0, true);
          wp_localize_script( 'chatster-chat-admin', 'chatsterDataAdmin', array(
            'api_base_url' => esc_url_raw( rest_url('chatster/v1') ),
            'nonce' => wp_create_nonce( 'wp_rest' )
          ) );
      });

  }

  private function get_menu_title_link() {
    $title  = '<span id="chatster-menu-link">'. __( 'Chatster', CHATSTER_DOMAIN ).'</span>&nbsp;';
    // TODO
    // $title .= '<span class="active-convs-link">'.'</span>';
    return $title;
  }



}

new AdminMenu();
