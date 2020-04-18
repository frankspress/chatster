<?php

namespace Chatster\Api;

if ( ! defined( 'ABSPATH' ) ) exit;
require_once( CHATSTER_PATH . '/includes/core/trait.chat.php' );

use Chatster\Core\ChatCollection;
use Chatster\Core\Crypto;
use Chatster\Core\CookieCatcher;

class ChatApi  {
  use ChatCollection;

    private $customer_id;
    private $customer_name;
    private $customer_email;
    private $customer_subject;

    public function __construct() {
      $this->presence_route();
      $this->form_data_route();
      $this->insert_msg_route();
      $this->poll_msg_route();
      $this->disconnect_chat_route();
    }

    /**
     * Routes
     */
    public function presence_route() {
      add_action('rest_api_init', function () {
       register_rest_route( 'chatster/v1', '/chat/presence/customer', array(
                     'methods'  => 'POST',
                     'callback' => array( $this, 'set_presence' ),
                     'permission_callback' => array( $this, 'validate_customer' )
           ));
      });
    }

    public function form_data_route() {
      add_action('rest_api_init', function () {
        register_rest_route( 'chatster/v1', '/chat/form-data', array(
                      'methods'  => 'POST',
                      'callback' => array( $this, 'insert_form_data_db' ),
                      'permission_callback' => array( $this, 'validate_customer_form' )
            ));
      });
    }

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
                      'permission_callback' => array( $this, 'validate_customer' )
            ));
      });
    }

    public function disconnect_chat_route() {
      add_action('rest_api_init', function () {
        register_rest_route( 'chatster/v1', '/chat/disconnect', array(
                      'methods'  => 'POST',
                      'callback' => array( $this, 'disconnect_chat_db' ),
                      'permission_callback' => array( $this, 'validate_customer' )
            ));
      });
    }

    /**
     * Methods
     */

    private function validate_name( $customer_name ) {
      if ( !empty($customer_name) && strlen( $customer_name ) <= 100 ) {
        return $customer_name;
      }
      return false;
    }

    private function set_customer_id_cookie() {
       if ( empty($this->customer_id) ) {
         $this->customer_id = substr(md5(uniqid(rand(), true)), 0, 100);
       }
       return setrawcookie('ch_ctmr_id', base64url_encode( Crypto::encrypt( $this->customer_id ) ), (time() + 8419200), "/");
     }

    private function set_customer_basics_cookie() {
        $cookie_set = true;
        if ( !empty($this->customer_name) ) {
          $cookie_set = setrawcookie('ch_ctmr_name', base64url_encode( Crypto::encrypt( $this->customer_name ) ), (time() + 8419200), "/") && $cookie_set;
        }
        if ( !empty($this->customer_email) ) {
          $cookie_set = setrawcookie('ch_ctmr_email', base64url_encode( Crypto::encrypt( $this->customer_email ) ), (time() + 8419200), "/") && $cookie_set;
        }
        return $cookie_set;
    }

    private function get_customer_id() {

      $current_user = wp_get_current_user();
      if ( $current_user && get_current_user_id() ) {
        return $this->customer_id = $current_user->user_email;
      }

      if ( isset($_COOKIE['ch_ctmr_id'])) {
        $this->customer_id = Crypto::decrypt(  base64url_decode( $_COOKIE['ch_ctmr_id'] ) );
        return $this->set_customer_id_cookie();
      }

      return $this->set_customer_id_cookie();
    }

    private function get_customer_basics( $request ) {

      $current_user = wp_get_current_user();
      if ( $current_user && get_current_user_id() ) {
          $this->customer_name = $current_user->user_nicename;
          $this->customer_email = $current_user->user_email;
          return true;
      }

      $this->customer_name = isset($request['customer_name']) ? $this->validate_name( $request['customer_name'] ) : false;
      $this->customer_email = isset($request['customer_email']) ? is_email($request['customer_email']) : false;

      if ( $this->customer_name || $this->customer_email ) {
          return $this->set_customer_basics_cookie();
      }

      $this->customer_name = isset( $_COOKIE['ch_ctmr_name']) ? Crypto::decrypt( base64url_decode( $_COOKIE['ch_ctmr_name'] ) ) : false;
      $this->customer_email = isset( $_COOKIE['ch_ctmr_email']) ? Crypto::decrypt( base64url_decode( $_COOKIE['ch_ctmr_email'] ) ) : false;

      if ( $this->customer_name || $this->customer_email ) {
          return $this->set_customer_basics_cookie();
      }

      return false;
    }

    /**
     * Validation Callbacks
     */
    public function validate_customer( $request ) {

      if ( $this->get_customer_id() ) {
        $request['chatster_customer_id'] = $this->customer_id;
        return true;
      }
      return false;
    }

    public function validate_message( $request ) {
      if ( $this->validate_customer( $request ) ) {
          return true;
      }
      return false;
    }

    public function validate_customer_form( $request ) {
      if ( $this->validate_customer( $request ) ) {
          $this->get_customer_basics( $request );
      }
      return false;
    }

    /**
     * Routes Callbacks
     */
    public function set_presence( \WP_REST_Request $data ) {
        $this->insert_presence_customer( $this->customer_id );
        return array('action'=> $this->customer_name);
    }

    public function insert_form_data_db( \WP_REST_Request $data ) {

        return array('action'=> 'form_data');
    }

    public function insert_msg_db( \WP_REST_Request $data) {

        return array('action'=> $this->insert_new_message('frankieeeit@gmail.com', $data['chatster_customer_id'],$data['chatster_customer_id'], 'Holy shit!!!' ));
    }

    public function long_poll_db( \WP_REST_Request $data ) {

        return array('action'=> $this->get_latest_messages(1, $data['chatster_customer_id']) );
    }

    public function disconnect_chat_db( \WP_REST_Request $data ) {

        return array('action'=> 'form_data');
    }

}


new ChatApi();
