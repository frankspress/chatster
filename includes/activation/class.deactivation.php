<?php

if ( ! defined( 'ABSPATH' ) ) exit;
require_once( CHATSTER_PATH . '/includes/core/trait.table-builder.php' );
use Chatster\Core\ChatsterTableBuilder;

register_deactivation_hook( CHATSTER_FILE_PATH, array( 'DeactivationLoader', 'init_deactivation' ) );

class DeactivationLoader {

    use ChatsterTableBuilder;

    public static function init_deactivation() {
        if ( ! current_user_can( 'manage_options' ) ) return;
        return self::drop_db_table() &&
                self::remove_cron_event() &&
                  self::remove_key_options();
    }

    private static function drop_db_table() {
      global $wpdb;

      $wpdb->query(" DROP PROCEDURE IF EXISTS chatster_insert ");
      $wpdb->query(" DROP TRIGGER IF EXISTS chatster_conv_time_upd ");

      $wp_table = self::get_table_name('reply');
      $wpdb->query("DROP TABLE IF EXISTS $wp_table ");
      $wp_table = self::get_table_name('request');
      $wpdb->query("DROP TABLE IF EXISTS $wp_table ");
      $wp_table = self::get_table_name('message');
      $wpdb->query("DROP TABLE IF EXISTS $wp_table ");
      $wp_table = self::get_table_name('conversation');
      $wpdb->query("DROP TABLE IF EXISTS $wp_table ");
      $wp_table = self::get_table_name('presence_admin');
      $wpdb->query("DROP TABLE IF EXISTS $wp_table ");
      $wp_table = self::get_table_name('presence');
      $wpdb->query("DROP TABLE IF EXISTS $wp_table ");
      $wp_table = self::get_table_name('ticket');
      $wpdb->query("DROP TABLE IF EXISTS $wp_table ");

      return true;
    }

    private static function remove_cron_event() {
      $timestamp = wp_next_scheduled( 'chatster_remove_old_convs' );
      return wp_unschedule_event( $timestamp, 'chatster_remove_old_convs' );
    }

    private static function remove_key_options() {
      delete_option( 'chatster_api_key' );
      return true;
    }

  }
