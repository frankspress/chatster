<?php

namespace Chatster\Api;

if ( ! defined( 'ABSPATH' ) ) exit;
require_once( CHATSTER_PATH . '/includes/core/trait.chat.php' );

use Chatster\Core\ChatCollection;

class ChatApiAdmin  {
  use ChatCollection;

  private $admin_email;

    public function __construct() {

      // $this->insert_msg_route();
      // $this->poll_msg_route();
      $this->poll_conv_route();
      $this->admin_presence_route();
      $this->admin_status_route();
    }

    /**
     * Routes
     */
    public function admin_presence_route() {
      add_action('rest_api_init', function () {
       register_rest_route( 'chatster/v1', '/chat/presence/admin', array(
                     'methods'  => 'POST',
                     'callback' => array( $this, 'set_presence_admin' ),
                     'permission_callback' => array( $this, 'validate_admin' )
           ));
      });
    }

    public function admin_status_route() {
      add_action('rest_api_init', function () {
       register_rest_route( 'chatster/v1', '/chat/is_active/admin', array(
                     'methods'  => 'POST',
                     'callback' => array( $this, 'set_admin_status' ),
                     'permission_callback' => array( $this, 'validate_status' )
           ));
      });
    }

    public function poll_conv_route() {
      add_action('rest_api_init', function () {
       register_rest_route( 'chatster/v1', '/chat/polling/admin', array(
                     'methods'  => 'POST',
                     'callback' => array( $this, 'get_admin_polling' ),
                     'permission_callback' => array( $this, 'validate_admin' )
           ));
      });
    }
    /**
     * Methods
     */
     public function get_admin_email() {
       if ( current_user_can( 'manage_options' ) ) {
         $current_user = wp_get_current_user();
         if ( $current_user && get_current_user_id() ) {
           return $this->admin_email = $current_user->user_email;
         }
       }
       return false;
     }

     public function validate_admin( $request ) {
       if ( $this->get_admin_email() ) {
         $request['chatster_admin_email'] = $this->admin_email;
         return true;
       }
       return false;
     }

     public function validate_status( $request ) {
       if ( $this->validate_admin( $request ) && isset($request['is_active']) ) {
         $request['is_active'] = filter_var($request['is_active'], FILTER_VALIDATE_BOOLEAN);
         return true;
       }
       return false;
     }

     public function validate_poll( $request ) {
       if ( $this->validate_admin( $request ) ) {

         $data['last_conv'] = isset( $data['last_conv'] ) ? intval($data['last_conv']) : 0;
         $data['current_conv'] = isset( $data['current_conv'] ) ? intval($data['current_conv']) : false;
         $data['last_message'] = isset( $data['last_message'] ) ? intval($data['last_message']) : 0;

         return true;
       }
       return false;
     }
    /**
     * Routes Callbacks
     */
    public function set_presence_admin( \WP_REST_Request $data ) {
        $this->insert_presence_admin( $this->admin_email );
        return array('action'=> 'presence');
    }

    public function set_admin_status( \WP_REST_Request $data ) {
        $this->change_admin_status( $this->admin_email, $data['is_active'] );
        return array('action'=> $data['is_active']);
    }

    public function get_admin_polling( \WP_REST_Request $data ) {

        for ($x = 0; $x <= 10; $x++) {
            $convs = $this->get_all_convs_admin( $this->admin_email, $data['last_conv'] );
            $messages = false;
            if ( $data['current_conv'] ) {
              $messages = $this->get_latest_messages( $data['current_conv'], $data['last_message'], $this->admin_email );
            }
            if ( $convs || $messages ) break;
            sleep(1);
        }

        return array( 'action'=>'polling', 'payload'=> array( 'convs' => $convs,
                                                              'current_conv' => $messages ) );

    }

}

new ChatApiAdmin();
