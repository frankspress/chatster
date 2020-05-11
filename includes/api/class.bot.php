<?php

namespace Chatster\Api;

if ( ! defined( 'ABSPATH' ) ) exit;
require_once( CHATSTER_PATH . '/includes/core/trait.bot.php' );
require_once( CHATSTER_PATH . '/includes/api/class.global-api.php' );

use Chatster\Core\BotCollection;

class BotApi extends GlobalApi  {
  use BotCollection;


    public function __construct() {

      $this->reply_answer_route();

    }

  /**
   * Routes
   */
    public function reply_answer_route() {
      add_action('rest_api_init', function () {
       register_rest_route( 'chatster/v1', '/bot/public/reply', array(
                     'methods'  => 'POST',
                     'callback' => array( $this, 'reply_question' ),
                     'permission_callback' => array( $this, 'validate_simple_request' )
           ));
      });
    }

  /**
   * Routes Callbacks
   */
    public function reply_question( \WP_REST_Request $data ) {

       $answer = $this->search_full_text($data['bot_question'], $data['excluded_ids']);
       return array( 'action'=>'bot_reply_question', 'payload'=> $answer );

    }

}

new BotApi();
