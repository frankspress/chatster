(function ($) {

  /**
   * It sends a presence ping to the database
   */
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
        },
        error: function(error) {

        },

      } ).done( function ( response ) {

      });
  }
  setInterval(presence, 10000);
  presence();

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
  function build_current_conv(current_conv, conv_id) {
      console.log(current_conv);
    let current_conv_id = $('#ch-message-board').attr('data-conv_id');
    /* If it's not the selected conversation ignore it */
    if ( current_conv && ( current_conv_id == conv_id ) ) {

      let last_msg_id = current_conv[Object.keys(current_conv)[current_conv.length - 1]].id;
      $("#ch-message-board").attr('data-last_msg_id', esc_json(last_msg_id) );
      $.each( current_conv, function( key, message ) {

        if ( $("#message-" + message.temp_id).length ) {
          $( "#message-" + message.temp_id ).attr("id", "message-" + message.id );
        } else {
          let is_self = message.is_author == "1" ? "single-message self" : "single-message";
          $message = $("<div>", {id: "message-" + message.id, "class": is_self });
          $message.html(message.message);
          $("#ch-message-board").append($message);
        }
      });

    }

  }

  /**
   * Retrieves messages for the current conversation
   */
  function long_poll_msg() {

    let ch_last_msg = $("#ch-message-board").attr('data-last_msg_id');
    ch_last_msg = ch_last_msg ? ch_last_msg : 0;

    payload = { last_message: ch_last_msg };

    $.ajax( {
        url: chatsterDataPublic.api_base_url + '/chat/poll',
        method: 'POST',
        beforeSend: function ( xhr ) {
            xhr.setRequestHeader( 'X-WP-Nonce', chatsterDataPublic.nonce );
        },
        data: payload,
        success: function(data) {
          console.log(data);
          //  $('#ch-roller-container').addClass('hidden');
            // $('#ch-message-board').empty();
            // build_current_conv(data.payload.current_conv, ch_current_conv);
            setTimeout( long_poll_msg, 500 );

        },
        error: function(error) {
          //  $('#ch-roller-container').addClass('hidden');
        },

      } ).done( function ( response ) {

      });
  }

  long_poll_msg();


  function long_poll() {

    $.ajax( {

        url: chatsterDataPublic.api_base_url + '/chat/poll/',
        method: 'POST',
        beforeSend: function ( xhr ) {
            xhr.setRequestHeader( 'X-WP-Nonce', chatsterDataPublic.nonce );
        },
        data: {},
        success: function(data) {
          console.log(data);
        },
        error: function(error) {

        },

      } ).done( function ( response ) {

      });

  }

  function chat_insert() {

    $.ajax( {

        url: chatsterDataPublic.api_base_url + '/chat/insert/',
        method: 'POST',
        beforeSend: function ( xhr ) {
            xhr.setRequestHeader( 'X-WP-Nonce', chatsterDataPublic.nonce );
        },
        data: {},
        success: function(data) {
          console.log(data);
        },
        error: function(error) {

        },

      } ).done( function ( response ) {

      });
  }



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

// setInterval(chat_form, 4000);




})(jQuery);
