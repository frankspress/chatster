<?php

if ( ! defined( 'ABSPATH' ) ) exit;


require_once( CHATSTER_PATH . '/includes/functions.global.php' );
require_once( CHATSTER_PATH . '/includes/core/trait.table-builder.php' );
require_once( CHATSTER_PATH . '/includes/activation/class.activation.php' );
require_once( CHATSTER_PATH . '/includes/activation/class.deactivation.php' );



if ( is_admin() ) {
  require_once( CHATSTER_PATH . '/includes/admin/class.add-options.php' );
  require_once( CHATSTER_PATH . '/includes/admin/class.admin-menu.php' );
  require_once( CHATSTER_PATH . '/includes/admin/class.validate-options.php' );
  require_once( CHATSTER_PATH . '/views/class.display-manager.php' );
}
