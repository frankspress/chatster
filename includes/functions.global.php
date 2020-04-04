<?php

namespace Frankspress\Chatster\GlobalFunction;

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
