<?php

namespace Chatster\Options;

if ( ! defined( 'ABSPATH' ) ) exit;
require_once( CHATSTER_PATH . '/includes/options/class.options-global.php' );

class AddOptionsBotQA extends OptionsGlobal {

  public static $success_set = false;
  public static $option_group = 'chatster_bot_qa_options';
  public static $fields_maxlength = [
                                      'ch_bot_qa_question' => 600,
                                      'ch_bot_qa_answer' => 600,
                                    ];

  public function __construct() {
    add_action( 'admin_init', array( $this, 'register_bot_settings' ) );
  }

  public function register_bot_settings() {

    if ( ! current_user_can( 'manage_options' ) ) return;

    register_setting(
            'chatster_bot_qa_options',
            'chatster_bot_qa_options',
             array( $this, 'validate_bot_qa_options') );

    add_settings_section(
            'ch_bot_qa_section',
            'Bot Q  &amp; A',
             array( $this, 'description' ),
            'chatster-menu' );

    add_settings_field(
            'ch_bot_qa_question',
            '',
             array( $this, 'textarea_field_callback'),
            'chatster-menu',
            'ch_bot_qa_section',
            ['id'=>'ch_bot_question',
             'label'=> 'Add a question or questions',
             'placeholder'=> 'What are your opening hours? What time do you open?',
             'required'=> true,
             'description'=> 'The Bot will look for similarities between saved questions and user question.'] );

     add_settings_field(
             'ch_bot_qa_answer',
             '',
              array( $this, 'textarea_field_callback'),
             'chatster-menu',
             'ch_bot_qa_section',
             ['id'=>'ch_bot_answer',
              'label'=> 'Bot Response to the question or questions',
              'placeholder'=> 'Our stores are open from 7 a.m. to 8:30 p.m.',
              'required'=> true,
              'description'=> 'This answer will be given when a similar question is asked.'] );

  }

  public function validate_bot_options( $input ) {
    if ( ! current_user_can( 'manage_options' ) ) return;
    // Q and A are saved on a dedicated table on the database.
    delete_option( 'chatster_bot_qa_options' );
    return false;
  }

}

new AddOptionsBotQA;
