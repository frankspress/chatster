<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function display_admin_settings() {

    if ( ! current_user_can( 'manage_options' ) ) return; ?>

    <div id="ch-options-main-container">

      <div class="ch-option-block" style="border: 1px solid #CCCCCC">
        <div class="ch-option-title">Bot Setup</div>
        <div id="bot-options" class="ch-option-container" style="display:none;">
          <form id="chatster-bot-options-form" action="options.php#bot-options" method="post">
          <?php
                settings_errors('chatster_bot_options');
                settings_fields( 'chatster_bot_options' );
                do_ch_settings_section( 'chatster-menu' , 'ch_bot_section'); ?>
                <div class="ch-reply-btn">
                  <?php submit_button($text = null, $type = 'primary', $name = 'submit-settings',$wrap = true, $other_attributes = ['id'=>'save-bot']); ?>
                  <p class="submit"><input type="submit" name="submit-default" class="button button-primary submit-reset" value="Reset Settings"></p>
                </div>
          </form>
        </div>
      </div>

      <div class="ch-option-block"  style="border: 1px solid #CCCCCC">
        <div class="ch-option-title">Chat Configuration</div>
        <div id="chat-options" class="ch-option-container" style="display:none;">
          <form id="chatster-chat-options-form" action="options.php#chat-options" method="post">
          <?php
              settings_errors('chatster_chat_options');
              settings_fields( 'chatster_chat_options' );
              do_ch_settings_section( 'chatster-menu', 'ch_chat_section' ); ?>
              <div class="ch-reply-btn">
                <?php submit_button($text = null, $type = 'primary', $name = 'submit-settings',$wrap = true, $other_attributes = ['id'=>'save-chat']); ?>
                <p class="submit"><input type="submit" name="submit-default" class="button button-primary submit-reset" value="Reset Settings"></p>
              </div>
          </form>
        </div>
      </div>

    </div>



    <?php
}
