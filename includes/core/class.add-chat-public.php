<?php

if ( ! defined( 'ABSPATH' ) ) exit;



/**
 * Adds the interactive chat form
 */
class ChatPublic
{

  function __construct() {
      add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_js' ) , 30 );
      add_action( 'wp_footer', array( 'DisplayManager', 'public_chat_view'));
  }

  public function enqueue_front_js() {

    wp_enqueue_script( 'chatster-public', CHATSTER_URL_PATH . '/assets/js/chat-public.js',  array('jquery'), 1.0, true);
    wp_localize_script( 'chatster-public', 'chatsterDataAdmin', array(
      'api_base_url' => esc_url_raw( rest_url('chatster/v1') ),
      'nonce' => wp_create_nonce( 'wp_rest' )
    ) );

    wp_enqueue_style( 'chatster-public', CHATSTER_URL_PATH . '/assets/css/style-public.css');

  }



}

new ChatPublic();
