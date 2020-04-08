<?php

if ( ! defined( 'ABSPATH' ) ) exit;
require_once( CHATSTER_PATH . '/includes/core/trait.table-builder.php' );

register_activation_hook( CHATSTER_FILE_PATH, array( 'ActivationLoader', 'init_activation' ) );

class ActivationLoader  {

  use ChatsterTableBuilder;

  public static function init_activation() {
     if ( ! current_user_can( 'manage_options' ) ) return;
      return  self::create_db_chat_system() &&
                self::create_db_request() &&
                  self::create_db_automation();
  }

  private static function create_db_chat_system() {

    global $table_prefix, $wpdb;
    $success = true;
    $wp_table_presence = self::get_table_name('presence');
    $wp_table_message = self::get_table_name('message');
    $wp_table_conversation = self::get_table_name('conversation');
    $Table_Users = $table_prefix . 'users';
    $charset_collate = $wpdb->get_charset_collate();

    if ($wpdb->get_var( "SHOW TABLES LIKE '$wp_table_presence' " ) != $wp_table_presence)  {

        $sql  = " CREATE TABLE $wp_table_presence ( " ;
        $sql .= " id INT(11) NOT NULL AUTO_INCREMENT , ";
        $sql .= " admin_email varchar(100) NOT NULL , ";
        $sql .= " last_presence TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP , ";
        $sql .= " is_active BOOLEAN NOT NULL DEFAULT false, ";
        $sql .= " PRIMARY KEY (id) , ";
        $sql .= " CONSTRAINT admin_email FOREIGN KEY (admin_email) REFERENCES $Table_Users( user_email )  ON DELETE CASCADE ";
        $sql .= " ) ENGINE=InnoDB " . $charset_collate;

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
        $success = $success && empty($wpdb->last_error);
    }

    if ($wpdb->get_var( "SHOW TABLES LIKE '$wp_table_conversation' " ) != $wp_table_conversation)  {

        $sql  = " CREATE TABLE $wp_table_conversation ( " ;
        $sql .= " id INT(11) NOT NULL AUTO_INCREMENT , ";
        $sql .= " admin_email VARCHAR(100) NOT NULL , ";
        $sql .= " customer_id VARCHAR(100) NOT NULL , ";
        $sql .= " is_deleted BOOLEAN NOT NULL DEFAULT false, ";
        $sql .= " is_blocked BOOLEAN NOT NULL DEFAULT false, ";
        $sql .= " created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , ";
        $sql .= " updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , ";
        $sql .= " PRIMARY KEY (id) , ";
        $sql .= " CONSTRAINT ctsr_admin_customer UNIQUE ( admin_email , customer_id ) , ";
        $sql .= " CONSTRAINT ctsr_admin_email FOREIGN KEY (admin_email) REFERENCES $Table_Users( user_email )  ON DELETE CASCADE ";
        $sql .= ") ENGINE=InnoDB " . $charset_collate ;

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
        $sql .= ") ENGINE=InnoDB " . $charset_collate ;

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
        $success = $success && empty($wpdb->last_error);
    }

    return $success;

  }

  private static function create_db_request() {

    global $wpdb;
    $success = true;
    $wp_table_request = self::get_table_name('request');
    $wp_table_reply = self::get_table_name('reply');
    $charset_collate = $wpdb->get_charset_collate();

    if ($wpdb->get_var( "SHOW TABLES LIKE '$wp_table_request' " ) != $wp_table_request)  {

        $sql  = " CREATE TABLE $wp_table_request ( " ;
        $sql .= " id INT(11) NOT NULL AUTO_INCREMENT , ";
        $sql .= " email VARCHAR(100) NOT NULL , ";
        $sql .= " message VARCHAR(2500) NOT NULL , ";
        $sql .= " created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , ";
        $sql .= " PRIMARY KEY (id) ) ENGINE=InnoDB " . $charset_collate ;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        $success = $success && empty($wpdb->last_error);
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
        $sql .= ") ENGINE=InnoDB " . $charset_collate ;

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
        $success = $success && empty($wpdb->last_error);
    }

    return $success;

  }

  private static function create_db_automation() {
      global $wpdb;
      $success = true;
      $wp_table_conversation = self::get_table_name('conversation');
      $wp_table_message = self::get_table_name('message');

      /**
       * Create Insert Presidure For Chat
       */
       $wpdb->query( " DROP PROCEDURE IF EXISTS chatster_insert ");

       $sql = " CREATE PROCEDURE chatster_insert(IN admin VARCHAR(100), customer VARCHAR(100), sender VARCHAR(100), msg VARCHAR(800))
                BEGIN

                	DECLARE c_id INT DEFAULT NULL;
                	DECLARE last_id INT DEFAULT 0;

                    SELECT id INTO c_id FROM $wp_table_conversation WHERE
                                (admin_email = admin AND customer_id = customer) LIMIT 1;

                    IF (c_id IS NOT NULL)
                        THEN

                                INSERT INTO $wp_table_message (conv_id, author_id, message)
                                VALUES (c_id, sender, msg);
                                SELECT LAST_INSERT_ID() as message_id , c_id as conv_id;
                        ELSE

                                INSERT INTO $wp_table_conversation ( admin_email, customer_id )
                                VALUES ( admin, customer );
                                SET last_id = LAST_INSERT_ID();
                                INSERT INTO $wp_table_message (conv_id, author_id, message)
                                VALUES (last_id, sender, msg);
                                SELECT LAST_INSERT_ID() as message_id, last_id as conv_id;
                        END IF;
                END  ";

        $wpdb->query( $sql );
        $success = $success && empty($wpdb->last_error);

        /**
         * Add Message Insert Trigger
         */
        $wpdb->query( " DROP TRIGGER IF EXISTS chatster_conv_time_upd ");

        $sql = " CREATE TRIGGER chatster_conv_time_upd AFTER INSERT ON $wp_table_message
                  	FOR EACH ROW
                      BEGIN
	                       UPDATE $wp_table_conversation
                         SET updated_at = CURRENT_TIMESTAMP
                         WHERE id = NEW.conv_id;
                      END ";

        $wpdb->query( $sql );
        $success = $success && empty($wpdb->last_error);

        return $success;
  }
}
