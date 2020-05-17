<?php

namespace Chatster\Api;

if ( ! defined( 'ABSPATH' ) ) exit;
require_once( CHATSTER_PATH . '/includes/core/trait.bot.php' );
require_once( CHATSTER_PATH . '/includes/api/class.global-api.php' );

use Chatster\Core\BotCollection;

class BotApi extends GlobalApi  {
  use BotCollection;


    public function __construct() {

      $this->reply_question_route();
      $this->update_admin_qa_route();

    }

  /**
   * Routes
   */
    public function reply_question_route() {
      add_action('rest_api_init', function () {
       register_rest_route( 'chatster/v1', '/bot/public/answer', array(
                     'methods'  => 'POST',
                     'callback' => array( $this, 'reply_question' ),
                     'permission_callback' => array( $this, 'validate_question' )
           ));
      });
    }

    public function update_admin_qa_route() {
      add_action('rest_api_init', function () {
       register_rest_route( 'chatster/v1', '/bot/admin/update', array(
                     'methods'  => 'POST',
                     'callback' => array( $this, 'bot_qa_insert' ),
                     'permission_callback' => array( $this, 'validate_qa_insert' )
           ));
      });
    }

    public function delete_admin_qa_route() {
      add_action('rest_api_init', function () {
       register_rest_route( 'chatster/v1', '/bot/admin/delete', array(
                     'methods'  => 'POST',
                     'callback' => array( $this, 'reply_question' ),
                     'permission_callback' => array( $this, 'validate_question' )
           ));
      });
    }


  /**
   * Validation Callbacks
   */
    public function validate_question( $request ) {
      if ( $this->validate_simple_request( $request ) ) {
        if ( isset( $request['user_question'] ) &&
                 strlen($request['user_question']) <= 349 ) {

          $request['user_question'] =  htmlentities( $request['user_question'], ENT_QUOTES, 'UTF-8');
          return true;

        }
      }
      return false;
    }

    public function validate_qa_insert( $request ) {

      if ( $this->validate_admin($request) ) {
          if ( is_string($request['answer']) && is_array( $request['questions']) ) {
               if ( strlen(trim($request['answer'])) == 0 || strlen(trim($request['answer'])) > 600 ) return false;
               $request['answer'] = htmlentities( $request['answer'], ENT_QUOTES, 'UTF-8');
               $sanitized_questions = array();
               foreach ( $request['questions'] as $question ) {
                   $question = trim($question);
                   if ( strlen($question) > 0 && strlen($question) <= 600 ) {
                     $sanitized_questions []= htmlentities( $question, ENT_QUOTES, 'UTF-8');
                   } else {
                     return false;
                   }
               }
               $request['questions'] = $sanitized_questions;
               return true;
          }
          return false;
      }
    }


  /**
   * Routes Callbacks
   */
    public function reply_question( \WP_REST_Request $data ) {

       $answer = $this->search_full_text($data['user_question'], $data['answer_ids']);
       if ( ! $answer ) {
         $answer = $this->search_full_text($data['user_question']);
       }


       return array( 'action'=>'bot_reply_question', 'payload'=> $answer );

    }

    public function bot_qa_insert( \WP_REST_Request $data ) {

       return array( 'action'=>'bot_qa_insert', 'payload'=> $data['questions']  );

    }

}

new BotApi();
