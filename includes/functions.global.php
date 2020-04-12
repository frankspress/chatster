<?php

if ( ! defined( 'ABSPATH' ) ) exit;


if ( ! function_exists( 'chatter_get_timezone' ) ) {
	/**
	 *
	 * @return DateTimeZone Install DateTimeZone
	 */
	function chatter_get_timezone() {
		$timezone_string = get_option( 'timezone_string' );

		if ( $timezone_string ) {
			return new DateTimeZone( $timezone_string );
		}

		$offset  = (float) get_option( 'gmt_offset' );
		$hours   = (int) $offset;
		$minutes = ( $offset - $hours );

		$sign      = ( $offset < 0 ) ? '-' : '+';
		$abs_hour  = abs( $hours );
		$abs_mins  = abs( $minutes * 60 );
		$tz_offset = sprintf( '%s%02d:%02d', $sign, $abs_hour, $abs_mins );

		return new DateTimeZone( $tz_offset );
	}
}

function base64url_encode( $data ){
  return rtrim( strtr( base64_encode( $data ), '+/', '-_'), '=');
}

function base64url_decode( $data ){
  return base64_decode( strtr( $data, '-_', '+/') . str_repeat('=', 3 - ( 3 + strlen( $data )) % 4 ));
}

/***************************************************************
 * Testing
 */
 function dump($var) {
   var_dump($var);
 }
 function dd($var) {
   var_dump($var);
   die;
 }
