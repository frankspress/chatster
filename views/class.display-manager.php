<?php

if ( ! defined( 'ABSPATH' ) ) exit;

require_once( CHATSTER_PATH . '/views/admin/function.header.php' );
require_once( CHATSTER_PATH . '/views/admin/function.chat.php' );
require_once( CHATSTER_PATH . '/views/admin/function.request.php' );
require_once( CHATSTER_PATH . '/views/admin/function.settings.php' );
require_once( CHATSTER_PATH . '/views/public/function.front-chat.php' );

use Chatster\Core\ChatCollection;

/**
 *
 */
class DisplayManager
{
  use ChatCollection;

  public static function find_admin_view() {
    if ( ! current_user_can( 'manage_options' ) ) return;

    $current_admin = wp_get_current_user();
    $tab = !empty($_GET['chtab']) ? $_GET['chtab'] : '';

    display_admin_header( $tab );

    switch ( $tab ) {
        case 'request':
            display_admin_request();
            break;
        case 'settings':
            display_admin_settings();
            break;
        default:
          /* Admin Chat */
          $admin_status = self::get_admin_status( $current_admin->user_email );
          $current_convs = self::get_all_conv_admin( $current_admin->user_email );
          display_admin_chat( $current_convs, $admin_status );
          break;
    }

  }

  public static function public_chat_view() {
    display_front_chat();
  }

}
