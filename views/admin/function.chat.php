<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function display_admin_chat( $current_convs ) {
    if ( ! current_user_can( 'manage_options' ) ) return;

    dump($current_convs);
    echo "HI FROM THE CHAT";
}
