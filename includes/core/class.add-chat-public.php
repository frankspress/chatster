<?php

if ( ! defined( 'ABSPATH' ) ) exit;



/**
 * Adds the interactive chat form
 */
class ChatPublic
{

  private static $text_size_percent = [
                                        'small'=> 85,
                                        'medium'=> 100,
                                        'large'=> 120
                                      ];

  function __construct() {
      add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_front_js' ) , 30 );
      add_action( 'wp_footer', array( 'Chatster\Views\DisplayManager', 'public_chat_view'));
  }

  private function calc_font( $value, $size ) {
    if ( !empty(self::$text_size_percent[$size]) ) {
      $percent = self::$text_size_percent[$size];
      $new_size = ( $value  * $percent ) / 100;
      return strval(number_format($new_size, 2, '.', '') + 0);
    }
    return $value;
  }

  private function get_custom_css() {
    global $ChatsterOptions;

    // Chat Header Color
    $bg_color = $ChatsterOptions->get_chat_option('ch_chat_header_back_color');
    $text_color = $ChatsterOptions->get_chat_option('ch_chat_header_text_color');
    $custom_css  = "#chatster-container #ch-header { ";
    $custom_css .= "background-color: ".esc_attr( $bg_color )."; ";
    $custom_css .= "color: ".esc_attr( $text_color )."; ";
    $custom_css .= "}";

    $custom_css .= "#chatster-opener #ch-open-button { ";
    $custom_css .= "background-color: ".esc_attr( $bg_color )."; ";
    $custom_css .= "color: ".esc_attr( $text_color )."; ";
    $custom_css .= "}";

    // Chat Text Size
    $txt_size = $ChatsterOptions->get_chat_option('ch_chat_text_size');
    $header_size = $this->calc_font('1', $txt_size);
    $custom_css .= "#chatster-container #ch-header { ";
    $custom_css .= " font-size: ".esc_attr( $header_size )."em; ";
    $custom_css .= "}";

    return $custom_css;

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
      'bot_img_path' => CHATSTER_URL_PATH . 'assets/img/' . esc_js( $ChatsterOptions->get_bot_option( 'ch_bot_image' )). '.jpg',
      'chat_sound_vol' => (( 1 / 50 ) * intval($ChatsterOptions->get_chat_option( 'ch_chat_volume' ))),
      'chat_static_string' => [ 'bot_intro'=> $ChatsterOptions->get_bot_option( 'ch_bot_intro' ),
                                'bot_followup'=> $ChatsterOptions->get_bot_option( 'ch_bot_followup' ),
                                'bot_nomatch'=> $ChatsterOptions->get_bot_option( 'ch_bot_nomatch' )
                              ]
    ) );
    if ( !wp_style_is( 'fontawesome' ) && $ChatsterOptions->get_chat_option( 'ch_chat_fontawesome' ) ) {
        wp_enqueue_style( 'fontawesome', CHATSTER_FONTAWESOME_URL, false, '4.7.0' );
    }
    wp_enqueue_style( 'chatster-loader-pbl', CHATSTER_URL_PATH . 'assets/css/style-loaders.css');
    wp_enqueue_style( 'chatster-public', CHATSTER_URL_PATH . 'assets/css/style-public.css');
    wp_add_inline_style( 'chatster-public', $this->get_custom_css() );

  }

}

new ChatPublic();
