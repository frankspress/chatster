<?php

if ( ! defined( 'ABSPATH' ) ) exit;


function display_front_chat()  { ?>

  <div id="chatster-container">
    <section id="ch-header">Header HERE
      <div class="ch-arrow">
        <i class="ch-down"></i>
      </div>
    </section>
    <div id="ch-indent-header"></div>
    <section id="ch-chat-section">

      <div id="ch-msg-container" data-conv-id="">

        <div id="ch-msg-" class="ch-align-left ch-msg-wrapper">
          <div class="ch-message">Your Messagefdddd  ddddddddd dddddd dddddddd :D </div>
        </div>

        <div id="ch-msg-" class="ch-align-right ch-msg-wrapper">
          <div class="ch-message">My message :)</div>
        </div>

        <div id="ch-msg-" class="ch-align-left ch-msg-wrapper" >
          <div class="ch-message">Your Messagefdddd  ddddddddd dddddd dddddddd :D </div>
        </div>

        <div id="ch-msg-" class="ch-align-left ch-msg-wrapper" >
            <div class="ch-message">Your Messagefdddd  ddddddddd dddddd dddddddd :D </div>
            <div class="ch-link"></div>
        </div>

        <div id="ch-msg-" class="ch-align-right ch-msg-wrapper" >
            <div class="ch-message">My message :)</div>
        </div>

      </div>


      <div class="ch-input">
        <input type="text" id="ch-reply" value="" placeholder="Your message here..">
      </div>
      <div class="ch-send-btn"> Send</div>
    </section>

    <section id="ch-message-section">
      <div class="ch-input">
        <input type="text" id="ch-customer-name" value="" placeholder="Your name" info="name">
      </div>
      <div class="ch-input">
        <input type="email" id="ch-customer-email" value="" placeholder="Your email" info="email">
      </div>
      <div class="ch-input">
        <textarea id="ch-customer-message" placeholder="Type here your message.." type="text" ></textarea>
      </div>
      <div class="ch-send-btn"> Send</div>
    </section>
  </div>

  <div id="chatster-opener">
    <section id="ch-open-button">Hey HEY HEY HEY</section>
  </div>

<?php
}
