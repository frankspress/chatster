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
          'ch_response_header_url' => '',
          'ch_request_alert' => false,
          'ch_request_alert_email' => '',
          'ch_request_test_email' => '',
      );

  }

  public function register_settings() {

    if ( ! current_user_can( 'manage_options' ) ) return;

    register_setting(
            self::$option_group,
            self::$option_group,
            array( $this, 'validate_options') );

    add_settings_section(
            'ch_request_section',
            'Chatster Request Settings',
             array( $this, 'description' ),
            'chatster-menu' );

    add_settings_section(
            'ch_request_test_section',
            'Test Functionality',
             array( $this, 'description' ),
            'chatster-menu' );

    add_settings_field(
            'ch_response_header_url',
            '',
             array( $this, 'text_field_callback'),
            'chatster-menu',
            'ch_request_section',
            ['id'=>'ch_response_header_url',
             'label'=> 'Email Header Image',
             'placeholder' => 'https://..',
             'description'=> 'Your response email can display an header image.<br>
                              Go to Media -> Library -> Add New, then copy and paste the link in this field.<br>
                              (Aspect ratio 600 X 300 px.)']
                            );

    add_settings_field(
            'ch_request_alert',
            '',
             array( $this, 'switch_field_callback'),
            'chatster-menu',
            'ch_request_section',
            ['id'=>'ch_request_alert',
             'label'=> 'Receive Email Alert',
             'placeholder' => 'https://..',
             'description'=> 'Receive an email when a new request is submitted.<br>
                              (Wordpress will check for new requests every hour.)']
                            );
    add_settings_field(
            'ch_request_alert_email',
            '',
             array( $this, 'email_field_callback'),
            'chatster-menu',
            'ch_request_section',
            ['id'=>'ch_request_alert_email',
             'label'=> 'Your Email',
             'required'=> true,
             'placeholder' => 'Ex: your@email.com',
             'description'=> 'Receive an email when a new request is submitted.<br>
                              (Wordpress will check for new requests every hour.)']
                            );
    add_settings_field(
            'ch_request_test_email',
            '',
             array( $this, 'email_field_callback'),
            'chatster-menu',
            'ch_request_test_section',
            ['id'=>'ch_request_test_email',
             'label'=> 'Enter an Email address to send test email',
             'required'=> true,
             'placeholder' => 'Ex: test@email.com',
             'description'=> 'Receive an email when a new request is submitted.<br>
                              (Wordpress will check for new requests every hour.)']
                            );

  }

  public function validate_options( $input ) {
    if ( ! current_user_can( 'manage_options' ) ) return;

    if ( !empty($input['default_settings']) &&
            "reset" === $input['default_settings'] ) {
      delete_option( self::$option_group );
      add_settings_error(
          self::$option_group, // Setting slug
          'success_message',
          'Chatster Request settings have been reset!',
          'success'
      );
      return false;
    }

    $err_msg = '';
    $input['ch_request_test_email'] = '';
  	$options = get_option( static::$option_group , static::default_values() );

    // TODO

    $this->add_success_message( $err_msg );
    return $input;
  }

}

new AddOptionsRequest;
