<?php

namespace Chatster\Api;

if ( ! defined( 'ABSPATH' ) ) exit;
require_once( CHATSTER_PATH . '/includes/core/trait.table-builder.php' );
use Chatster\Core\ChatsterTableBuilder;

trait ChatCollection {
  use ChatsterTableBuilder;

  protected function insert_presence_customer( $customer_id ) {
    global $wpdb;
    $wp_table_presence = self::get_table_name('presence');
    $sql = " INSERT INTO $wp_table_presence ( customer_id ) VALUES( %s ) ON DUPLICATE KEY UPDATE last_presence = DEFAULT ";
    $sql = $wpdb->prepare( $sql, $customer_id );

    $result = $wpdb->get_results( $sql );
    wp_reset_postdata();

    return ! empty( $result ) ? $result : false;


  }

  protected function get_conv_admin() {

  }

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

    return ! empty( $result ) ? $result : $user_id;
  }

  protected function insert_new_message( $admin, $customer, $sender, $msg ) {
    global $wpdb;

    $sql = " CALL chatster_insert( %s, %s, %s, %s ) ";
    $sql = $wpdb->prepare( $sql, $admin, $customer, $sender, $msg );

    $result = $wpdb->get_results($sql);
    wp_reset_postdata();

    return ! empty( $result ) ? $result : false;
  }

  protected function find_active_admin( $customer ) {
    global $wpdb;
    $wp_table_presence = self::get_table_name('presence');

    $sql = " SELECT * FROM $wp_table_presence WHERE is_active = true AND last_presence >= NOW() - INTERVAL 10 MINUTE ";
    $result = $wpdb->get_results($sql);

    return ! empty( $result ) ? $result : false;
  }

  protected function find_current_admin( $customer_id ) {
    global $wpdb;
    $wp_table_presence = self::get_table_name('presence');
    $wp_table_conversation= self::get_table_name('conversation');
    $Table_Users = self::get_table_name('users');

    $sql = " SELECT * FROM $wp_table_presence as p
             INNER JOIN $wp_table_conversation as c
             INNER JOIN $Table_Users as u ON c.admin_email = u.user_email
             ON customer_id = %s
             WHERE p.is_active = true AND p.last_presence >= NOW() - INTERVAL 10 MINUTE
             ORDER by c.updated_at DESC
             LIMIT 1 ";

    $sql = $wpdb->prepare( $sql, $customer_id );

    $result = $wpdb->get_results($sql);
    wp_reset_postdata();

    return ! empty( $result ) ? $result : false;
  }


  protected function remove_old_convs() {

  }

}
