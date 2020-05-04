<?php

if ( ! defined( 'ABSPATH' ) ) exit;


function display_front_chat( $current_conv = false, $chat_available = false )  { ?>

   <div id="chatster-container" class="">
    <section id="ch-header">Header here <div class="ch-arrow"><i class="ch-down"></i></div>
    </section>
    <div id="ch-indent-header"></div>

    <div id="ch-main-conv-container">
      <section id="ch-chat-section" class="hidden" data-is_conv_active="<?php echo $current_conv ? '1' : '0'; ?>">
        <div id="ch-msg-container" data-last_msg_id="">
        </div>
        <div class="ch-input">
          <input type="text" id="ch-reply-public" value="" placeholder="Your message here.." maxlength="799">
        </div>
        <div id="ch-chat-msg" class="ch-send-btn"><?php echo esc_html__( 'Send', CHATSTER_DOMAIN ); ?></div>
      </section>

      <section id="ch-message-section" class="hidden">
        <div class="ch-input">
          <input type="text" id="ch-customer-name" value="" placeholder="Your name" info="name">
        </div>
        <div class="ch-input">
          <input type="email" id="ch-customer-email" value="" placeholder="Your email" info="email">
        </div>
        <div class="ch-input">
          <textarea id="ch-customer-message" placeholder="Type here your message.." type="text" ></textarea>
        </div>
        <div class="ch-send-btn"><?php echo esc_html__( 'Send', CHATSTER_DOMAIN ); ?></div>
      </section>

      <section id="ch-chat-form" class="hidden">
        <div class="ch-input">
          <input type="text" id="ch-customer-name" value="" placeholder="Your name" info="name">
        </div>
        <div class="ch-input">
          <input type="email" id="ch-customer-email" value="" placeholder="Your email" info="email">
        </div>
        <div class="ch-input">
          <textarea id="ch-customer-message" placeholder="Type here your message.." type="text" ></textarea>
        </div>
        <div class="ch-send-btn"><?php echo esc_html__( 'Send', CHATSTER_DOMAIN ); ?></div>
      </section>

      <section id="ch-chat-select">
        <div id="ch-select-container">
            <div id="ch-btn-chat" class="<?php echo ! $chat_available ? 'ch-unavailable' : ''; ?>">Chat</div>
            <div id="ch-btn-request">Send a message</div>
        </div>
      </section>

    </div>

    <div id="sound"></div>

  </div>

  <div id="chatster-opener">
    <section id="ch-open-button">
      <div>Chat</div>
    </section>
  </div>


<?php
}
