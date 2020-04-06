<?php

if ( ! defined( 'ABSPATH' ) ) exit;

trait ChatsterTableBuilder {

    private static $presence = 'chatster_presence';
    private static $message = 'chatster_message';
    private static $conversation = 'chatster_conversation';
    private static $request = 'chatster_request';
    private static $reply = 'chatster_reply';

    public static function get_table_name($table = '') {
      global $table_prefix;
      return $table_prefix . self::$$table;
    }

}
