<?php

namespace Chatster\Options;

if ( ! defined( 'ABSPATH' ) ) exit;
require_once( CHATSTER_PATH . '/includes/options/class.options-global.php' );

class AddOptionsBot extends OptionsGlobal {

  protected static $option_group = 'chatster_bot_options';

  public function __construct() {
    add_action( 'admin_init', array( $this, 'register_bot_settings' ) );
  }

  public function default_values() {

      return array(
          'ch_bot_intro' => 'Hi!! How can I help you today?',
          'ch_bot_followup' => 'If you have any other questions please feel free to ask.',
          'ch_bot_nomatch' => 'Sorry, I couldn\'t find what you\'re looking for.. <br>Please try again',
          'ch_bot_deep_search' => true,
          'ch_bot_product_lookup' => false,
      );

  }

  public function register_bot_settings() {

    if ( ! current_user_can( 'manage_options' ) ) return;

    register_setting(
            'chatster_bot_options',
            'chatster_bot_options',
             array( $this, 'validate_bot_options') );

    add_settings_section(
            'ch_bot_section',
            'Chatster Bot Settings',
             array( $this, 'description' ),
            'chatster-menu' );

    add_settings_field(
            'ch_bot_intro',
            '',
             array( $this, 'text_field_callback'),
            'chatster-menu',
            'ch_bot_section',
            ['id'=>'ch_bot_intro',
             'label'=> 'Bot introductory sentence.',
             'description'=> 'Bot introductory sentece used when the chat is initially displayed. '] );

     add_settings_field(
             'ch_bot_followup',
             '',
              array( $this, 'text_field_callback'),
             'chatster-menu',
             'ch_bot_section',
             ['id'=>'ch_bot_followup',
              'label'=> 'Follow-up question',
              'description'=> 'The bot sentece that follows a successfull reply.'] );

     add_settings_field(
             'ch_bot_nomatch',
             '',
              array( $this, 'text_field_callback'),
             'chatster-menu',
             'ch_bot_section',
             ['id'=>'ch_bot_nomatch',
              'label'=> 'Nothing found response',
              'description'=> 'When no answer is found the bot will use this sentence.'] );

     add_settings_field(
             'ch_bot_deep_search',
             '',
              array( $this, 'switch_field_callback'),
             'chatster-menu',
             'ch_bot_section',
             ['id'=>'ch_bot_deep_search',
              'label'=> 'Enable Deep Search',
              'description'=> 'BOT will search full text in both questions and answers. <br>When not enabled it will only search among the saved questions.'] );

     add_settings_field(
             'ch_bot_product_lookup',
             '',
              array( $this, 'switch_field_callback'),
             'chatster-menu',
             'ch_bot_section',
             ['id'=>'ch_bot_product_lookup',
              'label'=> 'Enable Product Lookup',
              'description'=> 'Matching product links with thumbnail will be listed along with the found answer.'] );

  }

  public function validate_bot_options( $input ) {
    if ( ! current_user_can( 'manage_options' ) ) return;
      // delete_option( 'chatster_bot_options' );
      //       return false;
    if ( !empty($input['default_settings']) &&
            "reset" === $input['default_settings'] ) {
      delete_option( 'chatster_bot_options' );
      add_settings_error(
          'chatster_bot_options', // Setting slug
          'success_message',
          'Chatster BOT settings have been reset!',
          'success'
      );
      return false;
    }

    $err_msg = '';
    $options = get_option( 'chatster_bot_options', $this->default_values() );

    foreach (array( 'ch_bot_intro', 'ch_bot_followup','ch_bot_nomatch' ) as $value) {
      if ( isset($input[$value]) ) {
        if ( !is_string($input[$value]) || strlen($input[$value]) > 350 ) {
          $input[$value] = isset($options[$value]) ? $options[$value] : '';
          $err_msg .= __('Field text exceeds 350 characters <br>', CHATSTER_DOMAIN);
        }
      }
    }

    foreach (array( 'ch_bot_deep_search', 'ch_bot_product_lookup' ) as $value) {
      if ( isset($input[$value]) ) {
        $input[$value] = rest_sanitize_boolean( $input[$value] );
      } else {
        $input[$value] = null;
      }
    }

    if ( !empty( $err_msg ) ) {
      add_settings_error(
          'chatster_bot_options', // Setting slug
          'error_message',
           $err_msg,
          'error'
      );
    } else {
       add_settings_error(
          'chatster_bot_options', // Setting slug
          'success_message',
          'Settings Saved!',
          'success'
      );
    }

    return $input;
  }

}

new AddOptionsBot;