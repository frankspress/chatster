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
    private $assigned_admin;

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
                      'permission_callback' => array( $this, 'validate_msg_poll' )
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

    private function validate_name( $customer_name = '' ) {
      if ( !empty($customer_name) && strlen( $customer_name ) <= 100 ) {
        return htmlentities( $customer_name, ENT_QUOTES, 'UTF-8');
      }
      return false;
    }

    private function validate_subject( $chat_subject = '') {
      if ( !empty($chat_subject) && strlen( $chat_subject ) <= 200 ) {
        return htmlentities( $chat_subject, ENT_QUOTES, 'UTF-8');
      }
      return false;
    }

    private function validate_email( $email = '' ) {
      if ( !empty($email) && is_email($email) ) {
        return htmlentities( $email, ENT_QUOTES, 'UTF-8');
      }
      return false;
    }

    private function set_customer_id_cookie() {
       if ( empty($this->customer_id) ) {
         $this->customer_id = substr(md5(uniqid(rand(), true)), 0, 100);
       }
       return setrawcookie('ch_ctmr_id', base64url_encode( Crypto::encrypt( $this->customer_id ) ), (time() + 8419200), "/");
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

      $request['customer_name'] = isset($request['customer_name']) ? $this->validate_name( $request['customer_name'] ) : false;
      $request['customer_email'] = isset($request['customer_email']) ? validate_email($request['customer_email']) : false;
      $request['chat_subject'] = isset($request['chat_subject']) ? $this->validate_subject($request['chat_subject']) : false;

      if ( CookieCatcher::set_form_data($request) ) {
        return CookieCatcher::set_cookie_form_data();
      }
      return CookieCatcher::deserialized_form_data();
    }

    private function set_assigned_admin() {
      // TODO
      $this->assigned_admin = 'frankieeeit@gmail.com';
    }
    /**
     * Validation Callbacks
     */
    public function validate_customer( $request ) {

      if ( $this->get_customer_id() ) {
        $request['chatster_customer_id'] = $this->customer_id;
        $this->set_assigned_admin();
        return true;
      }
      return false;
    }

    public function validate_message( $request ) {
      if ( $this->validate_customer( $request ) ) {
        if ( isset( $request['new_message'] ) &&
                 strlen($request['new_message']) <= 799 &&
                      isset( $request['temp_id'] ) ) {

          $request['new_message'] = nl2br( htmlentities( $request['new_message'], ENT_QUOTES, 'UTF-8'));
          $this->set_assigned_admin();
          return true;

        }
      }
      return false;
    }

    public function validate_msg_poll( $request ) {
      if ( $this->validate_customer( $request ) ) {

          $request['last_msg_id'] = isset( $request['last_msg_id'] ) ? intval($request['last_msg_id']) : 0;
          return true;

      }
      return false;
    }

    public function validate_customer_form( $request ) {
      if ( $this->validate_customer( $request ) ) {
          $this->get_customer_basics( $request );
          return true;
      }
      return false;
    }

    /**
     * Routes Callbacks
     */
    public function set_presence( \WP_REST_Request $data ) {
        $this->insert_presence_customer( $this->customer_id );
        return array('action'=> $this->customer_id);
    }

    public function insert_form_data_db( \WP_REST_Request $data ) {
        if ( !empty(CookieCatcher::serialized_form_data(false))) {
          $this->insert_form_data( $this->customer_id, CookieCatcher::serialized_form_data(false) );
        }
        return array('action'=> 'form_data');
    }

    public function insert_msg_db( \WP_REST_Request $data) {

      $result = $this->insert_new_message( $this->assigned_admin, $this->customer_id, $this->customer_id, $data['new_message'], $data['temp_id'] );
      return array( 'action'=>'chat_insert', 'payload'=> $result, 'temp_id'=> $data['temp_id'] );
    }

    public function long_poll_db( \WP_REST_Request $data ) {
        for ($x = 0; $x <= 10; $x++) {
            $current_conv = $this->get_latest_messages_public( $data['last_msg_id'], $this->assigned_admin, $this->customer_id );
            if ( $current_conv ) {
              $conv_id = $this->get_current_conv_public($this->assigned_admin, $this->customer_id);
              $this->set_message_read( $this->customer_id, $conv_id, $data['last_msg_id'] );
              break;
            };
            usleep(700000);
        }
        return array('action'=>'polling', 'payload'=> $current_conv );
    }

    public function disconnect_chat_db( \WP_REST_Request $data ) {

        return array('action'=> 'form_data');
    }

}


new ChatApi();
