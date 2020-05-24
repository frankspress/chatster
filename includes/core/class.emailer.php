<?php

namespace Chatster\Core;

if ( ! defined( 'ABSPATH' ) ) exit;

class Emailer
{
  const REPLY_TEMPLATE = CHATSTER_PATH . 'views/email/template.reply-request.phtml';
  const NOTIFICATION_TEMPLATE = CHATSTER_PATH . 'views/email/template.notify-request.phtml';

  private function link_builder($string) {
    $url = '~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i';
    $string = preg_replace($url, '<a href="$0" target="_blank" title="$0">$0</a>', $string);
    return $string;
  }

  private function build_template( $Template, $body = '', $customer_name = '', $request ='' ) {
    Global $ChatsterOptions;
    $site_url_safe =  esc_url( get_site_url() );
    $customer_name_safe = ucfirst(esc_html( $customer_name  ));
    $body = $this->link_builder($body);
    $email_body_safe = wpautop( wp_kses( $body, wp_kses_allowed_html( 'post' ) ) );
    $request_safe = wpautop( wp_kses( $request, wp_kses_allowed_html( 'post' ) ) );
    
    $template = file_get_contents( self::$Template );
    $template = str_replace('{{ site_link }}', $site_url_safe, $template);
    $template = str_replace('{{ add_user_name }}', $customer_name_safe, $template);
    $template = str_replace('{{ header_img_link }}', esc_url( $ChatsterOptions->get_request_option('ch_response_header_url') ), $template);
    $template = str_replace('{{ received_request }}', $request_safe, $template);
    return $template;
  }

  public function send_reply_email( $request ) {

      add_filter('wp_mail_content_type', function() {
        return 'text/html';
      } );

      return wp_mail(
              esc_attr( $request->email ),
              esc_attr( 'RE: ' . ucfirst($request->subject) ),
              $this->build_template( 'REPLY_TEMPLATE', $request->reply, $request->name, $request->request ),
              $headers = array(),
              $attachments = array()
      );
  }

  public function send_notification_email( $request ) {

      add_filter('wp_mail_content_type', function() {
        return 'text/html';
      } );

      return wp_mail(
              esc_attr( $request->email ),
              esc_attr( 'RE: ' . ucfirst($request->subject) ),
              $this->build_template( 'NOTIFICATION_TEMPLATE', $request->reply, $request->name, $request->request ),
              $headers = array(),
              $attachments = array()
      );
  }

}
