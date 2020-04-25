(function ($) {

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
   * Chat sound function
   */
  function ch_chat_sound(){
    var mp3Source = '<source src="' + chatsterDataPublic.sound_file_path + '.mp3" type="audio/mpeg">';
    var oggSource = '<source src="' + chatsterDataPublic.sound_file_path + '.ogg" type="audio/ogg">';
    var embedSource = '<embed hidden="true" autostart="true" loop="false" src="' + chatsterDataPublic.sound_file_path +'.mp3">';
    document.getElementById("sound").innerHTML='<audio id="ch-audio" autoplay="autoplay">' + mp3Source + oggSource + embedSource +'</audio>';
    var chatSound = document.getElementById("ch-audio");
    chatSound.volume = chatsterDataPublic.chat_sound_vol;
  }

  /**
   * Inserts current messages into the conversation
   */
  function insert_messages( new_message, temp_id ) {

    let customer_id = $("#ch-message-board").attr("data-curr_customer_id");
    payload = { new_message: new_message, temp_id: temp_id };

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
        $(this).val('');
        let temp_id = (new Date()).getTime().toString();
        temp_id = temp_id.slice(4, temp_id.length);
        $message = $("<div>", {id: "ch-msg-"+temp_id, "class": "ch-single-message ch-right", "data-author_id": "self" });
        $message.text(message);
        $("#ch-msg-container").append($message);
        insert_messages(message, temp_id);
      }
    }
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
            $message = $("<div>", {id: "ch-msg-" + message.id, "class": is_self });
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

    payload = { last_msg_id: ch_last_msg };

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

  function chat_form() {

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




})(jQuery);
