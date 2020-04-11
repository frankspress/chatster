<?php

namespace Chatster\Api;

if ( ! defined( 'ABSPATH' ) ) exit;
require_once( CHATSTER_PATH . '/includes/core/trait.table-builder.php' );
use Chatster\Core\ChatsterTableBuilder;

trait ChatCollection {
  use ChatsterTableBuilder;

  protected function get_latest_messages( $conv_id = 0, $user_id = '' ) {

    global $wpdb;
    $wp_table_message = self::get_table_name('message');
    $wp_table_conversation = self::get_table_name('conversation');

    $sql = " SELECT  m.message, m.author_id, c.id as conv_id
             FROM $wp_table_message as m
             INNER JOIN $wp_table_conversation as c ON c.id = m.conv_id
             WHERE conv_id = %d AND ( customer_id = %s OR admin_email = %s )
             ORDER BY m.created_at DESC
             LIMIT 15 ";

    $sql = $wpdb->prepare( $sql, $conv_id, $user_id, $user_id );
    $result = $wpdb->get_results($sql);
    wp_reset_postdata();

    return ! empty( $result ) ? $result : false;
  }

  protected function remove_old_convs() {

  }

}
