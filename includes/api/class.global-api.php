<?php

namespace Chatster\Api;

if ( ! defined( 'ABSPATH' ) ) exit;

class GlobalApi  {

  public function get_admin_email() {
    if ( current_user_can( 'manage_options' ) ) {
      $current_user = wp_get_current_user();
      if ( $current_user && get_current_user_id() ) {
        return $this->admin_email = $current_user->user_email;
      }
    }
    return false;
  }

  public function validate_admin( $request ) {
    if ( $this->get_admin_email() ) {
      $request['chatster_admin_email'] = $this->admin_email;
      return true;
    }
    return false;
  }

  protected function format_timezone($dateTime = false) {
    if ( $dateTime ) {
      $dt = new \DateTime("now", chatter_get_timezone() );
      $dt->setTimestamp(strtotime($dateTime));
      return esc_attr( $dt->format('F d, Y h:i A') );
    }
    return $dateTime;
  }

  public function validate_simple_request( $request ) {
    // TODO
    return true;
  }

  public function validate_name( $customer_name = '' ) {
    if ( !empty($customer_name) && strlen( $customer_name ) <= 100 ) {
      return htmlentities( $customer_name, ENT_QUOTES, 'UTF-8');
    }
    return false;
  }

  public function validate_subject( $chat_subject = '') {
    if ( !empty($chat_subject) && strlen( $chat_subject ) <= 200 ) {
      return htmlentities( $chat_subject, ENT_QUOTES, 'UTF-8');
    }
    return false;
  }

  public function validate_email( $email = '' ) {
    if ( !empty($email) && is_email($email) ) {
      return htmlentities( $email, ENT_QUOTES, 'UTF-8');
    }
    return false;
  }

  public function validate_request_msg( $message = '') {
    if ( !empty($message) && strlen( $message ) <= 1500 ) {
      return nl2br( htmlentities( $message, ENT_QUOTES, 'UTF-8'));
    }
    return false;
  }

}
