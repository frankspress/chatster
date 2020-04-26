(function ($) {

  /**
   * It sends a presence ping to the database
   */
  function presence_admin() {
    $.ajax( {

        url: chatsterDataAdmin.api_base_url + '/chat/presence/admin',
        method: 'POST',
        beforeSend: function ( xhr ) {
            xhr.setRequestHeader( 'X-WP-Nonce', chatsterDataAdmin.nonce );
        },
        data: {},
        success: function(data) {
          //console.log(data);
        },
        error: function(error) {

        },

      } ).done( function ( response ) {

      });
  }
  setInterval(presence_admin, 10000);
  presence_admin();

  /**
   * Inserts current messages into the conversation
   */
  function insert_messages( new_message, temp_id ) {

    let customer_id = $("#ch-message-board").attr("data-curr_customer_id");

    payload = { new_message: new_message, msg_link: get_msg_links(), customer_id: customer_id, temp_id: temp_id };

    $.ajax( {
        url: chatsterDataAdmin.api_base_url + '/chat/insert/admin',
        method: 'POST',
        beforeSend: function ( xhr ) {
            xhr.setRequestHeader( 'X-WP-Nonce', chatsterDataAdmin.nonce );
        },
        data: payload,
        success: function(data) {

        },
        error: function(error) {

        },

      } ).done( function ( response ) {

      });
  }
  function get_msg_links() {
    let attachments = $("#ch-attachments div");
    let links = [];
    if ( attachments ) {
      $.each( attachments, function( key, attachment ) {
        let link_id = $(attachment).attr('data-link_id');
        links.push(link_id);
      });
    }
    return links;
  }
  $('#ch-reply').on('keypress', function(e) {
    if ( e.keyCode == 13 && ! e.shiftKey) {
      e.preventDefault();
      var message = $(this).val().trim();
      if (message && ( message.length <= 799 ) ) {
        $(this).val('');
        let temp_id = (new Date()).getTime().toString();
        temp_id = temp_id.slice(4, temp_id.length);
        $message = $("<div>", {id: "message-"+temp_id, "class": "single-message-local self", "data-author_id": "self" });
        $message.text(message);
        $("#ch-message-board").append($message);
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
  function build_convs(convs) {

    if ( convs ) {

       let last_conv_id = convs[Object.keys(convs)[convs.length - 1]].id;
       $("#conversations-block").attr('data-last_conv_id', last_conv_id);

       $.each( convs, function( key, conversation ) {
         let $conversation = $("<div>", {id: "conv-"+conversation.id, "class": "single-conversation", "data-customer_id": conversation.customer_id, "data-single_conv_id": conversation.id });
         $conversation.html('cock');
         $("#conversations-block").append($conversation);
       });

    }



  }
  function build_current_conv(current_conv, conv_id) {

    let current_conv_id = $('#ch-message-board').attr('data-conv_id');
    /* If it's not the selected conversation ignore it */
    if ( current_conv && ( current_conv_id == conv_id ) ) {
      let prev_last_msg_id = $("#ch-message-board").attr('data-last_msg_id');
      let last_msg_id = current_conv[Object.keys(current_conv)[current_conv.length - 1]].id;
      if ( prev_last_msg_id < last_msg_id ) {

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

  }
  $('.single-conversation').live('click',function() {
    $('#ch-message-board').empty();
    let current_conv_id = $(this).attr('data-single_conv_id');
    let current_customer_id = $(this).attr('data-customer_id');
    $('#ch-message-board').attr('data-conv_id', current_conv_id);
    $('#ch-message-board').attr('data-curr_customer_id', current_customer_id);
    $("#ch-message-board").attr('data-last_msg_id', 0);
    get_messages();

  });

  /**
   * Retrieves messages for the current conversation
   */
  function get_messages() {

    let ch_current_conv = $('#ch-message-board').attr('data-conv_id');
    ch_current_conv = ch_current_conv ? ch_current_conv : 0;
    let ch_last_msg = $("#ch-message-board").attr('data-last_msg_id');
    ch_last_msg = ch_last_msg ? ch_last_msg : 0;

    payload = { current_conv: ch_current_conv, last_message: ch_last_msg };

    $.ajax( {
        url: chatsterDataAdmin.api_base_url + '/chat/messages/admin',
        method: 'POST',
        beforeSend: function ( xhr ) {
            xhr.setRequestHeader( 'X-WP-Nonce', chatsterDataAdmin.nonce );
        },
        data: payload,
        success: function(data) {
          //  $('#ch-roller-container').addClass('hidden');

            if ( $('#chatster-chat-switch').prop("checked") ) {
              build_current_conv(data.payload.current_conv, ch_current_conv);
            }

        },
        error: function(error) {
          //  $('#ch-roller-container').addClass('hidden');
        },

      } ).done( function ( response ) {

      });
  }

  /**
   * Long poll with self relaunching technique
   */
  var LongPollRun = false;
  function long_poll() {
    if ( LongPollRun === false ) {

      LongPollRun = true;
      let ch_last_conv = $('#conversations-block').attr('data-last_conv_id');
      ch_last_conv = ch_last_conv ? ch_last_conv : 0;
      let ch_current_conv = $('#ch-message-board').attr('data-conv_id');
      ch_current_conv = ch_current_conv ? ch_current_conv : 0;
      let ch_last_msg = $("#ch-message-board").attr('data-last_msg_id');
      ch_last_msg = ch_last_msg ? ch_last_msg : 0;

      payload = { last_conv: ch_last_conv, current_conv: ch_current_conv, last_message: ch_last_msg };

      $.ajax( {
          url: chatsterDataAdmin.api_base_url + '/chat/polling/admin',
          method: 'POST',
          beforeSend: function ( xhr ) {
              xhr.setRequestHeader( 'X-WP-Nonce', chatsterDataAdmin.nonce );
          },
          data: payload,
          success: function(data) {
              $('#ch-roller-container').addClass('hidden');
              if ( $('#chatster-chat-switch').prop("checked") ) {
                build_convs(data.payload.convs);
                build_current_conv(data.payload.current_conv, ch_current_conv);
                setTimeout( long_poll, 500 );
              }
              LongPollRun = false;

          },
          error: function(error) {
              $('#ch-roller-container').addClass('hidden');
              LongPollRun = false;
              setTimeout( long_poll, 4000 );
          },

        } ).done( function ( response ) {

        });
      }


  }

  /**
   * Changes "Live chat" status and calls a long_poll
   */
  function change_admin_status( admin_status ) {
    $.ajax( {

        url: chatsterDataAdmin.api_base_url + '/chat/is_active/admin',
        method: 'POST',
        beforeSend: function ( xhr ) {
            xhr.setRequestHeader( 'X-WP-Nonce', chatsterDataAdmin.nonce );
        },
        data: { is_active: admin_status },
        success: function(data) {
          if ( admin_status) {
              long_poll();
          }
        },
        error: function(error) {
              $('#ch-roller-container').addClass('hidden');
              $('#chatster-chat-switch').prop("checked", ! admin_status);
        },

      } ).done( function ( response ) {

      });
  }
  $('#chatster-chat-switch').change(function() {
       if( this.checked ) {
          $('#ch-roller-container').removeClass('hidden');
          change_admin_status(true);
       } else {
          change_admin_status(false);
       }
   });
  if ( $('#chatster-chat-switch').prop("checked") ) {

      long_poll();
  }

})(jQuery);
