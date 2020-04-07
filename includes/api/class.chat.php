<?php

namespace Chatster\Api;

if ( ! defined( 'ABSPATH' ) ) exit;
require_once( CHATSTER_PATH . '/includes/api/trait.chat.php' );
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
                    'methods'  => 'GET',
                    'callback' => array( $this, 'insert_msg_db' ),
                    'permission_callback' => array( $this, 'validate_message' )
          ));
    });
  }

  public function poll_msg_route() {
    add_action('rest_api_init', function () {
      register_rest_route( 'chatster/v1', '/chat/poll', array(
                    'methods'  => 'GET',
                    'callback' => array( $this, 'long_poll_db' ),
                    'permission_callback' => array( $this, 'validate_user' )
          ));
    });
  }

  /**
   * Methods
   */

  public function validate_user( $request ) {
     return true;
     $email = !empty($request['email']) ? $request['email'] : '';
     $email = $this->validate_email($email);
     // Checks if email is valid and honeypot field was sent and it is empty
     if ( $email && ( isset( $request['hname'] ) && empty( $request['hname'] ) ) ) {
         $request['email'] = $email;
         return true;
     }
     return false;
  }

  public function validate_message( $request ) {
    return true;
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
      return array('action'=> 'add');
      // Create a self mock user from email list to feed to send_email
      // User must be admin and able to manage_options therefore must exist to send the mock email.
      $mock_requester = new stdClass();
      $mock_requester->user_name = wp_get_current_user()->user_login;
      $mock_requester->email = $data['test_email'];

      // Generate a random list of products
      global $wpdb;
      $products_merge = $wpdb->get_results( "  SELECT wp_posts.id as product_id , wp_posts.post_title as product_name
                                               FROM wp_posts
                                               INNER JOIN wp_postmeta ON ( wp_posts.id = wp_postmeta.post_id)
                                               WHERE wp_posts.post_type = 'product' AND wp_postmeta.meta_value = 'instock'
                                               ORDER BY RAND() LIMIT 3 ");
    }


}


new ChatApi();
