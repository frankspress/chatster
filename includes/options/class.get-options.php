<?php

namespace Chatster\Options;

if ( ! defined( 'ABSPATH' ) ) exit;
require_once( CHATSTER_PATH . '/includes/options/class.add-options-bot.php' );
require_once( CHATSTER_PATH . '/includes/options/class.add-options-chat.php' );

class GetOptions {

  private static $bot_options;
  private static $chat_options;
  private static $request_options;

  public function __construct() {
    self::$bot_options = get_option( AddOptionsBot::$option_group, AddOptionsBot::default_values() );
    self::$chat_options = get_option( AddOptionsChat::$option_group, AddOptionsChat::default_values() );
    self::$request_options = get_option( AddOptionsRequest::$option_group, AddOptionsRequest::default_values() );
  }

  public function get_bot_option( String $option_name ) {
    return !empty(self::$bot_options[$option_name]) ? self::$bot_options[$option_name] : false;
  }

  public function get_chat_option( String $option_name ) {
    return !empty(self::$chat_options[$option_name]) ? self::$chat_options[$option_name] : false;
  }

  public function get_request_option( String $option_name ) {
    return !empty(self::$request_options[$option_name]) ? self::$request_options[$option_name] : false;
  }

}

//GLOBAL
$ChatsterOptions = new GetOptions();
