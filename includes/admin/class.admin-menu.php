<?php

if ( ! defined( 'ABSPATH' ) ) exit;



/**
 * Adds the Admin Menu
 */
class AdminMenu
{

  function __construct() {
      add_action('admin_menu', array( $this, 'add_menu_link' ), 20);

  }

  public function add_menu_link() {
      add_submenu_page( 'woocommerce', __( 'Chatster', CHATSTER_DOMAIN ), __( 'Chatster', CHATSTER_DOMAIN ), 'view_woocommerce_reports', 'chatster-general', array( 'DisplayManager', 'find_view'));
  }

}

new AdminMenu();
