(function ($) {
"use strict";
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
    let conv_id = $("#ch-message-board").attr("data-conv_id");

    var payload = { new_message: new_message, msg_link: get_msg_links(), conv_id: conv_id, customer_id: customer_id, temp_id: temp_id };

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
  function get_attachment_objs() {

    let attachments = $(".ch-product-auto");
    let attachment_cont = [];
    if ( attachments ) {

      $.each( attachments, function( key, attachment ) {
        let found_attachment = {};
        found_attachment['id'] = $(attachment).attr('data-link_id');
        found_attachment['thumbnail'] = $(attachment).find('img').attr('src');
        found_attachment['title'] = $(attachment).find('.ch-auto-title').text();
        found_attachment['excerpt'] = $(attachment).find('.ch-auto-excerpt').text();
        found_attachment['link'] = $(attachment).find('.ch-auto-exlink a').attr('href');
        attachment_cont.push(found_attachment);
      });
      return attachment_cont;
    }
    return false;
  }
  function get_msg_links() {
    let attachments = $(".ch-product-auto");
    let links = [];
    if ( attachments ) {
      $.each( attachments, function( key, attachment ) {
        let link_id = $(attachment).attr('data-link_id');
        links.push(link_id);
      });
    }
    $("#ch-attachments div").slideUp( 300, function() {$("#ch-attachments div").remove();});
    return links;
  }
  $('#ch-reply').on('keypress', function(e) {
    if ( e.keyCode == 13 && ! e.shiftKey ) {
      e.preventDefault();
      var message = $(this).val().trim();
      if (message && ( message.length <= 799 ) ) {
        $(this).attr('rows', 1 ).val('');
        let temp_id = (new Date()).getTime().toString();
        temp_id = parseInt(temp_id.slice(4, temp_id.length));
        // Create elements
        let $message_cont = $("<div>", {id: "message-"+temp_id, "class": "single-message-local self", "data-author_id": "self" });
        let $message_text = $("<div>", {"class": "ch-msg-text"});
        let $message_links = $("<div>", {"class": "ch-link-cont"});
        // Populate elements
        $message_text.text(message);
        $message_links.html(msg_link_template(get_attachment_objs()));
        // Append elements
        $message_cont.append($message_text);
        $message_cont.append($message_links);
        // Append Message Block
        $("#ch-message-board").append($message_cont);
        // Insert Message - Ajax Call
        insert_messages(message, temp_id);
      }
    }
  });
  // Textarea rows controller
  $('#ch-reply').on('keydown', function(e) {
    if ( e.keyCode == 13 && e.shiftKey ) {
      let rows = $(this).attr('rows');
      $(this).attr('rows', parseInt(rows) + 1 );
    }
    else if ( e.keyCode == 8 ) {
      let lines = $(this).val().split(/\r*\n/).length;
      let rows = parseInt(lines) - 1;
      rows = rows >= 1 ? rows : 1;
      $(this).attr('rows', rows);
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
         // TODO
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
            let $message_cont = $("<div>", {id: "message-" + message.id, "class": is_self });
            let $message_text = $("<div>", {"class": "ch-msg-text"});
            let $message_links = $("<div>", {"class": "ch-link-cont"});
            $message_text.html(message.message);
            $message_links.html(msg_link_template(message.product_ids));
            $message_cont.append($message_text);
            $message_cont.append($message_links);
            $("#ch-message-board").append($message_cont);
          }
        });
      }

    }

  }
  function msg_link_template(links) {

    if ( links ) {
      var template = '';
      $.each( links, function( key, attachment ) {
        let thumbnail = attachment.thumbnail ? attachment.thumbnail : chatsterDataAdmin.no_image_link;
        template += '<div class="ch-link-chat" data-link_id="' + attachment.id + '">';
        template += ' <div class="ch-link-img">';
        template +=    '<img src="' + thumbnail + '" alt="product or page" height="32" width="32">';
        template += ' </div>';
        template += ' <div class="ch-link-descr">';
        template +=     '<div class="ch-link-title">' + attachment.title + '</div>';
        template +=     '<div class="ch-link-excerpt">' + attachment.excerpt + '</div>';
        template += ' </div>';
        template += ' <div class="ch-link-exlink"><a href="' + attachment.link + '"  target="_blank">Open</a></div>';
        template += '</div>';
      });
      return template;
    }
    return '';
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

    var payload = { current_conv: ch_current_conv, last_message: ch_last_msg };

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
      var ch_conv_list = $(".single-conversation").map(function(){return $(this).attr("data-single_conv_id");}).get();
      ch_conv_list = ch_conv_list ? ch_conv_list : 0;
      var payload = { last_conv: ch_last_conv, current_conv: ch_current_conv, last_message: ch_last_msg, conv_ids: ch_conv_list };

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
