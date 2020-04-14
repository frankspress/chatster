<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function display_admin_settings() {
    if ( ! current_user_can( 'manage_options' ) ) return;

}
