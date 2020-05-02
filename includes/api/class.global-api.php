<?php

namespace Chatster\Api;

if ( ! defined( 'ABSPATH' ) ) exit;

class GlobalApi  {

  protected function format_timezone($dateTime = false) {
    if ( $dateTime ) {
      $dt = new \DateTime("now", chatter_get_timezone() );
      $dt->setTimestamp(strtotime($dateTime));
      return esc_attr( $dt->format('F d, Y h:i A') );
    }
    return $dateTime;
  }
}
