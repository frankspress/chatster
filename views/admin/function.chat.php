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

                <div id="ch-attachments"><div id="link-id-1" data-link_id="1">Attachemnt1</div>
                                            <div id="link-id-2" data-link_id="19">Attachemnt2</div>
                                          </div>
                <div class="ch-input">
                  <textarea id="ch-reply" placeholder="Type here your message.." type="text" rows="1" maxlength="799"></textarea>
                </div>

                <div class="ch-input-link">
                  <input id="ch-reply-link" class="ch-chat-autocomplete" placeholder="Find a product or page.." type="text" maxlength="40">
                </div>
                <script src="https://cdn.jsdelivr.net/autocomplete.js/0.37.1/autocomplete.jquery.min.js"></script>

            </div>

       </div>

     </div>


    <?php

}
