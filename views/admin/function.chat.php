<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function display_admin_chat( $current_convs, $admin_status ) {
    if ( ! current_user_can( 'manage_options' ) ) return; ?>

    <div class="onoffswitch">
        <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="chatster-chat-switch" <?php echo $admin_status ? 'checked' : ''; ?>>
        <label class="onoffswitch-label" for="chatster-chat-switch">
            <span class="onoffswitch-inner"></span>
            <span class="onoffswitch-switch"></span>
        </label>
    </div>


    <?php dump($current_convs);

}
