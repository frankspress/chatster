<?php

if ( ! defined( 'ABSPATH' ) ) exit;
require_once( CHATSTER_PATH . '/includes/core/trait.table-builder.php' );

register_deactivation_hook( CHATSTER_FILE_PATH, array( 'DeactivationLoader', 'init_deactivation' ) );

class DeactivationLoader {

    use ChatsterTableBuilder;

    public static function init_deactivation() {
        if ( ! current_user_can( 'manage_options' ) ) return;
        return self::drop_db_table();
    }

    private static function drop_db_table() {
      global $wpdb;

      $wp_table = self::get_table_name('reply');
      $wpdb->query("DROP TABLE IF EXISTS $wp_table ");
      $wp_table = self::get_table_name('request');
      $wpdb->query("DROP TABLE IF EXISTS $wp_table ");
      $wp_table = self::get_table_name('message');
      $wpdb->query("DROP TABLE IF EXISTS $wp_table ");
      $wp_table = self::get_table_name('conversation');
      $wpdb->query("DROP TABLE IF EXISTS $wp_table ");
      $wp_table = self::get_table_name('presence');
      $wpdb->query("DROP TABLE IF EXISTS $wp_table ");
      $wpdb->query("DROP PROCEDURE IF EXISTS chatster_insert ");

      return true;
    }
  }
