<?php

if ( ! defined( 'ABSPATH' ) ) exit;

register_activation_hook( CHATSTER_FILE_PATH, array( 'activation_loader', 'init_activation' ) );

class activation_loader implements chatster_db_setup {

  public static function init_activation() {
      if ( ! current_user_can( 'manage_options' ) ) return;
      return  self::create_db_chat_system()  && self::create_db_request();
  }

  private static function create_db_chat_system() {

    global $table_prefix, $wpdb;
    $tblname = self::presence;
    $wp_table_presence = $table_prefix . $tblname;
    $tblname = self::message;
    $wp_table_message = $table_prefix . $tblname;
    $tblname = self::conversation;
    $wp_table_conversation = $table_prefix . $tblname;
    $Table_Users = $table_prefix . 'users';
    $success = true;

    if ($wpdb->get_var( "SHOW TABLES LIKE '$wp_table_presence' " ) != $wp_table_presence)  {

        $sql  = " CREATE TABLE $wp_table_presence ( " ;
        $sql .= " id INT(11) NOT NULL AUTO_INCREMENT , ";
        $sql .= " admin_email varchar(100) NOT NULL , ";
        $sql .= " last_presence TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP , ";
        $sql .= " PRIMARY KEY (id), ";
        $sql .= " CONSTRAINT ctsr_admin_email FOREIGN KEY (admin_email) REFERENCES $Table_Users( email ) ) ON DELETE CASCADE ) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
        $success = $success && empty($wpdb->last_error);
    }

    if ($wpdb->get_var( "SHOW TABLES LIKE '$wp_table_conversation' " ) != $wp_table_conversation)  {

        $sql  = " CREATE TABLE $wp_table_conversation ( " ;
        $sql .= " id INT(11) NOT NULL AUTO_INCREMENT , ";
        $sql .= " admin_email VARCHAR(100) NOT NULL , ";
        $sql .= " customer_id VARCHAR(100) NOT NULL , ";
        $sql .= " created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , ";
        $sql .= " updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP , ";
        $sql .= " PRIMARY KEY (id) , ";
        $sql .= " CONSTRAINT ctsr_admin_customer UNIQUE ( admin_email , customer_id ) , ";
        $sql .= " CONSTRAINT ctsr_admin_email FOREIGN KEY (admin_email) REFERENCES $Table_Users( email ) ) ON DELETE CASCADE ) ) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
        $success = $success && empty($wpdb->last_error);
    }

    if ($wpdb->get_var( "SHOW TABLES LIKE '$wp_table_message' " ) != $wp_table_message)  {

        $sql  = " CREATE TABLE $wp_table_message ( " ;
        $sql .= " id BIGINT(11) NOT NULL AUTO_INCREMENT , ";
        $sql .= " conv_id INT(11) NOT NULL , ";
        $sql .= " message VARCHAR(800) NOT NULL , ";
        $sql .= " author_id VARCHAR(100) NOT NULL , ";
        $sql .= " created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , ";
        $sql .= " updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP , ";
        $sql .= " PRIMARY KEY (id) , ";
        $sql .= " CONSTRAINT conv_id FOREIGN KEY (conv_id) REFERENCES $wp_table_conversation(id) ON DELETE CASCADE ";
        $sql .= ") ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
        $success = $success && empty($wpdb->last_error);
    }

    return $success;

  }

  private static function create_db_request() {

    global $table_prefix, $wpdb;
    $tblname = self::request;
    $wp_table_request = $table_prefix . $tblname;
    $tblname = self::reply;
    $wp_table_reply = $table_prefix . $tblname;

    if ($wpdb->get_var( "SHOW TABLES LIKE '$wp_table_request' " ) != $wp_table_request)  {

        $sql  = " CREATE TABLE $wp_table_request ( " ;
        $sql .= " id INT(11) NOT NULL AUTO_INCREMENT , ";
        $sql .= " email VARCHAR(100) NOT NULL , ";
        $sql .= " message VARCHAR(2500) NOT NULL , ";
        $sql .= " created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , ";
        $sql .= " PRIMARY KEY (id) ) ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        $success1 = empty($wpdb->last_error);
    }

    if ($wpdb->get_var( "SHOW TABLES LIKE '$wp_table_reply' " ) != $wp_table_reply)  {

        $sql  = " CREATE TABLE $wp_table_reply ( " ;
        $sql .= " id INT(11) NOT NULL AUTO_INCREMENT , ";
        $sql .= " request_id INT(11) NOT NULL , ";
        $sql .= " admin_email VARCHAR(100) NOT NULL , ";
        $sql .= " message VARCHAR(2500) NOT NULL , ";
        $sql .= " created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , ";
        $sql .= " PRIMARY KEY (id) , ";
        $sql .= " CONSTRAINT request_id FOREIGN KEY (request_id) REFERENCES $wp_table_request(id) ON DELETE CASCADE ";
        $sql .= ") ENGINE=InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        $success1 = empty($wpdb->last_error);
    }

  }

}
