<?php

namespace Chatster\Core;

if ( ! defined( 'ABSPATH' ) ) exit;
require_once( CHATSTER_PATH . '/includes/core/trait.table-builder.php' );
use Chatster\Core\ChatsterTableBuilder;

trait ChatCollection {
  use ChatsterTableBuilder;

/**
 * Static Methods for Display Manager
 */

  protected static function get_admin_status( $admin_email ) {
    global $wpdb;
    $wp_table_presence_admin = self::get_table_name('presence_admin');

    $sql = " SELECT is_active FROM $wp_table_presence_admin WHERE admin_email = %s ";
    $sql = $wpdb->prepare( $sql, $admin_email );

    $is_active = $wpdb->get_var( $sql );
    wp_reset_postdata();

    return $is_active;
  }

/**
 * Api Methods
 */

  protected function insert_form_data( $customer_id, $form_data ) {
     global $wpdb;
     $wp_table_presence = self::get_table_name('presence');
     $sql = " INSERT INTO $wp_table_presence ( customer_id, form_data ) VALUES( %s, %s ) ON DUPLICATE KEY UPDATE last_presence = DEFAULT, form_data = %s ";
     $sql = $wpdb->prepare( $sql, $customer_id, $form_data, $form_data );

     $result = $wpdb->get_results( $sql );
     wp_reset_postdata();

     return ! empty( $result ) ? $result : false;


  }

  protected function insert_presence_customer( $customer_id ) {
    global $wpdb;
    $wp_table_presence = self::get_table_name('presence');
    $sql = " INSERT INTO $wp_table_presence ( customer_id ) VALUES( %s ) ON DUPLICATE KEY UPDATE last_presence = DEFAULT ";
    $sql = $wpdb->prepare( $sql, $customer_id );

    $result = $wpdb->get_results( $sql );
    wp_reset_postdata();

    return ! empty( $result ) ? $result : false;


  }

  protected function insert_presence_admin( $admin_email ) {
    global $wpdb;
    $wp_table_presence_admin = self::get_table_name('presence_admin');
    $sql = " INSERT INTO $wp_table_presence_admin ( admin_email ) VALUES( %s ) ON DUPLICATE KEY UPDATE last_presence = DEFAULT ";
    $sql = $wpdb->prepare( $sql, $admin_email );

    $result = $wpdb->get_results( $sql );
    wp_reset_postdata();

    return ! empty( $result ) ? $result : false;


  }

  protected function change_admin_status( $admin_email, $status = false ) {
    global $wpdb;
    $wp_table_presence_admin = self::get_table_name('presence_admin');
    $safe_status = $status ? 'true' : 'false';

    $sql = " INSERT INTO $wp_table_presence_admin ( admin_email, is_active ) VALUES( %s, $safe_status ) ON DUPLICATE KEY UPDATE last_presence = DEFAULT, is_active = $safe_status ";
    $sql = $wpdb->prepare( $sql, $admin_email );

    $result = $wpdb->query( $sql );
    wp_reset_postdata();

    return $result;
  }

  protected function get_latest_messages( $conv_id = 0, $last_msg_id = 0, $user_id = '' ) {

    global $wpdb;
    $wp_table_message = self::get_table_name('message');
    $wp_table_conversation = self::get_table_name('conversation');

    $sql = " SELECT mm.*
             FROM (  SELECT m.id, m.temp_id, m.message, IF( m.author_id = %s , TRUE, FALSE ) AS is_author, c.id as conv_id, m.created_at as created_at
                     FROM $wp_table_message as m
                     INNER JOIN $wp_table_conversation as c ON c.id = m.conv_id
                     WHERE conv_id = %d AND ( customer_id = %s OR admin_email = %s ) AND m.id > %d
                     ORDER BY m.created_at DESC
                     LIMIT 25 )
             AS mm ORDER BY mm.created_at ASC ";

    $sql = $wpdb->prepare( $sql, $user_id, $conv_id, $user_id, $user_id, $last_msg_id );
    $result = $wpdb->get_results($sql);
    wp_reset_postdata();

    return ! empty( $result ) ? $result : false;
  }

