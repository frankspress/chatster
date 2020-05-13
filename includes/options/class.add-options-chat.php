<?php

namespace Chatster\Options;

if ( ! defined( 'ABSPATH' ) ) exit;
require_once( CHATSTER_PATH . '/includes/options/class.options-global.php' );

class AddOptionsChat extends OptionsGlobal {

  protected static $option_group = 'chatster_chat_options';

  public function __construct() {
    add_action( 'admin_init', array( $this, 'register_chat_settings' ) );
  }

  public function default_values() {

      return array(
          'ch_chat_header' => 'Chat'
      );

  }

  public function register_chat_settings() {

    if ( ! current_user_can( 'manage_options' ) ) return;

        register_setting(
                'chatster_chat_options',
                'chatster_chat_options',
                 array( $this, 'validate_chat_options') );

        add_settings_section(
                'ch_chat_section',
                'Chatster Chat Settings',
                 array( $this, 'description' ),
                'chatster-menu' );

        add_settings_field(
                'ch_chat_header',
                '',
                 array( $this, 'text_field_callback'),
                'chatster-menu',
                'ch_chat_section',
                ['id'=>'ch_chat_header',
                 'label'=> 'Chat header',
                 'description'=> 'Message stated at the top of the chat.'] );
  }

  public function validate_chat_options( $input ) {
    // if ( ! current_user_can( 'manage_options' ) ) return;
    //   // delete_option( 'chatster_chat_options' );
    //   //       return false;
    // if ( !empty($input['default_settings']) &&
    //         "reset" === $input['default_settings'] ) {
    //   delete_option( 'chatster_chat_options' );
    //   add_settings_error(
    //       'chatster_chat_options', // Setting slug
    //       'success_message',
    //       'Chatster Chat settings have been reset!',
    //       'success'
    //   );
    //   return false;
    // }

    return $input;
  }

}

new AddOptionsChat;
