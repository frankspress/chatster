<?php

namespace Chatster\Api;

if ( ! defined( 'ABSPATH' ) ) exit;
require_once( CHATSTER_PATH . '/includes/core/trait.chat.php' );

use Chatster\Api\ChatCollection;
use Chatster\Core\Crypto;

class ChatApi  {
  use ChatCollection;

    private $customer_id;

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
    private function set_customer_id_cookie() {
       if ( empty($this->customer_id) ) {
         $this->customer_id = substr(md5(uniqid(rand(), true)), 0, 100);
       }
       return setrawcookie('unreg_chatster_id', base64url_encode( Crypto::encrypt( $this->customer_id ) ), (time() + 8419200), "/");
     }

    private function get_customer_id() {

      $current_user = wp_get_current_user();
      if ( $current_user && get_current_user_id() ) {
        return $this->customer_id = $current_user->user_email;
      }

      if ( isset($_COOKIE['unreg_chatster_id'])) {
        $this->customer_id = Crypto::decrypt(  base64url_decode(  $_COOKIE['unreg_chatster_id'] ) );
        return $this->set_customer_id_cookie();
      }

      return $this->set_customer_id_cookie();
    }

    public function validate_user( $request ) {

      if ( $this->get_customer_id() ) {
        $request['chatster_customer_id'] = $this->customer_id;
        return true;
      }

      return false;
    }

    public function validate_message( $request ) {
      if ( $this->validate_user( $request ) ) {

          return true;
      }

      return false;
    }

    public function insert_msg_db( \WP_REST_Request $data) {

        return array('action'=> $this->insert_new_message('frankieeeit@gmail.com', $data['chatster_customer_id'],$data['chatster_customer_id'], 'Holy shit!!!' ));
    }

    public function long_poll_db( \WP_REST_Request $data ) {

        return array('action'=> $this->get_latest_messages(1, $data['chatster_customer_id']) );
    }

}


new ChatApi();
