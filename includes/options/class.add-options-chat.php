<?php

namespace Chatster\Options;

if ( ! defined( 'ABSPATH' ) ) exit;
require_once( CHATSTER_PATH . '/includes/options/class.options-global.php' );

class AddOptionsChat extends OptionsGlobal {

  public static $success_set = false;
  public static $option_group = 'chatster_chat_options';
  public static $fields_maxlength = [
                                      'ch_chat_header' => 90,
                                      'ch_chat_intro' => 100
                                    ];

  public function __construct() {
    add_action( 'admin_init', array( $this, 'register_chat_settings' ) );
  }

  public static function default_values() {

      return array(
          'ch_chat_header' => 'Chat or get in touch!',
          'ch_chat_intro' => 'Contact Us',
          'ch_chat_header_back_color' => '#04346D',
          'ch_chat_header_text_color' => '#FAFAFA',
          'ch_chat_screen_position' => 'right',
          'ch_chat_text_size' => 'medium',
          'ch_chat_volume' => 25,
          'ch_chat_volume_admin' => 25,
          'ch_chat_max_conv' => 20,
          'ch_chat_auto_offline' => 5,
          'ch_chat_remove_offline_conv_int' => 5,
          'ch_chat_remove_offline_conv' => true
      );

  }

  public static function get_options_select($option_name) {

      switch ($option_name) {

          case 'ch_chat_max_conv':
              return array(
                  '5 '.__('Customers', CHATSTER_DOMAIN) =>  5,
                  '10 '.__('Customers', CHATSTER_DOMAIN) => 10,
                  '15 '.__('Customers', CHATSTER_DOMAIN) => 15,
                  '20 '.__('Customers', CHATSTER_DOMAIN) => 20,
                  '25 '.__('Customers', CHATSTER_DOMAIN) => 25
              );
              break;

          case 'ch_chat_auto_offline':
              return array(
                  '1 '.__('Minute', CHATSTER_DOMAIN)   => 1,
                  '3 '.__('Minutes', CHATSTER_DOMAIN)  => 3,
                  '5 '.__('Minutes', CHATSTER_DOMAIN)  => 5,
                  '10 '.__('Minutes', CHATSTER_DOMAIN) => 10
              );
              break;

          case 'ch_chat_remove_offline_conv_int':
              return array(
                  '3 '.__('Minutes', CHATSTER_DOMAIN)  => 3,
                  '5 '.__('Minutes', CHATSTER_DOMAIN)  => 5,
                  '10 '.__('Minutes', CHATSTER_DOMAIN) => 10
              );
              break;
      }

  }

  public static function get_options_radio($option_name) {

      switch ($option_name) {

          case 'ch_chat_text_size':
              return array(
                  'small'=> '<h5>'.__('Small Text', CHATSTER_DOMAIN).'</h5>',
                  'medium'=> '<h4>'.__('Medium Text', CHATSTER_DOMAIN).'</h4>',
                  'large'=> '<h3>'.__('Large Text', CHATSTER_DOMAIN).'</h3>'
              );
              break;
          case 'ch_chat_screen_position':
              return array(
                  'left'=> __('Left Side of the screen', CHATSTER_DOMAIN),
                  'right'=> __('Right Side of the screen', CHATSTER_DOMAIN)
              );
              break;
      }

  }

