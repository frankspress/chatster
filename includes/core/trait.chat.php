<?php

namespace Chatster\Api;

if ( ! defined( 'ABSPATH' ) ) exit;
require_once( CHATSTER_PATH . '/includes/core/trait.table-builder.php' );
use Chatster\Core\ChatsterTableBuilder;

trait ChatCollection {
  use ChatsterTableBuilder;

  protected function get_latest_messages() {

    // global $table_prefix, $wpdb;
    // $success = true;
    // $wp_table_presence = self::get_table_name('presence');
    $wp_table_message = self::get_table_name('message');
    // $wp_table_conversation = self::get_table_name('conversation');
    // $Table_Users = $table_prefix . 'users';
    // $charset_collate = $wpdb->get_charset_collate();
 return $wp_table_message;
    $sql = " SELECT  m.message, m.author_id, c.id as conv_id
             FROM wp_chatster_message as m
             INNER JOIN wp_chatster_conversation as c ON c.id = m.conv_id
               WHERE conv_id = ? and customer_id = ?
             LIMIT 15 ";
  }

  protected function remove_old_convs() {
    
  }

}
