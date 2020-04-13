<?php

if ( ! defined( 'ABSPATH' ) ) exit;

require_once( CHATSTER_PATH . '/views/admin/function.chat.php' );
require_once( CHATSTER_PATH . '/views/admin/function.request.php' );
require_once( CHATSTER_PATH . '/views/admin/function.settings.php' );
require_once( CHATSTER_PATH . '/views/public/function.front-chat.php' );

/**
 *
 */
class DisplayManager
{


  public static function find_admin_view() {
    echo 'MULTI SECTIONS MENU';

    $page = 'TO DO';
    switch ($page) {
        case 'cock':
            echo "i equals 0";
            break;
        case 1:
            echo "i equals 1";
            break;
        case 2:
            echo "i equals 2";
            break;
        default:
        //    echo "i is not equal to 0, 1 or 2";
            break;
    }

  }

  public static function public_chat_view() {
    display_front_chat();
  }

}
