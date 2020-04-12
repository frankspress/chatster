<?php

namespace Chatster\Api;

if ( ! defined( 'ABSPATH' ) ) exit;
require_once( CHATSTER_PATH . '/includes/core/trait.chat.php' );
use Chatster\Api\ChatCollection;

class ChatApiAdmin  {

  use ChatCollection;

    public function __construct() {
      // $this->insert_msg_route();
      // $this->poll_msg_route();
      // $this->poll_conv_route();
      // $this->admin_presence_route();
    }

    /**
     * Routes
     */
    public function admin_presence_route() {
      add_action('rest_api_init', function () {
       register_rest_route( 'chatster/v1', '/chat/presence/admin', array(
                     'methods'  => 'POST',
                     'callback' => array( $this, 'insert_msg_db' ),
                     'permission_callback' => array( $this, 'validate_message' )
           ));
      });
    }


}
