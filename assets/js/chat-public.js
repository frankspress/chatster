(function ($) {
"use strict";
  /**
   * It sends a presence ping to the database
   */
  var presence_set = false;
  function presence() {
    $.ajax( {

        url: chatsterDataPublic.api_base_url + '/chat/presence/customer',
        method: 'POST',
        beforeSend: function ( xhr ) {
            xhr.setRequestHeader( 'X-WP-Nonce', chatsterDataPublic.nonce );
        },
        data: {},
        success: function(data) {
          console.log(data);
          presence_set = true;
        },
        error: function(error) {

        },

      } ).done( function ( response ) {

      });
  }
  setInterval(presence, 10000);
  presence();

  /**
   * User Utility fn
   */
  function ch_chat_sound(){
    var mp3Source = '<source src="' + chatsterDataPublic.sound_file_path + '.mp3" type="audio/mpeg">';
    var oggSource = '<source src="' + chatsterDataPublic.sound_file_path + '.ogg" type="audio/ogg">';
    var embedSource = '<embed hidden="true" autostart="true" loop="false" src="' + chatsterDataPublic.sound_file_path +'.mp3">';
    document.getElementById("sound").innerHTML='<audio id="ch-audio" autoplay="autoplay">' + mp3Source + oggSource + embedSource +'</audio>';
    var chatSound = document.getElementById("ch-audio");
    chatSound.volume = chatsterDataPublic.chat_sound_vol;
  }
  function scrollTopChat() {
    $("#ch-msg-container").animate({ scrollTop: $('#ch-msg-container').prop("scrollHeight")}, 400);
  }
  function ch_open_chat() {
    $('#chatster-opener').animate({
      right: '-10%'
    });
    $('#chatster-container').animate({
      bottom: '15px'
    });
  }
  function ch_close_chat() {
    $('#chatster-opener').animate({
      right: '2%'
    });
    $('#chatster-container').animate({
      bottom: '-650px'
    });
  }
  $('#chatster-opener').on('click', ch_open_chat );
  $('.ch-arrow').on('click', ch_close_chat);

  /**
   * Simplified Cookie fn
   */
  function setCookie(name, value, days) {
      if (days) {
          var date = new Date();
          date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
          var expires = "; expires=" + date.toGMTString();
      }
      else var expires = "";

      document.cookie = name + "=" + value + expires + "; path=/";
  }
  function getCookie(name) {
      var nameEQ = name + "=";
      var ca = document.cookie.split(';');
      for (var i = 0; i < ca.length; i++) {
          var c = ca[i];
          while (c.charAt(0) == ' ') c = c.substring(1, c.length);
          if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
      }
      return null;
  }
  function deleteCookie(name) {
      createCookie(name, "", -1);
  }

  /**
   * Inserts current messages into the conversation
   */
  function insert_messages( new_message, temp_id ) {

    let customer_id = $("#ch-message-board").attr("data-curr_customer_id");
    var payload = { new_message: new_message, temp_id: temp_id };

    $.ajax( {
        url: chatsterDataPublic.api_base_url + '/chat/insert',
        method: 'POST',
        beforeSend: function ( xhr ) {
            xhr.setRequestHeader( 'X-WP-Nonce', chatsterDataPublic.nonce );
        },
        data: payload,
        success: function(data) {

        },
        error: function(error) {

        },

      } ).done( function ( response ) {

      });
  }
  $('#ch-reply-public').on('keypress', function(e) {

    if ( e.keyCode == 13 && ! e.shiftKey) {
      e.preventDefault();
      var message = $(this).val().trim();
      if (message && ( message.length <= 799 ) ) {
        scrollTopChat();
        $(this).val('');
        let temp_id = (new Date()).getTime().toString();
        temp_id = temp_id.slice(4, temp_id.length);
        let $message = $("<div>", {id: "ch-msg-"+temp_id, "class": "ch-single-message ch-right", "data-author_id": "self" });
        $message.text(message);
        $("#ch-msg-container").append($message);
        insert_messages(message, temp_id);
      }
    }
  });
  $('#ch-chat-msg').on('click', function() {
    var press = jQuery.Event("keypress");
    press.ctrlKey = false;
    press.which = 13;
    press.keyCode = 13;
    $('#ch-reply-public').trigger(press);
  });

  /**
   * Ends current conversation
   */
  function disconnect_chat() {

   var payload = {};

   $.ajax( {
       url: chatsterDataPublic.api_base_url + '/chat/disconnect',
       method: 'POST',
       beforeSend: function ( xhr ) {
           xhr.setRequestHeader( 'X-WP-Nonce', chatsterDataPublic.nonce );
       },
       data: payload,
       success: function(data) {
         console.log(data);
         $('#ch-chat-section').css('background-color','#FAFAFA');
         $('#ch-reply-public').attr('disabled',true);
       },
       error: function(error) {

       },

     } ).done( function ( response ) {

     });
  }
  $('#ch-end-chat').on('click', function() {
    disconnect_chat();
  });

  /**
   * Accessory functions to build the conversation list and current convs
   */
  function esc_json(str) {
     return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }
  function build_current_conv(current_conv) {

    if ( current_conv ) {
        let current_msg_id = $("#ch-msg-container").attr('data-last_msg_id');
        let last_msg_id = current_conv[Object.keys(current_conv)[current_conv.length - 1]].id;
        $("#ch-msg-container").attr('data-last_msg_id', esc_json(last_msg_id) );
        $.each( current_conv, function( key, message ) {

          if ( $("#ch-msg-" + message.temp_id).length ) {
            $( "#ch-msg-" + message.temp_id ).attr("id", "ch-msg-" + message.id );
          } else {
            let is_self = message.is_author == "1" ? "ch-single-message ch-right" : "single-message";
            let $message = $("<div>", {id: "ch-msg-" + message.id, "class": is_self });
            $message.html(message.message);
            $("#ch-msg-container").append($message);
            if ( current_msg_id ) {
              ch_chat_sound();
            }
          }
        });
     }
  }

  /**
   * Retrieves messages for the open conversation
   */
  function long_poll_msg() {

    let ch_last_msg = $("#ch-msg-container").attr('data-last_msg_id');
    ch_last_msg = ch_last_msg ? ch_last_msg : 0;

    var payload = { last_msg_id: ch_last_msg };

    if ( presence_set ) {
      $.ajax( {
          url: chatsterDataPublic.api_base_url + '/chat/poll',
          method: 'POST',
          beforeSend: function ( xhr ) {
              xhr.setRequestHeader( 'X-WP-Nonce', chatsterDataPublic.nonce );
          },
          data: payload,
          success: function(data) {
            if ( data.payload ) {
              build_current_conv(data.payload);
              scrollTopChat();
              $("#ch-msg-container").find('.ch-small-loader').hide();
            }
            setTimeout( long_poll_msg, 500 );

          },
          error: function(error) {
             setTimeout( long_poll_msg, 500 );
          },

        } ).done( function ( response ) {

        });
      } else {
        setTimeout( long_poll_msg, 500 );
      }
  }
  long_poll_msg();

  /**
   * Adds Live chat initial form / Sends request questions (Offline)
   */
  function request_form() {

   $.ajax( {

       url: chatsterDataPublic.api_base_url + '/chat/form-data',
       method: 'POST',
       beforeSend: function ( xhr ) {
           xhr.setRequestHeader( 'X-WP-Nonce', chatsterDataPublic.nonce );
       },
       data: {customer_name: 'meeee', chat_subject: 'testing... ee'},
       success: function(data) {
         console.log(data);
       },
       error: function(error) {

       },

     } ).done( function ( response ) {

     });
 }

  /**
   * Chat Select Options
   */
  $('#ch-btn-chat').on('click', function(e) {
    if ( $(this).hasClass('ch-unavailable') ) {
        return;
    }

    $("#ch-chat-select").animate({
      top: '600px'
    });
    $('#ch-chat-form').slideDown(300);


  });
  $('#ch-btn-request').on('click', function(e) {


  });
  $("#ch-start-chat-form").submit(function(e){
      e.preventDefault();
      let c_name = $(this).find('#ch-customer-name').val();
      let c_email =  $(this).find('#ch-customer-email').val();
      let c_question =  $(this).find('#ch-customer-question').val();
      //queue_chat_poll();
  });
})(jQuery);
