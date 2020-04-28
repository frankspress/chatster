<?php

namespace Chatster\Api;

if ( ! defined( 'ABSPATH' ) ) exit;
require_once( CHATSTER_PATH . '/includes/core/trait.request.php' );

use Chatster\Core\RequestCollection;

class RequestApiAdmin  {
  use RequestCollection;

  private $admin_email;

    public function __construct() {

      $this->reply_request_message();
      $this->delete_request_message();
      $this->insert_request_message();

    }

    /**
     * Routes
     */
    public function reply_request_message() {
      add_action('rest_api_init', function () {
       register_rest_route( 'chatster/v1', '/request/admin/send', array(
                     'methods'  => 'POST',
                     'callback' => array( $this, 'reply_request' ),
                     'permission_callback' => array( $this, 'validate_admin' )
           ));
      });
    }

    public function delete_request_message() {
      add_action('rest_api_init', function () {
       register_rest_route( 'chatster/v1', '/request/admin/delete', array(
                     'methods'  => 'POST',
                     'callback' => array( $this, 'delete_request' ),
                     'permission_callback' => array( $this, 'validate_admin' )
           ));
      });
    }
    // Public Route - Insert Request
    public function insert_request_message() {
      add_action('rest_api_init', function () {
       register_rest_route( 'chatster/v1', '/request/public/insert', array(
                     'methods'  => 'POST',
                     'callback' => array( $this, 'delete_request' ),
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

     /**
      * Routes Callbacks
      */
     public function reply_received_request( \WP_REST_Request $data ) {

        $result = $this->insert_reply( $this->admin_email, $data['message'], $data['request_id'] );
        return array( 'action'=>'reply_request', 'temp_id'=> $data['temp_id'], 'payload'=> $result );

     }

     public function delete_received_request( \WP_REST_Request $data ) {

        $result = $this->delete_request( $data['request_id'] );
        return array( 'action'=>'delete_request', 'payload'=> $result, 'request_id'=>$data['request_id'] );

     }
}

new RequestApiAdmin();
