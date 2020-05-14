<?php

namespace Chatster\Options;

if ( ! defined( 'ABSPATH' ) ) exit;
require_once( CHATSTER_PATH . '/includes/options/class.options-global.php' );

class AddOptionsRequest extends OptionsGlobal {

  public static $option_group = 'chatster_request_options';

  public function __construct() {
    add_action( 'admin_init', array( $this, 'register_settings' ) );
  }

  public static function default_values() {
      return array(
          'ch_bot_intro' => 'Hi!! How can I help you today?',
      );

  }

  public function register_settings() {

    if ( ! current_user_can( 'manage_options' ) ) return;

    // register_setting(
    //         self::$option_group,
    //         self::$option_group,
    //         array( $this, 'validate_options') );
    //
    // add_settings_section(
    //         'ch_request_section',
    //         'Chatster Request Settings',
    //          array( $this, 'description' ),
    //         'chatster-menu' );
    //
    // add_settings_field(
    //         'ch_bot_intro',
    //         '',
    //          array( $this, 'text_field_callback'),
    //         'chatster-menu',
    //         'ch_bot_section',
    //         ['id'=>'ch_bot_intro',
    //          'label'=> 'Bot introductory sentence.',
    //          'description'=> 'Bot introductory sentece used when the chat is initially displayed. '] );

  }

  public function validate_options( $input ) {
    if ( ! current_user_can( 'manage_options' ) ) return;

    return $input;
  }

}

new AddOptionsRequest;
