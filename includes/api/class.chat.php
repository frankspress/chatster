<?php

namespace Chatster\Api;

if ( ! defined( 'ABSPATH' ) ) exit;
require_once( CHATSTER_PATH . '/includes/core/trait.chat.php' );

use Chatster\Api\ChatCollection;

class ChatApi  {
  use ChatCollection;

  public function __construct() {
    $this->insert_msg_route();
    $this->poll_msg_route();
  }

  /**
   * Routes
   */
  public function insert_msg_route() {
    add_action('rest_api_init', function () {
      register_rest_route( 'chatster/v1', '/chat/insert', array(
                    'methods'  => 'POST',
                    'callback' => array( $this, 'insert_msg_db' ),
                    'permission_callback' => array( $this, 'validate_message' )
          ));
    });
  }

  public function poll_msg_route() {
    add_action('rest_api_init', function () {
      register_rest_route( 'chatster/v1', '/chat/poll', array(
                    'methods'  => 'POST',
                    'callback' => array( $this, 'long_poll_db' ),
                    'permission_callback' => array( $this, 'validate_user' )
          ));
    });
  }

  /**
   * Methods
   */
   private function set_customer_id( $customer_id = '' ) {
     if ( empty($customer_id)) {
       $customer_id = substr(md5(uniqid(rand(), true)), 0, 100);
     }
     setcookie('unreg_chatster_id', base64_encode(serialize($customer_id)), (time() + 8419200), "/");
     return $customer_id;
   }

  private function get_customer_id() {

    $current_user = wp_get_current_user();
    if ( $current_user && get_current_user_id() ) {
      return $current_user->user_email;
    }

    if ( isset($_COOKIE['unreg_chatster_id'])) {
      $customer_id = unserialize(base64_decode($_COOKIE['unreg_chatster_id']), ["allowed_classes" => false]);
        return $this->set_customer_id($customer_id);
    }

    return $this->set_customer_id();
  }

  public function validate_user( $request ) {
    $request['chatster_id'] = $this->get_customer_id();
    return true;
  }

  public function validate_message( $request ) {
    if ( $this->validate_user() ) {

    }

    $email = !empty($request['email']) ? $request['email'] : '';
    $email = $this->validate_email($email);
    // Checks if email is valid and honeypot field was sent and it is empty
    if ( $email && ( isset( $request['hname'] ) && empty( $request['hname'] ) ) ) {
        $request['email'] = $email;
        return true;
    }
    return false;
  }

  public function insert_msg_db( \WP_REST_Request $data) {

      return array('action'=> 'add');
      global $wpdb, $table_prefix;
      $product_id = $data['product_id'];
      $email = $data['email'];

      $tblname = self::table_name;
      $wp_table = $table_prefix . $tblname;

      $sql = " INSERT INTO $wp_table ( product_id, email, sent_id ) VALUES ( %d, %s, null ) ON DUPLICATE KEY UPDATE product_id = %d ";
      $sql = $wpdb->prepare( $sql, $product_id, $email, $product_id );
      $wpdb->query($sql);
      return array('action'=> 'add');
  }

  public function long_poll_db( \WP_REST_Request $data ) {

      // return array('action'=> $data['chatster_id'] );
      return array('action'=> $this->get_latest_messages() );
  }

}


new ChatApi();
