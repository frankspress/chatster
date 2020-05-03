<?php

namespace Chatster\Core;

if ( ! defined( 'ABSPATH' ) ) exit;
require_once( CHATSTER_PATH . '/includes/options/class.get-options.php' );
use Chatster\Options\GetOptions;

class Emailer
{
  const REPLY_TEMPLATE = CHATSTER_PATH . 'views/email/template.reply-request.phtml';

  private function build_template( $template_name = '', $body = '' ) {
    $getOptions = new GetOptions;
    $site_url_safe =  esc_url( get_site_url() );
    $email_body_safe = wpautop( wp_kses( $body, wp_kses_allowed_html( 'post' ) ) );
    $template = file_get_contents( self::REPLY_TEMPLATE );
    $template = str_replace('{{ site_link }}', $site_url_safe, $template);
    $template = str_replace('{{ header_img_link }}', esc_url( $getOptions->get_admin_option('ch_email_header_img_url') ), $template);
    $template = str_replace('{{ email_body }}', $email_body_safe, $template);
    return $template;
  }

  public function send_reply_email( $response ) {

      add_filter('wp_mail_content_type', function() {
        return 'text/html';
      } );

      return wp_mail(
              esc_attr( $response->email ),
              esc_attr( 'RE: ' . ucfirst($response->subject) ),
              $this->build_template( 'reply_template', $response->reply ),
              $headers = array(),
              $attachments = array()
      );
  }

}
