<?php

if ( ! defined( 'ABSPATH' ) ) exit;



/**
 * Adds the Admin Menu
 */
class AdminMenu
{

  function __construct() {
      add_action( 'admin_menu', array( $this, 'add_menu_page' ), 20);
      add_action( 'admin_menu', array( $this, 'change_menu_order' ), 99);

  }

  public function add_menu_page() {

      $menu_page = add_menu_page( 'Chatster',
                                  $this->get_menu_title_link(),
                                  'manage_options',
                                  'chatster-menu',
                                  array( 'Chatster\Views\DisplayManager', 'find_admin_view'), 'dashicons-format-status'
      );

      add_action('admin_print_scripts-'.$menu_page, function() {

          $current_tab = isset( $_GET['chtab'] ) ? $_GET['chtab'] : false;

          wp_enqueue_style( 'wp-color-picker' );
          wp_enqueue_style( 'chatster-css-autocomplete', CHATSTER_URL_PATH . 'assets/css/chat-autocomplete.css');
          wp_enqueue_style( 'chatster-css-admin', CHATSTER_URL_PATH . 'assets/css/style-admin.css');
          wp_enqueue_style( 'chatster-css-admin-loaders', CHATSTER_URL_PATH . 'assets/css/style-loaders.css');
          wp_enqueue_script( 'chatster-general', CHATSTER_URL_PATH . 'assets/js/general-admin.js',  array('jquery'), 1.0, true);
          if ( !$current_tab || $current_tab == 'chat' ) {
            wp_enqueue_script( 'chatster-chat-admin', CHATSTER_URL_PATH . 'assets/js/chat-admin.js',  array('jquery'), 1.0, true);
            wp_enqueue_script( 'chatster-autocomplete-admin', CHATSTER_URL_PATH . 'assets/js/chat-autocomplete.js',  array('jquery'), 1.0, true);
          }
          if ( $current_tab == 'request' ) {
            wp_enqueue_script( 'chatster-request-admin', CHATSTER_URL_PATH . 'assets/js/request-admin.js',  array('jquery'), 1.0, true);
          }
          if ( $current_tab == 'settings' ) {
            wp_enqueue_script( 'chatster-settings-admin', CHATSTER_URL_PATH . 'assets/js/settings-admin.js',  array('jquery', 'wp-color-picker'), 1.0, true);
          }
          wp_localize_script( 'chatster-general', 'chatsterDataAdmin', array(
            'api_base_url' => esc_url_raw( rest_url('chatster/v1') ),
            'wp_api_base_url' => esc_url_raw( get_rest_url() ),
            'nonce' => wp_create_nonce( 'wp_rest' ),
            'no_image_link' => CHATSTER_URL_PATH . 'assets/img/no-image.jpg',
            'sound_file_path' => CHATSTER_URL_PATH . 'assets/sound/when',
            'chat_sound_vol' => 0.2
          ) );
      });

  }

  public function get_menu_title_link() {
    $title  = '<span id="chatster-menu-link">'. __( 'Chatster', CHATSTER_DOMAIN ).'</span>&nbsp;';
    // TODO
    // $title .= '<span class="active-convs-link">'.'</span>';
    return $title;
  }

  public function change_menu_order() {
    global $menu;

    foreach ($menu as $key => $array) {
      if ( $array[3] == 'Analytics' ) { $analytics_pos = $key; }
      if ( $array[3] == 'Chatster' ) { $chatster_pos = $key; }
    }

    if ( isset($analytics_pos) ) {
      $x = 1;
      while( $x <= count($menu)  ) {
          if ( ! isset($menu[$analytics_pos + $x]) && isset($menu[$chatster_pos])) {
            $menu[$analytics_pos + $x] = $menu[$chatster_pos];
            unset($menu[$chatster_pos]);
            break;
          }
          $x++;
      }
    }
  }

}

new AdminMenu();
