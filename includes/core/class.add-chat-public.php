<?php

if ( ! defined( 'ABSPATH' ) ) exit;



/**
 * Adds the interactive chat form
 */
class ChatPublic
{

  function __construct() {
      add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_js' ) , 30 );
      add_action( 'wp_footer', array( 'Chatster\Views\DisplayManager', 'public_chat_view'));
  }

  public function enqueue_front_js() {
    global $ChatsterOptions;

    wp_enqueue_script( 'chatster-public', CHATSTER_URL_PATH . 'assets/js/chat-public.js',  array('jquery'), 1.0, true);
    wp_enqueue_script( 'chatster-public-sound', CHATSTER_URL_PATH . 'assets/js/chat-sound.js',  array('jquery'), 1.0, true);
    wp_localize_script( 'chatster-public', 'chatsterDataPublic', array(
      'api_base_url' => esc_url_raw( rest_url('chatster/v1') ),
      'nonce' => wp_create_nonce( 'wp_rest' ),
      'no_image_link' => CHATSTER_URL_PATH . 'assets/img/no-image.jpg',
      'sound_file_path' => CHATSTER_URL_PATH . 'assets/sound/when',
      'chat_sound_vol' => (( 1 / 50 ) * intval($ChatsterOptions->get_chat_option( 'ch_chat_volume' )))
    ) );
    wp_enqueue_style( 'chatster-loader-pbl', CHATSTER_URL_PATH . 'assets/css/style-loaders.css');
    wp_enqueue_style( 'chatster-public', CHATSTER_URL_PATH . 'assets/css/style-public.css');
    $bg_color = $ChatsterOptions->get_chat_option('ch_chat_header_back_color');
    $text_color = $ChatsterOptions->get_chat_option('ch_chat_header_text_color');
    $custom_css = "#chatster-container #ch-header { background-color: ".esc_attr( $bg_color )."; color: ".esc_attr( $text_color )."; }";
    wp_add_inline_style( 'chatster-public', $custom_css );

  }



}

new ChatPublic();