  public function register_chat_settings() {

    if ( ! current_user_can( 'manage_options' ) ) return;

        register_setting(
                'chatster_chat_options',
                'chatster_chat_options',
                 array( $this, 'validate_chat_options') );

        add_settings_section(
                'ch_chat_section',
                'Front Chat Settings',
                 array( $this, 'description' ),
                'chatster-menu' );

        add_settings_section(
                'ch_chat_admin_section',
                'Admin Chat Settings',
                 array( $this, 'description' ),
                'chatster-menu' );

        // --- ch_chat_section ---

        add_settings_field(
                'ch_chat_header_back_color',
                '',
                 array( $this, 'color_picker_field_callback'),
                'chatster-menu',
                'ch_chat_section',
                ['id'=>'ch_chat_header_back_color',
                 'label'=> 'Header Background Color',
                 'description'=> 'Message stated at the top of the chat.'] );

        add_settings_field(
                'ch_chat_header_text_color',
                '',
                 array( $this, 'color_picker_field_callback'),
                'chatster-menu',
                'ch_chat_section',
                ['id'=>'ch_chat_header_text_color',
                 'label'=> 'Header Text Color',
                 'description'=> 'Message stated at the top of the chat.'] );

       add_settings_field(
               'ch_chat_intro',
               '',
                array( $this, 'text_field_callback'),
               'chatster-menu',
               'ch_chat_section',
               ['id'=>'ch_chat_intro',
                'label'=> 'Chat Intro',
                'description'=> 'The main link that opens the chat. (Contact Us, Chat, Send a message, etc.)'] );

       add_settings_field(
               'ch_chat_header',
               '',
                array( $this, 'text_field_callback'),
               'chatster-menu',
               'ch_chat_section',
               ['id'=>'ch_chat_header',
                'label'=> 'Chat Header',
                'description'=> 'Message stated at the top of the chat.'] );

       add_settings_field(
               'ch_chat_text_size',
               '',
                array( $this, 'radio_field_callback'),
               'chatster-menu',
               'ch_chat_section',
               ['id'=>'ch_chat_text_size',
                'label'=> 'Text Size',
                'description'=> ''] );

        add_settings_field(
                'ch_chat_volume',
                '',
                 array( $this, 'range_field_callback'),
                'chatster-menu',
                'ch_chat_section',
                ['id'=>'ch_chat_volume',
                 'label'=> 'New Message Sound',
                 'description'=> 'Chat will emit a sound when the customer receives a new message.'] );

        add_settings_field(
               'ch_chat_screen_position',
               '',
                array( $this, 'radio_field_callback'),
               'chatster-menu',
               'ch_chat_section',
               ['id'=>'ch_chat_screen_position',
                'label'=> 'New Message Sound',
                'description'=> ''] );

        // --- ch_chat_admin_section ---
        add_settings_field(
                'ch_chat_max_conv',
                '',
                 array( $this, 'option_field_callback'),
                'chatster-menu',
                'ch_chat_admin_section',
                ['id'=>'ch_chat_max_conv',
                 'label'=> 'Max number of conversations',
                 'description'=> 'Requests received after the limit is reached will be put on hold.'] );

        add_settings_field(
                'ch_chat_auto_offline',
                '',
                 array( $this, 'option_field_callback'),
                'chatster-menu',
                'ch_chat_admin_section',
                ['id'=>'ch_chat_auto_offline',
                 'label'=> 'Automatic Offline',
                 'description'=> 'Online chat will automatically go offline when not in use.<br/>You can choose how long before that happens.'] );

        add_settings_field(
                'ch_chat_remove_offline_conv',
                '',
                 array( $this, 'switch_field_callback'),
                'chatster-menu',
                'ch_chat_admin_section',
                ['id'=>'ch_chat_remove_offline_conv',
                 'label'=> 'Remove Offlined Convs',
                 'description'=> 'Automatically removes conversations that have been disconnected from either side.'] );

        add_settings_field(
                'ch_chat_remove_offline_conv_int',
                '',
                 array( $this, 'option_field_callback'),
                'chatster-menu',
                'ch_chat_admin_section',
                ['id'=>'ch_chat_remove_offline_conv_int',
                 'label'=> 'Remove Offlined Convs',
                 'description'=> 'Automatically removes conversations that have been disconnected from either side.'] );

         add_settings_field(
                 'ch_chat_volume_admin',
                 '',
                  array( $this, 'range_field_callback'),
                 'chatster-menu',
                 'ch_chat_admin_section',
                 ['id'=>'ch_chat_volume_admin',
                  'label'=> 'Conversation Sounds',
                  'description'=> 'Select the volume level for the "Admin Chat" sound effects.'] );


  }

  public function validate_chat_options( $input ) {
    if ( ! current_user_can( 'manage_options' ) ) return;

    if ( !empty($input['default_settings']) &&
            "reset" === $input['default_settings'] ) {
      delete_option( 'chatster_chat_options' );
      add_settings_error(
          'chatster_chat_options', // Setting slug
          'success_message',
          'Chatster Chat settings have been reset!',
          'success'
      );
      return false;
    }

    $err_msg = '';
  	$options = get_option( static::$option_group , static::default_values() );

    foreach (array( 'ch_chat_intro', 'ch_chat_header' ) as $value) {
      if ( isset($input[$value]) ) {
        if ( !is_string($input[$value]) || strlen($input[$value]) > self::get_maxlength($value) ) {
          $input[$value] = isset($options[$value]) ? $options[$value] : '';
          $err_msg .= __('A field text exceeds '.self::get_maxlength($value).' characters <br>', CHATSTER_DOMAIN);
        }
      }
    }

    $this->add_success_message( $err_msg );

    return $input;
  }

}

new AddOptionsChat;
