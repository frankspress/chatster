<?php

namespace Chatster\Views;

if ( ! defined( 'ABSPATH' ) ) exit;
require_once( CHATSTER_PATH . '/views/functions.loading-templates.php' );
require_once( CHATSTER_PATH . '/includes/core/trait.request.php' );
require_once( CHATSTER_PATH . '/views/admin/function.header.php' );
require_once( CHATSTER_PATH . '/views/admin/function.chat.php' );
require_once( CHATSTER_PATH . '/views/admin/function.request.php' );
require_once( CHATSTER_PATH . '/views/admin/function.settings.php' );
require_once( CHATSTER_PATH . '/views/public/function.front-chat.php' );

use Chatster\Core\ChatCollection;
use Chatster\Core\RequestCollection;

/**
 * Shows admin pages and front chat
 */
class DisplayManager
{
  use ChatCollection;
  use RequestCollection;

    public static function find_admin_view() {
      if ( ! current_user_can( 'manage_options' ) ) return;

      // GET request - parmeters
      $order = array('ASC', 'DESC');
      $tab = !empty($_GET['chtab']) ? $_GET['chtab'] : '';
      $cpage = !empty($_GET['cpage']) ? filter_var($_GET['cpage'], FILTER_VALIDATE_INT ) : 1;
      $order = isset($_GET['order']) && in_array(Strtoupper($_GET['order']), $order) ? $order[array_search(Strtoupper($_GET['order']), $order)] : 'DESC';

      $current_admin = wp_get_current_user();
      display_admin_header( $tab );

      switch ( $tab ) {
          case 'request':
              // Options
              $unreplied_only = false;
              // Pagination
              $per_page = 3;
              $count = self::count_all_requests( $unreplied_only );
              $current_page = ( $cpage > 0 && $cpage <= ceil( $count / $per_page ) ) ? $cpage : 1;
              $total_pages = ceil($count / $per_page);
              // Db requests query
              $requests = self::get_all_requests( $current_page, $per_page, $order_by = 'created_at', $order, $unreplied_only );
              display_admin_request( $requests, $total_pages, $current_page, $per_page, $count );
              break;
          case 'settings':
              display_admin_settings();
              break;
          default:
              /* Admin Chat */
              $admin_status = self::get_admin_status( $current_admin->user_email );
              display_admin_chat( $admin_status );
              break;
      }

    }

    public static function public_chat_view() {
      $customer_id = self::get_customer_id();
      $current_conv = $customer_id ? self::get_current_customer_conv( $customer_id ) : false;
      $chat_available = self::is_chat_available();
      display_front_chat( $current_conv, $chat_available );
    }

    private static function get_customer_id() {

      $current_user = wp_get_current_user();
      if ( $current_user && get_current_user_id() ) {
        return $current_user->user_email;
      }

      if ( isset($_COOKIE['ch_ctmr_id'])) {
        $customer_id = base64_decode($_COOKIE['ch_ctmr_id']);
      }

      return !empty( $customer_id ) ? $customer_id : false;

    }

}
