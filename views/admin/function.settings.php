<?php

if ( ! defined( 'ABSPATH' ) ) exit;

function display_admin_settings() {

    if ( ! current_user_can( 'manage_options' ) ) return; ?>

    <div id="ch-options-main-container">

      <div class="ch-option-block" style="border: 1px solid #CCCCCC">
        <div class="ch-option-title"><?php esc_html_e('Bot Setup', CHATSTER_DOMAIN); ?></div>
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

      <div class="ch-option-block" style="border: 1px solid #CCCCCC">
        <div class="ch-option-title"><?php esc_html_e('Bot Q &amp; A', CHATSTER_DOMAIN); ?></div>
        <div id="bot-q-and-a" class="ch-option-container" style="display:none;">
          <div id="q-and-a-container"></div>
          <form id="chatster-bot-and-a-form" action="" method="post">
          <?php ?>
            <div id="q-and-a-input">
            <?php
              do_ch_settings_section( 'chatster-menu' , 'ch_bot_qa_section'); ?>
              <div class="ch-reply-btn" style="display:flex;">
                <input type="submit" name="submit-settings" id="save-bot-q-and-a" class="button button-primary custom-class" value="Save Response">
                <div class="ch-smaller-loader hidden" style="margin-left:20px;"></div>
              </div>
            </div>
          </form>
        </div>
      </div>

      <div class="ch-option-block"  style="border: 1px solid #CCCCCC">
        <div class="ch-option-title"><?php esc_html_e('Chat Configuration', CHATSTER_DOMAIN); ?></div>
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

      <div class="ch-option-block"  style="border: 1px solid #CCCCCC">
        <div class="ch-option-title"><?php esc_html_e('Request&#47;Response Configuration', CHATSTER_DOMAIN); ?></div>
        <div id="request-options" class="ch-option-container" style="display:none;">
          <form id="chatster-request-options-form" action="options.php#request-options" method="post">
          <?php
              settings_errors('chatster_request_options');
              settings_fields( 'chatster_request_options' );
              do_ch_settings_section( 'chatster-menu', 'ch_request_section' ); ?>
              <div class="ch-reply-btn">
                <?php submit_button($text = null, $type = 'primary', $name = 'submit-settings',$wrap = true, $other_attributes = ['id'=>'save-request']); ?>
                <p class="submit"><input type="submit" name="submit-default" class="button button-primary submit-reset" value="Reset Settings"></p>
              </div>
          </form>
        </div>
      </div>

    </div>



    <?php
}
