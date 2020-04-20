<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function display_admin_chat( $admin_status ) {
    if ( ! current_user_can( 'manage_options' ) ) return; ?>

    <div class="wrap">

      <div class="onoffswitch">
          <input type="checkbox" name="onoffswitch" class="onoffswitch-checkbox" id="chatster-chat-switch" <?php echo $admin_status ? 'checked' : ''; ?>>
          <label class="onoffswitch-label" for="chatster-chat-switch">
              <span class="onoffswitch-inner"></span>
              <span class="onoffswitch-switch"></span>
          </label>
      </div>

      <div id="ch-conversation-container">

            <div id="conversations-block" data-last_conv_id="">

              <div id="ch-load-conv-container">
                <div id="ch-empty-conv-msg"> Your conversations will be shown here.. </div>
                <div id="ch-roller-container" class="hidden">
                  <div class="ch-roller" ><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>
                </div>
              </div>

            </div>


            <div id="ch-reply-block">

                <div id="ch-message-board" data-conv_id=""  data-last_msg_id="" data-customer_id="">
                </div>

                <div class="ch-input">
                  <textarea id="ch-reply" placeholder="Type here your message.." type="text" rows="2" maxlength="799"></textarea>
                </div>

            </div>

       </div>

     </div>


    <?php

}
