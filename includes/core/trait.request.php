<?php

namespace Chatster\Core;

if ( ! defined( 'ABSPATH' ) ) exit;
require_once( CHATSTER_PATH . '/includes/core/trait.table-builder.php' );
use Chatster\Core\ChatsterTableBuilder;

trait RequestCollection {
  use ChatsterTableBuilder;

/**
 * Static Requests
 */
  protected static function count_all_requests( $unreplied_only = true ) {
      global $wpdb;
      $wp_table_request = self::get_table_name('request');
      $wp_table_reply = self::get_table_name('reply');

      $sql  = " SELECT COUNT(*) AS count FROM $wp_table_request AS r ";
      $sql .= " LEFT JOIN $wp_table_reply AS rp ON r.id = rp.request_id ";
      if ( $unreplied_only ) {
        $sql .= " WHERE rp.created_at IS NULL ";
      }

      $count = $wpdb->get_var( $sql );
      dump($count);
      return !empty($count) ? $count : false;
  }

  protected static function get_all_requests( $current_page = 1, $per_page = 8, $order_by = 'created_at', $order = 'ASC', $unreplied_only = true ) {

      global $wpdb;
      $wp_table_request = self::get_table_name('request');
      $wp_table_reply = self::get_table_name('reply');
      $offset = ( $current_page - 1 ) * $per_page;

      $sql  = " SELECT r.*, rp.admin_email as replied_by, rp.message as reply_context, rp.created_at as replied_at FROM $wp_table_request AS r
                LEFT JOIN $wp_table_reply as rp ON r.id = rp.request_id ";
      if ( $unreplied_only ) {
        $sql .= " WHERE rp.created_at IS NULL OR ( r.is_flagged = true ) ";
      }
      $sql .= " ORDER BY is_flagged DESC, ". esc_sql($order_by)." ".esc_sql($order). "
                LIMIT %d, %d ";

      $sql = $wpdb->prepare( $sql, array( $offset, $per_page ) );
      $result = $wpdb->get_results( $sql);

      return ! empty( $result ) ? $result : false;
  }

 /**
  * Api Methods
  */
   protected function insert_reply( $admin_email, $message, $request_id ) {
     global $wpdb;
     $wp_table_reply = self::get_table_name('reply');

     $sql = " INSERT INTO $wp_table_reply ( request_id, admin_email, message ) VALUES( %d, %s, %s ) ";
     $sql = $wpdb->prepare( $sql, array( $request_id, $admin_email, $message ) );

     $result = $wpdb->get_results( $sql );
     wp_reset_postdata();

     return ! empty( $result ) ? $result : false;
   }

   protected function get_replies( $request_id ) {
     global $wpdb;
     $wp_table_reply = self::get_table_name('reply');

     $sql  = " SELECT * FROM $wp_table_reply WHERE request_id = %d ";

     $sql = $wpdb->prepare( $sql, $request_id);
     $result = $wpdb->get_results( $sql);

     return ! empty( $result ) ? $result : false;
   }

   protected function delete_request( $request_id ) {
     global $wpdb;
     $wp_table_request = self::get_table_name('request');

     $sql = " DELETE FROM $wp_table_request WHERE id = %d ";
     $sql = $wpdb->prepare( $sql, $request_id );

     $result = $wpdb->get_results( $sql );
     wp_reset_postdata();

     return ! empty( $result ) ? $result : false;
   }

   // Public Insert
   protected function insert_request( $name, $email, $subject, $message ) {
     global $wpdb;
     $wp_table_request = self::get_table_name('request');

     $sql = " INSERT INTO $wp_table_request ( name, email, subject, message ) VALUES( %s, %s, %s, %s ) ";
     $sql = $wpdb->prepare( $sql, array( $name, $email, $subject, $message ) );

     $result = $wpdb->get_results( $sql );
     wp_reset_postdata();

     return ! empty( $result ) ? $result : false;
   }

}
