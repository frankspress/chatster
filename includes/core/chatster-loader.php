<?php


if ( ! defined( 'ABSPATH' ) ) exit;

require_once( CHATSTER_PATH . '/includes/core/class.cookie.php' );
require_once( CHATSTER_PATH . '/includes/functions.global.php' );
require_once( CHATSTER_PATH . '/includes/activation/class.activation.php' );
require_once( CHATSTER_PATH . '/includes/activation/class.deactivation.php' );
require_once( CHATSTER_PATH . '/includes/api/class.chat.php' );
require_once( CHATSTER_PATH . '/includes/core/class.add-chat-public.php' );
require_once( CHATSTER_PATH . '/views/class.display-manager.php' );

if ( is_admin() ) {

  require_once( CHATSTER_PATH . '/includes/options/class.add-options.php' );
  require_once( CHATSTER_PATH . '/includes/options/class.validate-options.php' );
  require_once( CHATSTER_PATH . '/includes/api/class.chat-admin.php' );
  require_once( CHATSTER_PATH . '/includes/core/class.add-admin-menu.php' );

}
