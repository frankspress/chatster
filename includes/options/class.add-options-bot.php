<?php

namespace Chatster\Options;

if ( ! defined( 'ABSPATH' ) ) exit;
require_once( CHATSTER_PATH . '/includes/options/class.options-global.php' );

class AddOptionsBot extends OptionsGlobal {

  public static $success_set = false;
  public static $option_group = 'chatster_bot_options';
  public static $fields_maxlength = [
                                      'ch_bot_nomatch' => 350,
                                      'ch_bot_followup' => 350,
                                      'ch_bot_intro' => 350,
                                      'ch_bot_name' => 10
                                    ];

  public function __construct() {
    add_action( 'admin_init', array( $this, 'register_bot_settings' ) );
  }

  public static function default_values() {

      return array(
          'ch_bot_name' => 'Chatster',
          'ch_bot_intro' => 'Hi!! How can I help you today?',
          'ch_bot_followup' => 'If you have any other questions please feel free to ask.',
          'ch_bot_nomatch' => 'Sorry, I couldn\'t find what you\'re looking for..
                                Please try again',
          'ch_bot_deep_search' => true,
          'ch_bot_product_lookup' => false,
          'ch_bot_image' => 'bot-1'
      );

  }

  public static function get_options_radio($option_name) {

      switch ($option_name) {

          case 'ch_bot_image':
              return array(
                  'bot-1'=>  CHATSTER_URL_PATH . 'assets/img/',
                  'bot-2'=>  CHATSTER_URL_PATH . 'assets/img/',
                  'bot-3'=>  CHATSTER_URL_PATH . 'assets/img/',
                  'bot-4'=>  CHATSTER_URL_PATH . 'assets/img/',
                  'bot-5'=>  CHATSTER_URL_PATH . 'assets/img/',
                  'bot-6'=>  CHATSTER_URL_PATH  . 'assets/img/'
              );
              break;
      }

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
            'ch_bot_name',
            '',
             array( $this, 'text_field_callback'),
            'chatster-menu',
            'ch_bot_section',
            ['id'=>'ch_bot_name',
             'label'=> 'Bot Name',
             'description'=> 'Give your bot your favorite name.'] );

    add_settings_field(
            'ch_bot_image',
            '',
             array( $this, 'radio_img_field_callback'),
            'chatster-menu',
            'ch_bot_section',
            ['id'=>'ch_bot_image',
             'label'=> 'Bot Image',
             'description'=> 'Give your bot a friendly image'] );

    add_settings_field(
            'ch_bot_intro',
            '',
             array( $this, 'textarea_field_callback'),
            'chatster-menu',
            'ch_bot_section',
            ['id'=>'ch_bot_intro',
             'label'=> 'Bot introductory sentence.',
             'description'=> 'Bot introductory sentece used when the chat is initially displayed.
                              <br><span class="ch-field-descr-extra">(Each line break is shown as separate message)</span>'] );

     add_settings_field(
             'ch_bot_followup',
             '',
              array( $this, 'textarea_field_callback'),
             'chatster-menu',
             'ch_bot_section',
             ['id'=>'ch_bot_followup',
              'label'=> 'Follow-up question',
              'description'=> 'The bot sentece that follows a successfull reply.
                               <br><span class="ch-field-descr-extra">(Each line break is shown as separate message)</span>'] );

     add_settings_field(
             'ch_bot_nomatch',
             '',
              array( $this, 'textarea_field_callback'),
             'chatster-menu',
             'ch_bot_section',
             ['id'=>'ch_bot_nomatch',
              'label'=> 'Nothing found response',
              'description'=> 'When no answer is found the bot will use this sentence.
                               <br><span class="ch-field-descr-extra">(Each line break is shown as separate message)</span>'] );

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

    if ( !empty($input['default_settings']) &&
            "reset" === $input['default_settings'] ) {
      delete_option( 'chatster_bot_options' );
      add_settings_error(
          'chatster_bot_options', // Setting slug
          'success_message_reset',
          'BOT settings have been reset!',
          'success'
      );
      return false;
    }

    $err_msg = '';
  	$options = get_option( static::$option_group , static::default_values() ) + static::default_values();

    foreach (array( 'ch_bot_name' ) as $value) {
      if ( isset($input[$value]) ) {
        if ( !is_string($input[$value]) || strlen($input[$value]) > self::get_maxlength($value) ) {
          $input[$value] = isset($options[$value]) ? $options[$value] : '';
          $err_msg .= __('A field text exceeds '.self::get_maxlength($value).' characters <br>', CHATSTER_DOMAIN);
        }
      }
    }

    foreach (array( 'ch_bot_intro', 'ch_bot_followup','ch_bot_nomatch' ) as $value) {
      if ( isset($input[$value]) ) {
        if ( !is_string($input[$value]) || strlen($input[$value]) > self::get_maxlength($value) ) {
          $input[$value] = isset($options[$value]) ? $options[$value] : '';
          $err_msg .= __('A field text exceeds '.self::get_maxlength($value).' characters <br>', CHATSTER_DOMAIN);
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

    foreach (array( 'ch_bot_image' ) as $value) {
      if ( isset($input[$value]) ) {
        $current_input = $input[$value];
        $input[$value] = $options[$value];
        $attr_array = array_keys(self::get_options_radio($value));
        if ( in_array( $current_input, $attr_array ) ) {
          $array_key = array_search($current_input, $attr_array);
          if ( $array_key ) {
            $input[$value] = $attr_array[$array_key];
          }
        }
      }
    }

    $this->add_success_message( $err_msg );
    return $input;
  }

}

new AddOptionsBot;
