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
      $this->delete_admin_qa_route();
      $this->get_page_admin_qa_route();

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

    public function get_page_admin_qa_route() {
      add_action('rest_api_init', function () {
       register_rest_route( 'chatster/v1', '/bot/admin/(?P<page>\d+)/get-page', array(
                     'methods'  => 'POST',
                     'callback' => array( $this, 'bot_qa_get_page' ),
                     'args' => [
                          'product_id' => [
                              'validate_callback' => function($page) {
                                    return intval($page) > 0 ? intval($page) : false;
                                  },
                          ]
                      ],
                     'permission_callback' => array( $this, 'validate_admin' )
           ));
      });
    }

    public function delete_admin_qa_route() {
      add_action('rest_api_init', function () {
       register_rest_route( 'chatster/v1', '/bot/admin/(?P<answer_id>\d+)/delete', array(
                     'methods'  => 'POST',
                     'callback' => array( $this, 'bot_qa_delete' ),
                     'args' => [
                          'product_id' => [
                              'validate_callback' => function($answer_id) {
                                    return intval($answer_id) > 0 ? intval($answer_id) : 1;
                                  },
                          ]
                      ],
                     'permission_callback' => array( $this, 'validate_admin' )
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
               $request['answer_id'] = !empty($request['answer_id']) ? intval($request['answer_id']) : false;
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
       $is_update = $data['answer_id'] ? 1 : 0;
       if ( $is_update ) {
         $answer_id = $data['answer_id'];
         $this->delete_all_questions( $data['answer_id'] );
         $this->update_answer( $data['answer'], $data['answer_id'] );
       } else {
         $answer_id = $this->insert_answer( $data['answer'] );
       }
       $this->insert_questions( $data['questions'], $answer_id );

       $answer = $this->get_answer( $answer_id );
       $questions = $this->get_all_questions( $answer );

       return array( 'action'=>'bot_qa_insert', 'is_update'=> $is_update, 'payload'=> array('answer_data'=> array_shift($answer),
                                                                                            'questions' => $questions ) );

    }

    public function bot_qa_delete( \WP_REST_Request $data ) {

       $result = $this->delete_answer( $data['answer_id'] );
       return array( 'action'=>'bot_qa_insert', 'payload'=> $result  );

    }

    public function bot_qa_get_page( \WP_REST_Request $data ) {

       $count = $this->get_answer_count();
       $result_container = [];

       if ( $data['page'] <= ceil( $count / self::$per_page_qa ) ) {
         $answers = $this->get_all_answers( $data['page'], $count );
         $questions = $this->get_all_questions($answers);

         if ( $questions && $answers ) {
           foreach ($answers as $answer) {
               $question_container = [];
               $qa_container = [];

               foreach ($questions as $question) {
                   if ( $question->answer_id == $answer->id ) {
                     $question_container []= $question;
                   }
               }
               $qa_container['answer_data'] = $answer;
               $qa_container['questions'] = $question_container;
               $result_container []= $qa_container;
           }
         }
       }

       return array( 'action'=>'bot_qa_get_page', 'payload'=> $result_container );
    }


}

new BotApi();
