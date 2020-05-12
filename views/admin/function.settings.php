<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function display_admin_settings() {
    if ( ! current_user_can( 'manage_options' ) ) return; ?>




    <?php settings_errors('chatster_bot_options'); ?>
    <form id="chatster-options-form" action="options.php" method="post">
    <?php
 
          settings_fields( 'chatster_bot_options' );

          do_settings_sections( 'chatster-menu' );
          // submit_button($text = null, $type = 'primary', $name = 'submit-settings');
          submit_button(); ?>
    </form>


    <?php
}
