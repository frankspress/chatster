<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function ism_add_link_to_woo_menu() {

    add_submenu_page( 'woocommerce', __( 'Chatster', CHATSTER_DOMAIN ), __( 'Chatster', CHATSTER_DOMAIN ), 'view_woocommerce_reports', 'chatster-general', function() {echo '';});
}
add_action('admin_menu', 'ism_add_link_to_woo_menu', 10);
