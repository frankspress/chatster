<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function display_admin_settings() {

    if ( ! current_user_can( 'manage_options' ) ) return; ?>

    <?php settings_errors('chatster_bot_options'); ?>
    <form id="chatster-bot-options-form" action="options.php" method="post">
    <?php

          settings_fields( 'chatster_bot_options' );
          do_ch_settings_section( 'chatster-menu' , 'ch_bot_section');
          submit_button($text = null, $type = 'primary', $name = 'submit-settings',$wrap = true, $other_attributes = ['id'=>'save-bot']); ?>
    </form>

    <?php  settings_errors('chatster_chat_options'); ?>
    <form id="chatster-chat-options-form" action="options.php" method="post">
    <?php

        settings_fields( 'chatster_chat_options' );
        do_ch_settings_section( 'chatster-menu', 'ch_chat_section' );
        submit_button($text = null, $type = 'primary', $name = 'submit-settings',$wrap = true, $other_attributes = ['id'=>'save-chat']);
        ?>
    </form>

    <?php
}
