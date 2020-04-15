<?php

namespace Chatster\Options;

if ( ! defined( 'ABSPATH' ) ) exit;
require_once( CHATSTER_PATH . '/includes/options/class.validate-options.php' );

class AddOptions {

  public function __construct() {
    add_action( 'admin_init', array( $this, 'register_settings' ) );
  }

  public function register_settings() {

    register_setting(
            'chatster_options',
            'chatster_options',
            'chatster_validate_options' );


  }







}

new AddOptions;