  protected function get_latest_messages_public( $last_msg_id = 0, $assigned_admin, $customer_id = '' ) {

    global $wpdb;
    $wp_table_message = self::get_table_name('message');
    $wp_table_conversation = self::get_table_name('conversation');

    $sql = " SELECT mm.*
             FROM (  SELECT m.id, m.temp_id, m.message, IF( m.author_id = %s , TRUE, FALSE ) AS is_author, c.id as conv_id, m.created_at as created_at
                     FROM $wp_table_message as m
                     INNER JOIN $wp_table_conversation as c ON c.id = m.conv_id
                     WHERE  ( customer_id = %s AND admin_email = %s ) AND c.is_connected = true AND m.id > %d
                     ORDER BY m.created_at DESC
                     LIMIT 25 )
             AS mm ORDER BY mm.created_at ASC ";

    $sql = $wpdb->prepare( $sql, $customer_id, $customer_id, $assigned_admin, $last_msg_id );
    $result = $wpdb->get_results($sql);
    wp_reset_postdata();

    return ! empty( $result ) ? $result : false;
  }

  protected function insert_new_message( $admin, $customer, $sender, $msg, $temp_id ) {
    global $wpdb;

    $sql = " CALL chatster_insert( %s, %s, %s, %s, %d ) ";
    $sql = $wpdb->prepare( $sql, $admin, $customer, $sender, $msg, $temp_id );

    $result = $wpdb->get_results($sql);
    wp_reset_postdata();

    return ! empty( $result ) ? $result : false;
  }

  protected function set_message_read( $admin, $conv_id, $msg_id ) {
    global $wpdb;
    $wp_table_message = self::get_table_name('message');

    $sql = " UPDATE $wp_table_message SET is_read = TRUE
             WHERE id > %d AND conv_id = %d AND author_id = %s ";

    $sql = $wpdb->prepare( $sql, $msg_id, $conv_id, $admin );
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

  protected function get_all_convs_admin( $admin_email, $last_conv_poll = 0 ) {
    global $wpdb;
    $wp_table_conversation = self::get_table_name('conversation');
    $wp_table_message = self::get_table_name('message');
    $wp_table_presence = self::get_table_name('presence');
    $Table_Users = self::get_table_name('users');

    $sql = " SELECT  c.*, u.user_nicename as reg_customer_name, p.last_presence, p.form_data, COUNT(m.id) as not_read
             FROM $wp_table_conversation as c
             INNER JOIN $wp_table_presence as p ON p.customer_id = c.customer_id
             LEFT JOIN $wp_table_message as m ON m.conv_id = c.id AND m.is_read = false
             LEFT JOIN $Table_Users as u ON c.customer_id = u.user_email
             WHERE admin_email = %s AND c.id > %d
             AND p.last_presence >= NOW() - INTERVAL 100000 MINUTE
             GROUP BY c.id
             ORDER BY c.created_at DESC
             LIMIT 20 ";

    $sql = $wpdb->prepare( $sql, $admin_email, $last_conv_poll );
    $result = $wpdb->get_results( $sql );
    wp_reset_postdata();

    return ! empty( $result ) ? $result : false;

  }

  protected function disconnect_chat( $conv_id ) {
    global $wpdb;
    $wp_table_conversation= self::get_table_name('conversation');

    $sql = " UPDATE $wp_table_conversation
             SET is_connected = FALSE
             WHERE id = %d
             LIMIT 1 ";

    $sql = $wpdb->prepare( $sql, $conv_id );

    $result = $wpdb->get_results($sql);
    wp_reset_postdata();

    return ! empty( $result ) ? $result : false;
  }

/**
 * Cron Jobs
 */
  protected function remove_old_convs() {

  }

}
